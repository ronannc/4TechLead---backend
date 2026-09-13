<?php

use App\Enums\DeliveryMilestoneType;
use App\Enums\DeliveryParticipantRole;
use App\Models\DeliveryCase;
use App\Models\DeliveryCaseExternalLink;
use App\Models\DeliveryCaseMilestone;
use App\Models\DeliveryCaseParticipant;
use App\Models\IntegrationSystem;
use App\Models\IntegrationWebhookEvent;
use App\Models\Person;
use App\Models\PersonExternalIdentity;
use App\Models\Tenant;
use App\Services\GitHubDeliveryCaseProjector;

it('projects GitHub PR, review, workflow and deployment evidence into one delivery case', function (): void {
    $tenant = Tenant::factory()->create();
    $integration = IntegrationSystem::factory()->create([
        'tenant_id' => $tenant->id,
        'provider' => 'github',
    ]);
    $author = Person::factory()->create(['tenant_id' => $tenant->id]);
    $reviewer = Person::factory()->create(['tenant_id' => $tenant->id]);
    PersonExternalIdentity::factory()->create([
        'tenant_id' => $tenant->id,
        'integration_system_id' => $integration->id,
        'person_id' => $author->id,
        'external_code' => 'github_user:ada',
    ]);
    PersonExternalIdentity::factory()->create([
        'tenant_id' => $tenant->id,
        'integration_system_id' => $integration->id,
        'person_id' => $reviewer->id,
        'external_code' => 'github_user:grace',
    ]);

    $events = [
        githubEvent($tenant, $integration, 'pr-opened', [
            'event_type' => 'pull_request.opened',
            'pr_id' => 101,
            'pr_number' => 7,
            'pr_title' => 'Implementa entrega',
            'pr_author' => 'ada',
            'head_sha' => 'abc123',
            'task_refs' => ['DRIE-23000'],
            'occurred_at' => '2026-09-13T10:00:00Z',
        ]),
        githubEvent($tenant, $integration, 'review', [
            'event_type' => 'pull_request_review.submitted',
            'pr_id' => 101,
            'pr_number' => 7,
            'pr_author' => 'ada',
            'review_id' => 91,
            'review_state' => 'approved',
            'external_actor_code' => 'github_user:grace',
            'head_sha' => 'abc123',
            'task_refs' => ['DRIE-23000'],
            'occurred_at' => '2026-09-13T11:00:00Z',
        ]),
        githubEvent($tenant, $integration, 'workflow', [
            'event_type' => 'workflow_run.completed',
            'workflow_run_id' => 501,
            'workflow_run_attempt' => 2,
            'workflow_run_conclusion' => 'success',
            'workflow_check_suite_id' => 777,
            'head_sha' => 'abc123',
            'task_refs' => ['DRIE-23000'],
            'occurred_at' => '2026-09-13T12:00:00Z',
        ]),
        githubEvent($tenant, $integration, 'deployment', [
            'event_type' => 'deployment_status.created',
            'deployment_id' => 44,
            'deployment_status_id' => 45,
            'deployment_status_state' => 'success',
            'deployment_environment' => 'production',
            'head_sha' => 'abc123',
            'task_refs' => ['DRIE-23000'],
            'occurred_at' => '2026-09-13T13:00:00Z',
        ]),
    ];

    foreach ($events as $event) {
        app(GitHubDeliveryCaseProjector::class)->project($event);
    }

    $deliveryCase = DeliveryCase::query()->sole();

    expect($deliveryCase->task_ref)->toBe('DRIE-23000')
        ->and($deliveryCase->title)->toBe('Implementa entrega')
        ->and(DeliveryCaseExternalLink::query()->where('delivery_case_id', $deliveryCase->id)->count())->toBe(4)
        ->and(DeliveryCaseMilestone::query()->where('delivery_case_id', $deliveryCase->id)
            ->where('milestone_type', DeliveryMilestoneType::PullRequestOpened)->count())->toBe(1)
        ->and(DeliveryCaseMilestone::query()->where('delivery_case_id', $deliveryCase->id)
            ->where('milestone_type', DeliveryMilestoneType::ReviewSubmitted)->count())->toBe(1)
        ->and(DeliveryCaseMilestone::query()->where('delivery_case_id', $deliveryCase->id)
            ->where('milestone_type', DeliveryMilestoneType::CiCompleted)->count())->toBe(1)
        ->and(DeliveryCaseMilestone::query()->where('delivery_case_id', $deliveryCase->id)
            ->where('milestone_type', DeliveryMilestoneType::DeploymentCompleted)->count())->toBe(1)
        ->and(DeliveryCaseParticipant::query()->where('delivery_case_id', $deliveryCase->id)
            ->where('person_id', $author->id)->where('role', DeliveryParticipantRole::CodeAuthor)->count())->toBe(1)
        ->and(DeliveryCaseParticipant::query()->where('delivery_case_id', $deliveryCase->id)
            ->where('person_id', $reviewer->id)->where('role', DeliveryParticipantRole::Reviewer)->count())->toBe(1);
});

it('uses workflow as CI source of truth and refuses ambiguous task references', function (): void {
    $tenant = Tenant::factory()->create();
    $integration = IntegrationSystem::factory()->create([
        'tenant_id' => $tenant->id,
        'provider' => 'github-actions',
    ]);
    $workflow = githubEvent($tenant, $integration, 'workflow-canonical', [
        'event_type' => 'workflow_run.completed',
        'workflow_run_id' => 901,
        'workflow_run_attempt' => 1,
        'workflow_check_suite_id' => 444,
        'task_refs' => ['DRIE-23001'],
    ]);
    $checkSuite = githubEvent($tenant, $integration, 'suite-fallback', [
        'event_type' => 'check_suite.completed',
        'check_suite_id' => 444,
        'task_refs' => ['DRIE-23001'],
    ]);
    $ambiguous = githubEvent($tenant, $integration, 'ambiguous', [
        'event_type' => 'pull_request.opened',
        'task_refs' => ['DRIE-23002', 'DRIE-23003'],
        'task_ref_ambiguous' => true,
    ]);

    app(GitHubDeliveryCaseProjector::class)->project($workflow);
    app(GitHubDeliveryCaseProjector::class)->project($checkSuite);

    expect(app(GitHubDeliveryCaseProjector::class)->project($ambiguous))->toBeNull()
        ->and(DeliveryCase::query()->count())->toBe(1)
        ->and(DeliveryCaseMilestone::query()->where('milestone_type', DeliveryMilestoneType::CiCompleted)->count())->toBe(1);
});

/**
 * @param  array<string, mixed>  $payload
 */
function githubEvent(Tenant $tenant, IntegrationSystem $integration, string $eventId, array $payload): IntegrationWebhookEvent
{
    return IntegrationWebhookEvent::factory()->create([
        'tenant_id' => $tenant->id,
        'integration_system_id' => $integration->id,
        'person_id' => null,
        'event_id' => $eventId,
        'event_type' => $payload['event_type'] ?? 'github',
        'normalized_payload' => array_merge([
            'source' => 'github',
            'repository_id' => 99,
            'repository_full_name' => 'org/repo',
            'task_refs' => [],
            'task_ref_ambiguous' => false,
            'occurred_at' => '2026-09-13T09:00:00Z',
        ], $payload),
    ]);
}
