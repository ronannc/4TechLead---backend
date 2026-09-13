<?php

use App\Models\IntegrationSystem;
use App\Models\IntegrationWebhookEvent;
use App\Models\Person;
use App\Models\PersonDeliveryMetric;
use App\Services\DeliveryMetricIngestService;

it('counts only workflow runs as canonical ci executions', function (): void {
    $integration = IntegrationSystem::factory()->create(['provider' => 'github']);
    $person = Person::factory()->create(['tenant_id' => $integration->tenant_id]);
    $service = app(DeliveryMetricIngestService::class);

    foreach (['check_run.completed', 'check_suite.completed'] as $eventType) {
        $event = githubMetricEvent($integration, $person, $eventType, [
            'check_run_conclusion' => 'failure',
            'check_suite_conclusion' => 'failure',
        ]);

        $service->ingest($event);
    }

    $workflow = githubMetricEvent($integration, $person, 'workflow_run.completed', [
        'workflow_run_conclusion' => 'failure',
    ]);

    $service->ingest($workflow);
    $service->ingest($workflow);

    expect(PersonDeliveryMetric::query()->pluck('metric_type')->all())->toEqualCanonicalizing([
        'ci_run_count',
        'ci_failure_count',
    ]);
});

it('does not count non-terminal deployment states as failures', function (): void {
    $integration = IntegrationSystem::factory()->create(['provider' => 'github']);
    $person = Person::factory()->create(['tenant_id' => $integration->tenant_id]);
    $service = app(DeliveryMetricIngestService::class);

    $pending = githubMetricEvent($integration, $person, 'deployment_status.created', [
        'deployment_status_state' => 'in_progress',
    ]);
    $failed = githubMetricEvent($integration, $person, 'deployment_status.created', [
        'deployment_status_state' => 'failure',
    ]);

    $service->ingest($pending);
    expect(PersonDeliveryMetric::query()->count())->toBe(0);

    $service->ingest($failed);
    expect(PersonDeliveryMetric::query()->pluck('metric_type')->all())->toEqualCanonicalizing([
        'deployment_count',
        'deployment_failure_count',
    ]);
});

/**
 * @param  array<string, mixed>  $normalizedPayload
 */
function githubMetricEvent(
    IntegrationSystem $integration,
    Person $person,
    string $eventType,
    array $normalizedPayload,
): IntegrationWebhookEvent {
    return IntegrationWebhookEvent::factory()->create([
        'tenant_id' => $integration->tenant_id,
        'integration_system_id' => $integration->id,
        'person_id' => $person->id,
        'event_id' => fake()->uuid(),
        'event_type' => $eventType,
        'normalized_payload' => [
            'source' => 'github',
            'task_refs' => ['DRIE-21919'],
            'occurred_at' => now()->toISOString(),
            ...$normalizedPayload,
        ],
        'received_at' => now(),
    ]);
}
