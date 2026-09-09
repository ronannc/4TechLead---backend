<?php

use App\Models\IntegrationSystem;
use App\Models\IntegrationWebhookEvent;
use App\Models\Person;
use App\Models\PersonDeliveryMetric;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

it('lists webhook events with search filters ordering and related context for tech leads', function (): void {
    Sanctum::actingAs(User::factory()->create());

    $github = IntegrationSystem::factory()->create(['name' => 'GitHub Produto', 'provider' => 'github']);
    $clickUp = IntegrationSystem::factory()->create(['name' => 'ClickUp Produto', 'provider' => 'clickup']);
    $person = Person::factory()->create(['name' => 'Ada Lovelace']);

    IntegrationWebhookEvent::factory()->create([
        'integration_system_id' => $clickUp->id,
        'person_id' => null,
        'event_id' => 'clickup-old',
        'event_type' => 'task.updated',
        'status' => 'unmapped_person',
        'external_actor_code' => 'clickup_user:123',
        'failure_reason' => 'Pessoa externa nao mapeada.',
        'received_at' => '2026-09-07 10:00:00',
    ]);

    $matched = IntegrationWebhookEvent::factory()->create([
        'integration_system_id' => $github->id,
        'person_id' => $person->id,
        'event_id' => 'github-new',
        'event_type' => 'pull_request.merged',
        'status' => 'processed',
        'external_actor_code' => 'ada',
        'received_at' => '2026-09-08 10:00:00',
    ]);

    PersonDeliveryMetric::factory()->create([
        'person_id' => $person->id,
        'integration_system_id' => $github->id,
        'integration_webhook_event_id' => $matched->id,
        'metric_type' => 'pull_request_count',
    ]);

    $query = http_build_query([
        'search' => 'github',
        'filters' => [
            'integration_system_id' => $github->id,
            'status' => 'processed',
        ],
        'order' => ['received_at' => 'asc'],
        'per_page' => 10,
    ]);

    $this->getJson("/api/v1/integration-webhook-events?{$query}")
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $matched->id)
        ->assertJsonPath('data.0.integration_system.name', 'GitHub Produto')
        ->assertJsonPath('data.0.person.name', 'Ada Lovelace')
        ->assertJsonPath('data.0.delivery_metrics_count', 1)
        ->assertJsonPath('meta.total', 1);
});

it('shows a webhook event with processed payload and generated metrics', function (): void {
    Sanctum::actingAs(User::factory()->create());

    $integration = IntegrationSystem::factory()->create(['name' => 'GitHub Produto']);
    $person = Person::factory()->create(['name' => 'Grace Hopper']);
    $event = IntegrationWebhookEvent::factory()->create([
        'integration_system_id' => $integration->id,
        'person_id' => $person->id,
        'payload' => ['source' => 'github', 'repository' => 'org/repo'],
        'normalized_payload' => ['task_reference' => 'DRIE-21919'],
    ]);
    PersonDeliveryMetric::factory()->create([
        'person_id' => $person->id,
        'integration_system_id' => $integration->id,
        'integration_webhook_event_id' => $event->id,
        'metric_type' => 'code_quality_score',
        'metric_value' => 95,
    ]);

    $this->getJson("/api/v1/integration-webhook-events/{$event->id}")
        ->assertOk()
        ->assertJsonPath('data.id', $event->id)
        ->assertJsonPath('data.payload.repository', 'org/repo')
        ->assertJsonPath('data.normalized_payload.task_reference', 'DRIE-21919')
        ->assertJsonPath('data.delivery_metrics.0.metric_type', 'code_quality_score')
        ->assertJsonPath('data.delivery_metrics.0.metric_value', '95.00');
});

it('archives webhook events instead of permanently deleting them', function (): void {
    Sanctum::actingAs(User::factory()->create());

    $event = IntegrationWebhookEvent::factory()->create();

    $this->deleteJson("/api/v1/integration-webhook-events/{$event->id}")
        ->assertNoContent();

    expect(IntegrationWebhookEvent::query()->find($event->id))->toBeNull()
        ->and(IntegrationWebhookEvent::withTrashed()->find($event->id))->not->toBeNull();
});

it('blocks members from managing webhook event lake data', function (): void {
    Sanctum::actingAs(User::factory()->member()->create());

    $event = IntegrationWebhookEvent::factory()->create();

    $this->getJson('/api/v1/integration-webhook-events')->assertForbidden();
    $this->getJson("/api/v1/integration-webhook-events/{$event->id}")->assertForbidden();
    $this->deleteJson("/api/v1/integration-webhook-events/{$event->id}")->assertForbidden();
});

it('requires authentication to inspect webhook events', function (): void {
    IntegrationWebhookEvent::factory()->create();

    $this->getJson('/api/v1/integration-webhook-events')->assertUnauthorized();
});
