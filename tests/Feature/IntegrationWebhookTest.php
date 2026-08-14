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

it('regenerates an integration webhook token and invalidates the old token', function (): void {
    Sanctum::actingAs(User::factory()->create());

    $oldToken = 'old-github-secret-token';
    $integration = IntegrationSystem::factory()->create([
        'token_hash' => hash('sha256', $oldToken),
        'token_prefix' => substr($oldToken, 0, 8),
    ]);

    $response = $this->postJson("/api/v1/integration-systems/{$integration->id}/regenerate-token")
        ->assertOk()
        ->assertJsonPath('data.id', $integration->id)
        ->assertJsonStructure(['data' => ['token_prefix', 'webhook_token']]);

    $newToken = $response->json('data.webhook_token');

    expect($newToken)
        ->toBeString()
        ->not->toBe('')
        ->not->toBe($oldToken);

    $integration->refresh();

    expect($integration->token_hash)->toBe(hash('sha256', $newToken));
    expect($integration->token_prefix)->toBe(substr($newToken, 0, 8));

    $this->postJson('/api/v1/integration-webhooks', githubPullRequestPayload(), [
        'Authorization' => "Bearer {$oldToken}",
    ])->assertUnauthorized();

    $this->postJson('/api/v1/integration-webhooks', githubPullRequestPayload(), [
        'Authorization' => "Bearer {$newToken}",
    ])->assertOk()
        ->assertJsonPath('data.status', 'unmapped_person');
});

it('maps an external identity to a person', function (): void {
    Sanctum::actingAs(User::factory()->create());

    $person = Person::factory()->create();
    $integration = IntegrationSystem::factory()->create();

    $this->postJson('/api/v1/person-external-identities', [
        'person_id' => $person->id,
        'integration_system_id' => $integration->id,
    ])->assertCreated()
        ->assertJsonPath('data.person_id', $person->id)
        ->assertJsonPath('data.integration_system_id', $integration->id)
        ->assertJsonMissingPath('data.external_username');

    $identity = PersonExternalIdentity::query()->firstOrFail();

    expect($identity->external_code)
        ->toStartWith('ext_')
        ->toHaveLength(24);
});

it('rejects duplicate person mappings for the same integration system', function (): void {
    Sanctum::actingAs(User::factory()->create());

    $integration = IntegrationSystem::factory()->create();
    $person = Person::factory()->create();
    PersonExternalIdentity::factory()->create([
        'person_id' => $person->id,
        'integration_system_id' => $integration->id,
    ]);

    $this->postJson('/api/v1/person-external-identities', [
        'person_id' => $person->id,
        'integration_system_id' => $integration->id,
    ])->assertUnprocessable()
        ->assertJsonValidationErrors('person_id');
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
        '/api/v1/integration-webhooks',
        $payload,
        ['Authorization' => "Bearer {$token}"],
    )->assertOk()
        ->assertJsonPath('data.person_id', $person->id)
        ->assertJsonPath('data.status', 'processed')
        ->assertJsonPath('data.normalized_payload.quality_score', 55)
        ->assertJsonPath('data.normalized_payload.changed_lines', 500)
        ->assertJsonPath('data.normalized_payload.review_count', 3)
        ->assertJsonPath('data.normalized_payload.head_ref', 'feature/payments')
        ->assertJsonPath('data.normalized_payload.base_ref', 'main')
        ->assertJsonPath('data.normalized_payload.review_acceptance_rate', 0)
        ->assertJsonPath('data.normalized_payload.ci_success_rate', 0)
        ->assertJsonPath('data.normalized_payload.pr_open_time_hours', 32)
        ->assertJsonPath('data.normalized_payload.pr_merge_time_hours', 32)
        ->assertJsonMissingPath('data.normalized_payload.analysis');

    expect(PersonDeliveryMetric::query()->where('person_id', $person->id)->count())->toBe(21);
    expect(
        PersonDeliveryMetric::query()
            ->where('person_id', $person->id)
            ->where('metric_type', 'code_quality_score')
            ->value('metric_value'),
    )->toBe('55.00');
    expect(
        PersonDeliveryMetric::query()
            ->where('person_id', $person->id)
            ->where('metric_type', 'code_quality_score')
            ->firstOrFail()
            ->metadata,
    )->not->toHaveKey('analysis');
    expect(
        PersonDeliveryMetric::query()
            ->where('person_id', $person->id)
            ->where('metric_type', 'delivery_points')
            ->value('metric_value'),
    )->toBe('8.00');
    expect(
        PersonDeliveryMetric::query()
            ->where('person_id', $person->id)
            ->where('metric_type', 'delivery_analysis')
            ->exists(),
    )->toBeFalse();
    expect(
        PersonDeliveryMetric::query()
            ->where('person_id', $person->id)
            ->where('metric_type', 'annual_ci_failure_average')
            ->value('metric_value'),
    )->toBe('1.00');
    expect(
        PersonDeliveryMetric::query()
            ->where('person_id', $person->id)
            ->where('metric_type', 'annual_review_comment_average')
            ->value('metric_value'),
    )->toBe('5.00');
    expect(
        PersonDeliveryMetric::query()
            ->where('person_id', $person->id)
            ->where('metric_type', 'annual_rework_average')
            ->value('metric_value'),
    )->toBe('1.00');
    expect(
        PersonDeliveryMetric::query()
            ->where('person_id', $person->id)
            ->where('metric_type', 'annual_pr_size_average')
            ->value('metric_value'),
    )->toBe('500.00');
    expect(
        PersonDeliveryMetric::query()
            ->where('person_id', $person->id)
            ->where('metric_type', 'annual_pr_merge_time_average')
            ->value('metric_value'),
    )->toBe('32.00');
    expect(
        PersonDeliveryMetric::query()
            ->where('person_id', $person->id)
            ->where('metric_type', 'annual_review_acceptance_rate')
            ->value('metric_value'),
    )->toBe('0.00');
    expect(
        PersonDeliveryMetric::query()
            ->where('person_id', $person->id)
            ->where('metric_type', 'annual_ci_success_rate')
            ->value('metric_value'),
    )->toBe('0.00');
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

    $this->postJson('/api/v1/integration-webhooks', $payload, [
        'Authorization' => "Bearer {$token}",
    ])->assertOk();
    $this->postJson('/api/v1/integration-webhooks', $payload, [
        'Authorization' => "Bearer {$token}",
    ])->assertOk();

    expect(PersonDeliveryMetric::query()->where('person_id', $person->id)->count())->toBe(21);
});

it('accepts a closed pull request without merge and stores the richer payload without delivery metrics', function (): void {
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

    $payload = githubPullRequestPayload([
        'event_id' => 'github-pr-42-closed',
        'event_type' => 'pull_request_closed',
        'occurred_at' => '2026-08-08T18:00:00Z',
        'payload' => [
            'pull_request' => [
                'closed_at' => '2026-08-08T18:00:00Z',
                'merged_at' => null,
            ],
        ],
    ]);

    $this->postJson('/api/v1/integration-webhooks', $payload, [
        'Authorization' => "Bearer {$token}",
    ])->assertOk()
        ->assertJsonPath('data.status', 'processed')
        ->assertJsonPath('data.normalized_payload.closed_without_merge', true)
        ->assertJsonPath('data.normalized_payload.pr_open_time_hours', 32)
        ->assertJsonPath('data.normalized_payload.pr_merge_time_hours', null);

    expect(PersonDeliveryMetric::query()->where('person_id', $person->id)->count())->toBe(0);
});

it('rejects webhooks with an invalid token', function (): void {
    $integration = IntegrationSystem::factory()->create([
        'token_hash' => hash('sha256', 'valid-token'),
        'token_prefix' => 'valid-to',
    ]);

    $this->postJson('/api/v1/integration-webhooks', githubPullRequestPayload(), [
        'Authorization' => 'Bearer invalid-token',
    ])->assertUnauthorized();
});

it('rejects merged pull request webhooks missing required pull request metrics', function (): void {
    $token = 'github-secret-token';
    IntegrationSystem::factory()->create([
        'token_hash' => hash('sha256', $token),
        'token_prefix' => substr($token, 0, 8),
    ]);

    $payload = githubPullRequestPayload();
    unset($payload['payload']['pull_request']['created_at']);

    $this->postJson('/api/v1/integration-webhooks', $payload, [
        'Authorization' => "Bearer {$token}",
    ])->assertUnprocessable()
        ->assertJsonValidationErrors('payload.pull_request.created_at');
});

it('rejects webhooks for inactive integrations', function (): void {
    $token = 'github-secret-token';
    $integration = IntegrationSystem::factory()->create([
        'active' => false,
        'token_hash' => hash('sha256', $token),
        'token_prefix' => substr($token, 0, 8),
    ]);

    $this->postJson('/api/v1/integration-webhooks', githubPullRequestPayload(), [
        'Authorization' => "Bearer {$token}",
    ])->assertForbidden();
});

it('accepts integration tokens through the x integration token header', function (): void {
    $token = 'github-secret-token';
    $integration = IntegrationSystem::factory()->create([
        'token_hash' => hash('sha256', $token),
        'token_prefix' => substr($token, 0, 8),
    ]);

    $this->postJson('/api/v1/integration-webhooks', githubPullRequestPayload(), [
        'X-Integration-Token' => $token,
    ])->assertOk()
        ->assertJsonPath('data.status', 'unmapped_person');
});

it('resolves the integration from the bearer token without requiring the integration id in the path', function (): void {
    $token = 'github-actions-project-token';
    $integration = IntegrationSystem::factory()->create([
        'provider' => 'github-actions',
        'token_hash' => hash('sha256', $token),
        'token_prefix' => substr($token, 0, 8),
    ]);
    $person = Person::factory()->create();
    PersonExternalIdentity::factory()->create([
        'person_id' => $person->id,
        'integration_system_id' => $integration->id,
        'external_code' => 'lucas-github-id',
    ]);

    $payload = githubPullRequestPayload([
        'event_id' => 'github-pr-43-merged',
        'external_actor_code' => 'lucas-github-id',
    ]);

    $this->postJson('/api/v1/integration-webhooks', $payload, [
        'Authorization' => "Bearer {$token}",
    ])->assertOk()
        ->assertJsonPath('data.integration_system_id', $integration->id)
        ->assertJsonPath('data.person_id', $person->id)
        ->assertJsonPath('data.status', 'processed');

    expect(
        PersonDeliveryMetric::query()
            ->where('person_id', $person->id)
            ->where('integration_system_id', $integration->id)
            ->where('metric_type', 'pull_request_count')
            ->exists(),
    )->toBeTrue();
});

it('stores unmapped webhook events without generating metrics', function (): void {
    $token = 'github-secret-token';
    $integration = IntegrationSystem::factory()->create([
        'token_hash' => hash('sha256', $token),
        'token_prefix' => substr($token, 0, 8),
    ]);

    $this->postJson('/api/v1/integration-webhooks', githubPullRequestPayload(), [
        'Authorization' => "Bearer {$token}",
    ])->assertOk()
        ->assertJsonPath('data.status', 'unmapped_person')
        ->assertJsonPath('data.person_id', null);

    expect(PersonDeliveryMetric::query()->count())->toBe(0);
});

/**
 * @return array<string, mixed>
 */
function githubPullRequestPayload(array $overrides = []): array
{
    return array_replace_recursive([
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
                'author' => 'lucas-github',
                'merged_by' => 'ronannc',
                'head_ref' => 'feature/payments',
                'base_ref' => 'main',
                'review_comments_count' => 5,
                'comments_count' => 8,
                'review_count' => 3,
                'unique_reviewer_count' => 2,
                'approvals_count' => 1,
                'changes_requested_count' => 1,
                'ci_failures_count' => 1,
                'rework_count' => 1,
                'story_points' => 8,
                'changed_files' => 12,
                'additions' => 420,
                'deletions' => 80,
                'created_at' => '2026-08-07T10:00:00Z',
                'closed_at' => '2026-08-08T18:00:00Z',
                'merged_at' => '2026-08-08T18:00:00Z',
            ],
        ],
    ], $overrides);
}
