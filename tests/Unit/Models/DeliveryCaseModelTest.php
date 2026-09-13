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
use App\Models\Team;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\QueryException;

it('identifies a delivery by tenant and task reference', function () {
    $tenantA = Tenant::factory()->create();
    $tenantB = Tenant::factory()->create();

    $caseA = DeliveryCase::factory()->create([
        'tenant_id' => $tenantA->id,
        'task_ref' => 'DRIE-21919',
        'story_points' => 5,
    ]);
    $caseB = DeliveryCase::factory()->create([
        'tenant_id' => $tenantB->id,
        'task_ref' => 'DRIE-21919',
    ]);

    expect($caseA->task_ref)->toBe($caseB->task_ref)
        ->and($caseA->current_stage)->toBe(DeliveryStage::Pending)
        ->and($caseA->story_points)->toBe('5.00')
        ->and($caseA->first_seen_at)->not->toBeNull()
        ->and($caseA->last_activity_at)->not->toBeNull();

    expect(fn () => DeliveryCase::factory()->create([
        'tenant_id' => $tenantA->id,
        'task_ref' => 'DRIE-21919',
    ]))->toThrow(QueryException::class);
});

it('keeps implementer and QA attribution as separate temporal roles', function () {
    $tenant = Tenant::factory()->create();
    $team = Team::factory()->create(['tenant_id' => $tenant->id]);
    $implementer = Person::factory()->create([
        'tenant_id' => $tenant->id,
        'team_id' => $team->id,
    ]);
    $qa = Person::factory()->create([
        'tenant_id' => $tenant->id,
        'team_id' => $team->id,
    ]);
    $deliveryCase = DeliveryCase::factory()->create(['tenant_id' => $tenant->id]);
    $developmentStartedAt = now()->subDays(4);
    $qaStartedAt = now()->subDay();

    $developerParticipation = DeliveryCaseParticipant::factory()
        ->forDeliveryCase($deliveryCase)
        ->create([
            'person_id' => $implementer->id,
            'role' => DeliveryParticipantRole::Implementer,
            'confidence' => DeliveryAssociationConfidence::High,
            'valid_from' => $developmentStartedAt,
        ]);
    $qaParticipation = DeliveryCaseParticipant::factory()
        ->forDeliveryCase($deliveryCase)
        ->create([
            'person_id' => $qa->id,
            'role' => DeliveryParticipantRole::Qa,
            'confidence' => DeliveryAssociationConfidence::Medium,
            'valid_from' => $qaStartedAt,
        ]);

    $activeParticipants = DeliveryCaseParticipant::query()
        ->whereBelongsTo($deliveryCase)
        ->activeAt(now())
        ->get();

    expect($developerParticipation->role)->toBe(DeliveryParticipantRole::Implementer)
        ->and($developerParticipation->confidence)->toBe(DeliveryAssociationConfidence::High)
        ->and($qaParticipation->role)->toBe(DeliveryParticipantRole::Qa)
        ->and($qaParticipation->confidence)->toBe(DeliveryAssociationConfidence::Medium)
        ->and($activeParticipants)->toHaveCount(2)
        ->and($activeParticipants->pluck('person_id')->all())
        ->toContain($implementer->id, $qa->id);
});

it('orders milestones by occurrence even when events arrive out of order', function () {
    $tenant = Tenant::factory()->create();
    $deliveryCase = DeliveryCase::factory()->create(['tenant_id' => $tenant->id]);
    $integration = IntegrationSystem::factory()->create(['tenant_id' => $tenant->id]);
    $webhookEvent = IntegrationWebhookEvent::factory()->create([
        'tenant_id' => $tenant->id,
        'integration_system_id' => $integration->id,
        'person_id' => null,
    ]);
    $developmentStartedAt = now()->subDays(5);
    $publishedAt = now()->subDay();

    DeliveryCaseMilestone::factory()->forDeliveryCase($deliveryCase)->create([
        'integration_webhook_event_id' => $webhookEvent->id,
        'milestone_type' => DeliveryMilestoneType::Published,
        'semantic_key' => 'clickup:published:event-2',
        'occurred_at' => $publishedAt,
    ]);
    DeliveryCaseMilestone::factory()->forDeliveryCase($deliveryCase)->create([
        'milestone_type' => DeliveryMilestoneType::DevelopmentStarted,
        'semantic_key' => 'clickup:development-started:event-1',
        'occurred_at' => $developmentStartedAt,
    ]);

    $milestones = $deliveryCase->fresh()->milestones;

    expect($milestones)->toHaveCount(2)
        ->and($milestones->first()->milestone_type)->toBe(DeliveryMilestoneType::DevelopmentStarted)
        ->and($milestones->last()->milestone_type)->toBe(DeliveryMilestoneType::Published)
        ->and($milestones->last()->integrationWebhookEvent->is($webhookEvent))->toBeTrue();
});

it('rejects duplicate semantic milestones for the same delivery', function () {
    $deliveryCase = DeliveryCase::factory()->create();

    DeliveryCaseMilestone::factory()->forDeliveryCase($deliveryCase)->create([
        'semantic_key' => 'clickup:status:event-1:published',
    ]);

    expect(fn () => DeliveryCaseMilestone::factory()->forDeliveryCase($deliveryCase)->create([
        'semantic_key' => 'clickup:status:event-1:published',
    ]))->toThrow(QueryException::class);
});

it('links one delivery to multiple pull requests and its head SHA', function () {
    $tenant = Tenant::factory()->create();
    $deliveryCase = DeliveryCase::factory()->create(['tenant_id' => $tenant->id]);
    $integration = IntegrationSystem::factory()->create(['tenant_id' => $tenant->id]);

    DeliveryCaseExternalLink::factory()->forDeliveryCase($deliveryCase)->create([
        'integration_system_id' => $integration->id,
        'link_type' => DeliveryExternalLinkType::GitHubPullRequest,
        'external_id' => 'acme/api#101',
    ]);
    DeliveryCaseExternalLink::factory()->forDeliveryCase($deliveryCase)->create([
        'integration_system_id' => $integration->id,
        'link_type' => DeliveryExternalLinkType::GitHubPullRequest,
        'external_id' => 'acme/app#202',
    ]);
    DeliveryCaseExternalLink::factory()->forDeliveryCase($deliveryCase)->create([
        'integration_system_id' => $integration->id,
        'link_type' => DeliveryExternalLinkType::GitHubHeadSha,
        'external_id' => 'acme/api@abc123',
    ]);

    $links = $deliveryCase->fresh()->externalLinks;

    expect($links)->toHaveCount(3)
        ->and($links->where('link_type', DeliveryExternalLinkType::GitHubPullRequest))->toHaveCount(2)
        ->and($links->where('link_type', DeliveryExternalLinkType::GitHubHeadSha))->toHaveCount(1)
        ->and($links->every(fn (DeliveryCaseExternalLink $link): bool => $link->integrationSystem->is($integration)))
        ->toBeTrue();
});

it('does not associate the same external entity with two deliveries in a tenant', function () {
    $tenant = Tenant::factory()->create();
    $caseA = DeliveryCase::factory()->create(['tenant_id' => $tenant->id]);
    $caseB = DeliveryCase::factory()->create(['tenant_id' => $tenant->id]);

    DeliveryCaseExternalLink::factory()->forDeliveryCase($caseA)->create([
        'link_type' => DeliveryExternalLinkType::GitHubPullRequest,
        'external_id' => 'acme/api#101',
    ]);

    expect(fn () => DeliveryCaseExternalLink::factory()->forDeliveryCase($caseB)->create([
        'link_type' => DeliveryExternalLinkType::GitHubPullRequest,
        'external_id' => 'acme/api#101',
    ]))->toThrow(QueryException::class);
});

it('applies tenant isolation to canonical deliveries', function () {
    $tenantA = Tenant::factory()->create();
    $tenantB = Tenant::factory()->create();
    $leadA = User::factory()->create(['tenant_id' => $tenantA->id]);

    DeliveryCase::factory()->create([
        'tenant_id' => $tenantA->id,
        'task_ref' => 'TEAM-A-1',
    ]);
    DeliveryCase::factory()->create([
        'tenant_id' => $tenantB->id,
        'task_ref' => 'TEAM-B-1',
    ]);

    $this->actingAs($leadA, 'sanctum');

    expect(DeliveryCase::query()->pluck('task_ref')->all())->toBe(['TEAM-A-1']);
});
