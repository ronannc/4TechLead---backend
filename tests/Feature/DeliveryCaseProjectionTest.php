<?php

use App\Enums\DeliveryAssociationConfidence;
use App\Enums\DeliveryExternalLinkType;
use App\Enums\DeliveryMilestoneType;
use App\Enums\DeliveryParticipantRole;
use App\Enums\DeliveryStage;
use App\Models\DeliveryCase;
use App\Models\DeliveryCaseExternalLink;
use App\Models\DeliveryCaseMilestone;
use App\Models\DeliveryCaseParticipant;
use App\Models\IntegrationSystem;
use App\Models\IntegrationWebhookEvent;
use App\Models\Person;
use App\Models\PersonExternalIdentity;
use App\Models\Team;
use App\Models\Tenant;
use App\Services\DeliveryCaseProjector;
use Illuminate\Support\Carbon;

it('projects the ClickUp delivery timeline without attributing the event actor as implementer', function (): void {
    $tenant = Tenant::factory()->create();
    $team = Team::factory()->create(['tenant_id' => $tenant->id]);
    $integration = IntegrationSystem::factory()->create([
        'tenant_id' => $tenant->id,
        'provider' => 'clickup',
    ]);
    $developer = Person::factory()->create([
        'tenant_id' => $tenant->id,
        'team_id' => $team->id,
        'clickup_user_id' => 'developer-1',
    ]);
    $qa = Person::factory()->create([
        'tenant_id' => $tenant->id,
        'team_id' => $team->id,
        'clickup_user_id' => 'qa-1',
    ]);
    $secondDeveloper = Person::factory()->create([
        'tenant_id' => $tenant->id,
        'team_id' => $team->id,
        'clickup_user_id' => 'developer-2',
    ]);
    $actor = Person::factory()->create([
        'tenant_id' => $tenant->id,
        'team_id' => $team->id,
        'clickup_user_id' => 'actor-1',
    ]);

    foreach ([
        'developer-1' => $developer,
        'developer-2' => $secondDeveloper,
        'qa-1' => $qa,
        'actor-1' => $actor,
    ] as $externalId => $person) {
        PersonExternalIdentity::factory()->create([
            'tenant_id' => $tenant->id,
            'integration_system_id' => $integration->id,
            'person_id' => $person->id,
            'external_code' => 'clickup_user:'.$externalId,
        ]);
    }

    $developmentStarted = clickUpProjectionEvent(
        $integration,
        'doing-1',
        '2026-09-01T09:00:00Z',
        DeliveryStage::InProgress,
        ['developer-1', 'developer-2'],
        personId: $actor->id,
    );
    clickUpProjectionEvent(
        $integration,
        'blocked-1',
        '2026-09-02T09:00:00Z',
        DeliveryStage::Blocked,
        ['developer-1'],
    );
    clickUpProjectionEvent(
        $integration,
        'ready-qa-1',
        '2026-09-03T09:00:00Z',
        DeliveryStage::ReadyForQa,
        ['developer-1'],
    );
    clickUpProjectionEvent(
        $integration,
        'qa-assigned-before-status-change',
        '2026-09-03T10:00:00Z',
        DeliveryStage::ReadyForQa,
        ['developer-1', 'qa-1'],
    );
    clickUpProjectionEvent(
        $integration,
        'in-qa-1',
        '2026-09-04T09:00:00Z',
        DeliveryStage::InQa,
        ['developer-1', 'qa-1'],
    );
    clickUpProjectionEvent(
        $integration,
        'qa-failed-1',
        '2026-09-05T09:00:00Z',
        DeliveryStage::QaFailed,
        ['developer-1', 'qa-1'],
    );
    clickUpProjectionEvent(
        $integration,
        'doing-2',
        '2026-09-06T09:00:00Z',
        DeliveryStage::InProgress,
        ['developer-1', 'qa-1'],
    );
    clickUpProjectionEvent(
        $integration,
        'ready-publish-1',
        '2026-09-07T09:00:00Z',
        DeliveryStage::ReadyToPublish,
        ['developer-1', 'qa-1'],
        taskTags: ['HOMOLOG', 'MERGIADO'],
    );
    $published = clickUpProjectionEvent(
        $integration,
        'published-1',
        '2026-09-08T09:00:00Z',
        DeliveryStage::Published,
        ['developer-1', 'qa-1'],
    );

    $deliveryCase = app(DeliveryCaseProjector::class)->project($published);

    expect($deliveryCase)->not->toBeNull()
        ->and($deliveryCase->task_ref)->toBe('DRIE-21919')
        ->and($deliveryCase->title)->toBe('Cadastro de oficina')
        ->and($deliveryCase->current_stage)->toBe(DeliveryStage::Published)
        ->and($deliveryCase->story_points)->toBe('5.00')
        ->and($deliveryCase->sprint_ref)->toBe('sprint-42')
        ->and($deliveryCase->first_seen_at->equalTo(Carbon::parse('2026-09-01T09:00:00Z')))->toBeTrue()
        ->and($deliveryCase->completed_at->equalTo(Carbon::parse('2026-09-08T09:00:00Z')))->toBeTrue()
        ->and($deliveryCase->canceled_at)->toBeNull();

    $link = DeliveryCaseExternalLink::query()->sole();

    expect($link->link_type)->toBe(DeliveryExternalLinkType::ClickUpTask)
        ->and($link->external_id)->toBe('clickup-task-1')
        ->and($link->delivery_case_id)->toBe($deliveryCase->id);

    $milestoneTypes = DeliveryCaseMilestone::query()
        ->where('delivery_case_id', $deliveryCase->id)
        ->orderBy('occurred_at')
        ->orderBy('id')
        ->pluck('milestone_type');

    expect($milestoneTypes)->toContain(
        DeliveryMilestoneType::DevelopmentStarted,
        DeliveryMilestoneType::Blocked,
        DeliveryMilestoneType::Unblocked,
        DeliveryMilestoneType::ImplementationCompleted,
        DeliveryMilestoneType::QaStarted,
        DeliveryMilestoneType::QaFailed,
        DeliveryMilestoneType::QaApproved,
        DeliveryMilestoneType::ReadyToPublish,
        DeliveryMilestoneType::Published,
        DeliveryMilestoneType::Homologated,
        DeliveryMilestoneType::PullRequestMerged,
    );

    $participants = DeliveryCaseParticipant::query()
        ->where('delivery_case_id', $deliveryCase->id)
        ->get();

    expect($participants)->toHaveCount(3)
        ->and($participants->firstWhere('person_id', $developer->id)?->role)
        ->toBe(DeliveryParticipantRole::Implementer)
        ->and($participants->firstWhere('person_id', $developer->id)?->confidence)
        ->toBe(DeliveryAssociationConfidence::High)
        ->and($participants->firstWhere('person_id', $qa->id)?->role)
        ->toBe(DeliveryParticipantRole::Qa)
        ->and($participants->firstWhere('person_id', $qa->id)?->confidence)
        ->toBe(DeliveryAssociationConfidence::Medium)
        ->and($participants->firstWhere('person_id', $secondDeveloper->id)?->role)
        ->toBe(DeliveryParticipantRole::Implementer)
        ->and($participants->firstWhere('person_id', $secondDeveloper->id)?->valid_to?->equalTo(
            Carbon::parse('2026-09-02T09:00:00Z'),
        ))->toBeTrue()
        ->and($participants->contains('person_id', $actor->id))->toBeFalse()
        ->and($developmentStarted->person_id)->toBe($actor->id);

    expect(DeliveryCaseMilestone::query()
        ->where('milestone_type', DeliveryMilestoneType::Homologated)
        ->value('metadata'))->toMatchArray(['evidence_role' => 'confirmation'])
        ->and(DeliveryCaseMilestone::query()
            ->where('milestone_type', DeliveryMilestoneType::PullRequestMerged)
            ->value('metadata'))->toMatchArray(['evidence_role' => 'confirmation']);

    $participantIds = DeliveryCaseParticipant::query()->orderBy('id')->pluck('id')->all();
    $milestoneIds = DeliveryCaseMilestone::query()->orderBy('id')->pluck('id')->all();

    app(DeliveryCaseProjector::class)->project($published);

    expect(DeliveryCaseParticipant::query()->orderBy('id')->pluck('id')->all())->toBe($participantIds)
        ->and(DeliveryCaseMilestone::query()->orderBy('id')->pluck('id')->all())->toBe($milestoneIds);
});

it('rebuilds deterministically when an older ClickUp event arrives after publication', function (): void {
    $tenant = Tenant::factory()->create();
    $integration = IntegrationSystem::factory()->create([
        'tenant_id' => $tenant->id,
        'provider' => 'clickup',
    ]);
    $published = clickUpProjectionEvent(
        $integration,
        'published-first',
        '2026-09-08T09:00:00Z',
        DeliveryStage::Published,
        [],
    );
    $projector = app(DeliveryCaseProjector::class);

    $projector->project($published);

    $lateOlderEvent = clickUpProjectionEvent(
        $integration,
        'qa-failed-late',
        '2026-09-05T09:00:00Z',
        DeliveryStage::QaFailed,
        [],
    );
    $deliveryCase = $projector->project($lateOlderEvent);
    $milestoneCount = DeliveryCaseMilestone::query()->count();

    $deliveryCase = $projector->project($lateOlderEvent);

    expect(DeliveryCase::query()->count())->toBe(1)
        ->and($deliveryCase)->not->toBeNull()
        ->and($deliveryCase->current_stage)->toBe(DeliveryStage::Published)
        ->and($deliveryCase->completed_at->equalTo(Carbon::parse('2026-09-08T09:00:00Z')))->toBeTrue()
        ->and(DeliveryCaseMilestone::query()->count())->toBe($milestoneCount)
        ->and(DeliveryCaseMilestone::query()->pluck('semantic_key')->unique())
        ->toHaveCount($milestoneCount)
        ->and(DeliveryCaseMilestone::query()->pluck('milestone_type'))
        ->toContain(DeliveryMilestoneType::QaFailed, DeliveryMilestoneType::Published);
});

it('treats rejected as cancellation and not as completed delivery', function (): void {
    $tenant = Tenant::factory()->create();
    $integration = IntegrationSystem::factory()->create([
        'tenant_id' => $tenant->id,
        'provider' => 'clickup',
    ]);
    $rejected = clickUpProjectionEvent(
        $integration,
        'rejected-1',
        '2026-09-09T09:00:00Z',
        DeliveryStage::Rejected,
        [],
        taskRef: 'DRIE-22000',
    );

    $deliveryCase = app(DeliveryCaseProjector::class)->project($rejected);

    expect($deliveryCase)->not->toBeNull()
        ->and($deliveryCase->current_stage)->toBe(DeliveryStage::Rejected)
        ->and($deliveryCase->completed_at)->toBeNull()
        ->and($deliveryCase->canceled_at->equalTo(Carbon::parse('2026-09-09T09:00:00Z')))->toBeTrue()
        ->and($deliveryCase->milestones->pluck('milestone_type'))
        ->toContain(DeliveryMilestoneType::Rejected)
        ->not->toContain(DeliveryMilestoneType::Published);
});

it('ignores events without a canonical ClickUp task reference', function (): void {
    $tenant = Tenant::factory()->create();
    $integration = IntegrationSystem::factory()->create([
        'tenant_id' => $tenant->id,
        'provider' => 'clickup',
    ]);
    $event = IntegrationWebhookEvent::factory()->create([
        'tenant_id' => $tenant->id,
        'integration_system_id' => $integration->id,
        'person_id' => null,
        'event_id' => 'missing-task-ref',
        'event_type' => 'taskStatusUpdated',
        'normalized_payload' => [
            'source' => 'clickup',
            'task_id' => 'clickup-task-without-ref',
            'task_refs' => [],
            'task_stage' => DeliveryStage::InProgress->value,
            'occurred_at' => '2026-09-01T09:00:00Z',
        ],
        'received_at' => '2026-09-01T09:00:01Z',
    ]);

    expect(app(DeliveryCaseProjector::class)->project($event))->toBeNull()
        ->and(DeliveryCase::query()->count())->toBe(0);
});

/**
 * @param  array<int, string>  $assigneeIds
 */
function clickUpProjectionEvent(
    IntegrationSystem $integration,
    string $eventId,
    string $occurredAt,
    DeliveryStage $stage,
    array $assigneeIds,
    ?int $personId = null,
    string $taskRef = 'DRIE-21919',
    array $taskTags = [],
): IntegrationWebhookEvent {
    return IntegrationWebhookEvent::factory()->create([
        'tenant_id' => $integration->tenant_id,
        'integration_system_id' => $integration->id,
        'person_id' => $personId,
        'event_id' => $eventId,
        'event_type' => 'taskStatusUpdated',
        'normalized_payload' => [
            'source' => 'clickup',
            'task_id' => 'clickup-task-1',
            'task_custom_id' => $taskRef,
            'task_refs' => [$taskRef],
            'task_name' => 'Cadastro de oficina',
            'task_stage' => $stage->value,
            'task_status' => $stage->value,
            'task_sprint_points' => 5,
            'list_ids' => ['sprint-42'],
            'task_url' => 'https://app.clickup.com/t/clickup-task-1',
            'task_assignees' => array_map(
                static fn (string $id): array => [
                    'external_code' => 'clickup_user:'.$id,
                    'id' => $id,
                ],
                $assigneeIds,
            ),
            'task_tags' => $taskTags,
            'occurred_at' => $occurredAt,
        ],
        'received_at' => Carbon::parse($occurredAt)->addSecond(),
    ]);
}
