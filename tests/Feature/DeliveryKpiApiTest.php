<?php

use App\Enums\DeliveryAssociationConfidence;
use App\Enums\DeliveryMilestoneType;
use App\Enums\DeliveryParticipantRole;
use App\Enums\DeliveryStage;
use App\Models\DeliveryCase;
use App\Models\DeliveryCaseMilestone;
use App\Models\DeliveryCaseParticipant;
use App\Models\Person;
use App\Models\Team;
use App\Models\Tenant;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

it('returns tenant-scoped KPI aggregates to tech leads', function (): void {
    $tenant = Tenant::factory()->create();
    $team = Team::factory()->create(['tenant_id' => $tenant->id]);
    $user = User::factory()->create(['tenant_id' => $tenant->id]);
    $person = Person::factory()->create(['tenant_id' => $tenant->id, 'team_id' => $team->id]);
    $deliveryCase = DeliveryCase::factory()->create([
        'tenant_id' => $tenant->id,
        'task_ref' => 'DRIE-25000',
        'current_stage' => DeliveryStage::Published,
        'story_points' => 5,
        'completed_at' => '2026-09-13 12:00:00',
        'last_activity_at' => '2026-09-13 12:00:00',
    ]);
    DeliveryCaseParticipant::factory()->forDeliveryCase($deliveryCase)->create([
        'person_id' => $person->id,
        'role' => DeliveryParticipantRole::Implementer,
        'confidence' => DeliveryAssociationConfidence::High,
    ]);
    DeliveryCaseMilestone::factory()->forDeliveryCase($deliveryCase)->create([
        'milestone_type' => DeliveryMilestoneType::Published,
        'semantic_key' => 'published:DRIE-25000',
        'occurred_at' => '2026-09-13 12:00:00',
    ]);

    Sanctum::actingAs($user);

    $this->getJson("/api/v1/delivery-kpis?person_id={$person->id}&period_start=2026-09-13&period_end=2026-09-13")
        ->assertOk()
        ->assertJsonPath('data.definition_version', 'delivery-kpis.v1')
        ->assertJsonPath('data.filters.person_id', $person->id)
        ->assertJsonPath('data.published_delivery_count.value', 1)
        ->assertJsonPath('data.published_story_points.value', 5);
});

it('does not expose delivery KPIs to members', function (): void {
    Sanctum::actingAs(User::factory()->member()->create());

    $this->getJson('/api/v1/delivery-kpis')->assertForbidden();
});
