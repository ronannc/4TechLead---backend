<?php

use App\Models\ExternalNotification;
use App\Models\IntegrationSystem;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

it('rejects notification webhooks without a valid integration token', function (): void {
    $integration = IntegrationSystem::factory()->create([
        'token_hash' => hash('sha256', 'valid-token'),
        'token_prefix' => 'valid-to',
    ]);

    $this->postJson("/api/v1/notification-webhooks/{$integration->id}", notificationPayload(), [
        'Authorization' => 'Bearer invalid-token',
    ])->assertUnauthorized();

    expect(ExternalNotification::query()->count())->toBe(0);
});

it('stores an external notification using an integration bearer token', function (): void {
    $token = 'github-notification-token';
    $integration = IntegrationSystem::factory()->create([
        'name' => 'GitHub Actions',
        'token_hash' => hash('sha256', $token),
        'token_prefix' => substr($token, 0, 8),
    ]);

    $this->postJson("/api/v1/notification-webhooks/{$integration->id}", notificationPayload(), [
        'Authorization' => "Bearer {$token}",
    ])->assertOk()
        ->assertJsonPath('data.integration_system_id', $integration->id)
        ->assertJsonPath('data.integration_system.name', 'GitHub Actions')
        ->assertJsonPath('data.title', 'Deploy finalizado')
        ->assertJsonPath('data.severity', 'success')
        ->assertJsonPath('data.source_ref', 'org/repo/actions/42');

    $notification = ExternalNotification::query()->firstOrFail();

    expect($notification->payload)->toBe(['workflow' => 'deploy'])
        ->and($notification->metadata)->toBe([
            'environment' => 'production',
            'occurred_at' => '2026-08-09T12:00:00Z',
        ])
        ->and($integration->refresh()->last_received_at)->not->toBeNull();
});

it('does not duplicate external notifications with the same provider event id', function (): void {
    $token = 'github-notification-token';
    $integration = IntegrationSystem::factory()->create([
        'token_hash' => hash('sha256', $token),
        'token_prefix' => substr($token, 0, 8),
    ]);

    $this->postJson("/api/v1/notification-webhooks/{$integration->id}", notificationPayload(), [
        'Authorization' => "Bearer {$token}",
    ])->assertOk();

    $this->postJson("/api/v1/notification-webhooks/{$integration->id}", notificationPayload(), [
        'X-Integration-Token' => $token,
    ])->assertOk();

    expect(ExternalNotification::query()->count())->toBe(1);
});

it('lists external notifications paginated with the latest first', function (): void {
    Sanctum::actingAs(User::factory()->create());

    ExternalNotification::factory()->create([
        'title' => 'Mais antiga',
        'received_at' => '2026-08-08 10:00:00',
    ]);
    ExternalNotification::factory()->create([
        'title' => 'Mais nova',
        'received_at' => '2026-08-09 10:00:00',
    ]);
    ExternalNotification::factory()->create([
        'title' => 'Intermediaria',
        'received_at' => '2026-08-08 18:00:00',
    ]);

    $this->getJson('/api/v1/notifications?per_page=2')
        ->assertOk()
        ->assertJsonPath('data.0.title', 'Mais nova')
        ->assertJsonPath('data.1.title', 'Intermediaria')
        ->assertJsonPath('meta.current_page', 1)
        ->assertJsonPath('meta.per_page', 2)
        ->assertJsonPath('meta.total', 3);

    $this->getJson('/api/v1/notifications?per_page=2&page=2')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.title', 'Mais antiga')
        ->assertJsonPath('meta.current_page', 2);
});

it('requires app authentication to list external notifications', function (): void {
    ExternalNotification::factory()->create();

    $this->getJson('/api/v1/notifications')->assertUnauthorized();
});

/**
 * @return array<string, mixed>
 */
function notificationPayload(): array
{
    return [
        'event_id' => 'github-actions-42',
        'title' => 'Deploy finalizado',
        'message' => 'Pipeline de producao finalizou com sucesso.',
        'type' => 'deploy',
        'severity' => 'success',
        'source_ref' => 'org/repo/actions/42',
        'payload' => ['workflow' => 'deploy'],
        'metadata' => ['environment' => 'production'],
        'occurred_at' => '2026-08-09T12:00:00Z',
    ];
}
