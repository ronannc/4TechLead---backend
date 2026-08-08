<?php

use App\Models\IntegrationSystem;
use App\Models\Person;
use App\Models\PersonDeliveryMetric;
use App\Models\PersonExternalIdentity;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

it('creates an integration system with a one time webhook token', function (): void {
    Sanctum::actingAs(User::factory()->create());

    $response = $this->postJson('/api/v1/integration-systems', [
        'name' => 'GitHub Produto',
        'provider' => 'github',
        'description' => 'PRs e CI do time de produto.',
    ]);

    $response
        ->assertCreated()
        ->assertJsonPath('data.name', 'GitHub Produto')
        ->assertJsonPath('data.provider', 'github')
        ->assertJsonPath('data.active', true)
        ->assertJsonStructure(['data' => ['id', 'token_prefix', 'webhook_token']]);

    $token = $response->json('data.webhook_token');
    expect($token)->toBeString()->not->toBe('');

    $integration = IntegrationSystem::query()->firstOrFail();

    expect($integration->token_hash)->toBe(hash('sha256', $token));
    expect($integration->token_hash)->not->toBe($token);
});

it('maps an external identity to a person', function (): void {
    Sanctum::actingAs(User::factory()->create());

    $person = Person::factory()->create();
    $integration = IntegrationSystem::factory()->create();

    $this->postJson('/api/v1/person-external-identities', [
        'person_id' => $person->id,
        'integration_system_id' => $integration->id,
        'external_code' => 'lucas-github',
        'external_username' => 'Lucas Farias',
    ])->assertCreated()
        ->assertJsonPath('data.person_id', $person->id)
        ->assertJsonPath('data.external_code', 'lucas-github');
});

it('rejects duplicate external identity codes for the same integration system', function (): void {
    Sanctum::actingAs(User::factory()->create());

    $integration = IntegrationSystem::factory()->create();
    PersonExternalIdentity::factory()->create([
        'integration_system_id' => $integration->id,
        'external_code' => 'lucas-github',
    ]);

    $this->postJson('/api/v1/person-external-identities', [
        'person_id' => Person::factory()->create()->id,
        'integration_system_id' => $integration->id,
        'external_code' => 'lucas-github',
    ])->assertUnprocessable()
        ->assertJsonValidationErrors('external_code');
});

it('receives a github pull request webhook and creates delivery metrics for the mapped person', function (): void {
    $token = 'github-secret-token';
    $integration = IntegrationSystem::factory()->create([
        'provider' => 'github',
        'token_hash' => hash('sha256', $token),
        'token_prefix' => substr($token, 0, 8),
    ]);
    $person = Person::factory()->create();
    PersonExternalIdentity::factory()->create([
        'person_id' => $person->id,
        'integration_system_id' => $integration->id,
        'external_code' => 'lucas-github',
    ]);

    $payload = githubPullRequestPayload();

    $this->postJson(
        "/api/v1/integration-webhooks/{$integration->id}",
        $payload,
        ['Authorization' => "Bearer {$token}"],
    )->assertOk()
        ->assertJsonPath('data.person_id', $person->id)
        ->assertJsonPath('data.status', 'processed')
        ->assertJsonPath('data.normalized_payload.quality_score', 55);

    expect(PersonDeliveryMetric::query()->where('person_id', $person->id)->count())->toBe(7);
    expect(
        PersonDeliveryMetric::query()
            ->where('person_id', $person->id)
            ->where('metric_type', 'code_quality_score')
            ->value('metric_value'),
    )->toBe('55.00');
    expect(
        PersonDeliveryMetric::query()
            ->where('person_id', $person->id)
            ->where('metric_type', 'delivery_points')
            ->value('metric_value'),
    )->toBe('8.00');
});

it('does not duplicate metrics when the same webhook event is received again', function (): void {
    $token = 'github-secret-token';
    $integration = IntegrationSystem::factory()->create([
        'token_hash' => hash('sha256', $token),
        'token_prefix' => substr($token, 0, 8),
    ]);
    $person = Person::factory()->create();
    PersonExternalIdentity::factory()->create([
        'person_id' => $person->id,
        'integration_system_id' => $integration->id,
        'external_code' => 'lucas-github',
    ]);

    $payload = githubPullRequestPayload();

    $this->postJson("/api/v1/integration-webhooks/{$integration->id}", $payload, [
        'Authorization' => "Bearer {$token}",
    ])->assertOk();
    $this->postJson("/api/v1/integration-webhooks/{$integration->id}", $payload, [
        'Authorization' => "Bearer {$token}",
    ])->assertOk();

    expect(PersonDeliveryMetric::query()->where('person_id', $person->id)->count())->toBe(7);
});

it('rejects webhooks with an invalid token', function (): void {
    $integration = IntegrationSystem::factory()->create([
        'token_hash' => hash('sha256', 'valid-token'),
        'token_prefix' => 'valid-to',
    ]);

    $this->postJson("/api/v1/integration-webhooks/{$integration->id}", githubPullRequestPayload(), [
        'Authorization' => 'Bearer invalid-token',
    ])->assertUnauthorized();
});

it('rejects webhooks for inactive integrations', function (): void {
    $token = 'github-secret-token';
    $integration = IntegrationSystem::factory()->create([
        'active' => false,
        'token_hash' => hash('sha256', $token),
        'token_prefix' => substr($token, 0, 8),
    ]);

    $this->postJson("/api/v1/integration-webhooks/{$integration->id}", githubPullRequestPayload(), [
        'Authorization' => "Bearer {$token}",
    ])->assertForbidden();
});

it('accepts integration tokens through the x integration token header', function (): void {
    $token = 'github-secret-token';
    $integration = IntegrationSystem::factory()->create([
        'token_hash' => hash('sha256', $token),
        'token_prefix' => substr($token, 0, 8),
    ]);

    $this->postJson("/api/v1/integration-webhooks/{$integration->id}", githubPullRequestPayload(), [
        'X-Integration-Token' => $token,
    ])->assertOk()
        ->assertJsonPath('data.status', 'unmapped_person');
});

it('stores unmapped webhook events without generating metrics', function (): void {
    $token = 'github-secret-token';
    $integration = IntegrationSystem::factory()->create([
        'token_hash' => hash('sha256', $token),
        'token_prefix' => substr($token, 0, 8),
    ]);

    $this->postJson("/api/v1/integration-webhooks/{$integration->id}", githubPullRequestPayload(), [
        'Authorization' => "Bearer {$token}",
    ])->assertOk()
        ->assertJsonPath('data.status', 'unmapped_person')
        ->assertJsonPath('data.person_id', null);

    expect(PersonDeliveryMetric::query()->count())->toBe(0);
});

/**
 * @return array<string, mixed>
 */
function githubPullRequestPayload(): array
{
    return [
        'event_id' => 'github-pr-42-merged',
        'event_type' => 'pull_request_merged',
        'external_actor_code' => 'lucas-github',
        'occurred_at' => '2026-08-08T18:00:00Z',
        'source_ref' => 'org/repo#42',
        'payload' => [
            'task_refs' => ['ABC-123'],
            'pull_request' => [
                'number' => 42,
                'title' => 'ABC-123 entregar fluxo de pagamentos',
                'review_comments_count' => 5,
                'comments_count' => 8,
                'ci_failures_count' => 1,
                'rework_count' => 1,
                'story_points' => 8,
                'changed_files' => 12,
                'additions' => 420,
                'deletions' => 80,
                'merged_at' => '2026-08-08T18:00:00Z',
            ],
        ],
    ];
}
