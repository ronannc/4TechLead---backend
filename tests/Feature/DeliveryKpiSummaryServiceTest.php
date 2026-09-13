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
use App\Services\DeliveryKpiSummaryService;

it('summarizes only explainable delivery KPIs from correlated cases', function (): void {
    $tenant = Tenant::factory()->create();
    $team = Team::factory()->create(['tenant_id' => $tenant->id]);
    $developer = Person::factory()->create(['tenant_id' => $tenant->id, 'team_id' => $team->id]);
    $firstPass = kpiCase($tenant, 'DRIE-24000', 5, '2026-09-10 13:00:00');
    $rework = kpiCase($tenant, 'DRIE-24001', 3, '2026-09-11 13:00:00');
    $rejected = DeliveryCase::factory()->create([
        'tenant_id' => $tenant->id,
        'task_ref' => 'DRIE-24002',
        'current_stage' => DeliveryStage::Rejected,
        'canceled_at' => '2026-09-12 10:00:00',
        'last_activity_at' => '2026-09-12 10:00:00',
    ]);

    foreach ([$firstPass, $rework] as $case) {
        DeliveryCaseParticipant::factory()->forDeliveryCase($case)->create([
            'person_id' => $developer->id,
            'role' => DeliveryParticipantRole::Implementer,
            'confidence' => DeliveryAssociationConfidence::High,
            'valid_from' => '2026-09-10 08:00:00',
        ]);
        kpiMilestone($case, DeliveryMilestoneType::DevelopmentStarted, '2026-09-10 08:00:00');
        kpiMilestone($case, DeliveryMilestoneType::ImplementationCompleted, '2026-09-10 10:00:00');
        kpiMilestone($case, DeliveryMilestoneType::QaStarted, '2026-09-10 11:00:00');
        kpiMilestone($case, DeliveryMilestoneType::QaApproved, '2026-09-10 12:00:00');
        kpiMilestone($case, DeliveryMilestoneType::Published, '2026-09-10 13:00:00');
        kpiMilestone($case, DeliveryMilestoneType::Blocked, '2026-09-10 08:30:00');
        kpiMilestone($case, DeliveryMilestoneType::Unblocked, '2026-09-10 09:00:00');
    }
    kpiMilestone($rework, DeliveryMilestoneType::QaFailed, '2026-09-10 11:30:00');

    $summary = app(DeliveryKpiSummaryService::class)->summarize($tenant->id, [
        'person_id' => $developer->id,
        'period_start' => '2026-09-10',
        'period_end' => '2026-09-11',
    ]);

    expect($summary['definition_version'])->toBe('delivery-kpis.v1')
        ->and($summary['coverage']['cases_in_scope'])->toBe(2)
        ->and($summary['coverage']['canceled_cases_excluded'])->toBe(0)
        ->and($summary['published_delivery_count']['value'])->toBe(2)
        ->and($summary['published_story_points']['value'])->toBe(8.0)
        ->and($summary['development_cycle_time_hours']['p50'])->toBe(2.0)
        ->and($summary['qa_wait_time_hours']['p50'])->toBe(1.0)
        ->and($summary['qa_first_pass_rate']['numerator'])->toBe(1)
        ->and($summary['qa_first_pass_rate']['denominator'])->toBe(2)
        ->and($summary['qa_first_pass_rate']['value_pct'])->toBe(50.0)
        ->and($summary['qa_rework_rate']['value_pct'])->toBe(50.0)
        ->and($summary['blocked_time_hours']['total'])->toBe(1.0);

    expect($rejected->exists)->toBeTrue();
});

it('filters people by confidence without inflating team throughput', function (): void {
    $tenant = Tenant::factory()->create();
    $team = Team::factory()->create(['tenant_id' => $tenant->id]);
    $developer = Person::factory()->create(['tenant_id' => $tenant->id, 'team_id' => $team->id]);
    $case = kpiCase($tenant, 'DRIE-24003', 2, '2026-09-13 12:00:00');
    kpiMilestone($case, DeliveryMilestoneType::Published, '2026-09-13 12:00:00');
    DeliveryCaseParticipant::factory()->forDeliveryCase($case)->create([
        'person_id' => $developer->id,
        'role' => DeliveryParticipantRole::CodeAuthor,
        'confidence' => DeliveryAssociationConfidence::Low,
        'valid_from' => '2026-09-13 08:00:00',
    ]);

    $highOnly = app(DeliveryKpiSummaryService::class)->summarize($tenant->id, [
        'person_id' => $developer->id,
        'minimum_confidence' => DeliveryAssociationConfidence::High,
    ]);
    $lowIncluded = app(DeliveryKpiSummaryService::class)->summarize($tenant->id, [
        'person_id' => $developer->id,
        'minimum_confidence' => DeliveryAssociationConfidence::Low,
    ]);

    expect($highOnly['coverage']['associated_cases'])->toBe(0)
        ->and($highOnly['published_delivery_count']['value'])->toBe(0)
        ->and($lowIncluded['coverage']['associated_cases'])->toBe(1)
        ->and($lowIncluded['published_delivery_count']['value'])->toBe(1);
});

function kpiCase(Tenant $tenant, string $taskReference, int $storyPoints, string $completedAt): DeliveryCase
{
    return DeliveryCase::factory()->create([
        'tenant_id' => $tenant->id,
        'task_ref' => $taskReference,
        'current_stage' => DeliveryStage::Published,
        'story_points' => $storyPoints,
        'completed_at' => $completedAt,
        'last_activity_at' => $completedAt,
    ]);
}

function kpiMilestone(DeliveryCase $case, DeliveryMilestoneType $type, string $occurredAt): void
{
    DeliveryCaseMilestone::factory()->forDeliveryCase($case)->create([
        'milestone_type' => $type,
        'semantic_key' => $case->task_ref.':'.$type->value.':'.$occurredAt,
        'occurred_at' => $occurredAt,
    ]);
}
