<?php

use App\Models\IntegrationSystem;
use App\Models\Person;
use App\Models\PersonDeliveryMetric;
use App\Models\PersonExternalIdentity;
use App\Models\User;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\DB;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;

beforeEach(function (): void {
    $this->withoutMiddleware(ThrottleRequests::class);
});

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
    $webhookUrl = $response->json('data.webhook_url');
    expect($token)->toBeString()->not->toBe('');
    expect($webhookUrl)
        ->toBeString()
        ->toEndWith('/api/v1/github-webhooks')
        ->not->toContain($token);

    $integration = IntegrationSystem::query()->firstOrFail();
    $storedSecret = DB::table('integration_systems')->where('id', $integration->id)->value('webhook_secret');

    expect($integration->token_hash)->toBe(hash('sha256', $token));
    expect($integration->webhook_secret)->toBe($token);
    expect($integration->token_hash)->not->toBe($token);
    expect($storedSecret)->not->toBe($token);
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
    expect($integration->webhook_secret)->toBe($newToken);
    expect($integration->token_prefix)->toBe(substr($newToken, 0, 8));

    $payload = githubNativePullRequestPayload();
    $rawBody = json_encode($payload, JSON_THROW_ON_ERROR);

    $this->call(
        'POST',
        '/api/v1/github-webhooks',
        server: [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
            'HTTP_X_GITHUB_DELIVERY' => 'old-token-delivery',
            'HTTP_X_GITHUB_EVENT' => 'pull_request',
            'HTTP_X_HUB_SIGNATURE_256' => 'sha256='.hash_hmac('sha256', $rawBody, $oldToken),
        ],
        content: $rawBody,
    )->assertForbidden();

    $this->call(
        'POST',
        '/api/v1/github-webhooks',
        server: [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
            'HTTP_X_GITHUB_DELIVERY' => 'new-token-delivery',
            'HTTP_X_GITHUB_EVENT' => 'pull_request',
            'HTTP_X_HUB_SIGNATURE_256' => 'sha256='.hash_hmac('sha256', $rawBody, $newToken),
        ],
        content: $rawBody,
    )->assertOk()
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

it('receives a clickup automation webhook and stores the raw payload without generating metrics', function (): void {
    $token = 'clickup-automation-token';
    $integration = IntegrationSystem::factory()->create([
        'provider' => 'clickup',
        'token_hash' => hash('sha256', $token),
        'token_prefix' => substr($token, 0, 8),
    ]);

    $payload = clickUpAutomationPayload();

    $this->postJson('/api/v1/clickup-webhooks', $payload, [
        'X-Integration-Token' => $token,
    ])->assertOk()
        ->assertJson(fn (AssertableJson $json) => $json
            ->where('data.integration_system_id', $integration->id)
            ->where('data.person_id', null)
            ->where('data.event_id', '80c28fd1-2a2c-46a5-a0d6-67b0dbd27633:tasks')
            ->where('data.event_type', 'clickup_automation')
            ->where('data.external_actor_code', 'clickup_user:230504877')
            ->where('data.status', 'unmapped_person')
            ->where('data.payload.auto_id', '4ff67264-298b-4639-b0a8-4c066025f4e1:main')
            ->where('data.payload.payload.id', '86ak1xv8h')
            ->where('data.normalized_payload.source', 'clickup')
            ->where('data.normalized_payload.automation_id', '4ff67264-298b-4639-b0a8-4c066025f4e1:main')
            ->where('data.normalized_payload.trigger_id', '80c28fd1-2a2c-46a5-a0d6-67b0dbd27633:tasks')
            ->where('data.normalized_payload.workspace_id', '31134301')
            ->where('data.normalized_payload.task_id', '86ak1xv8h')
            ->where('data.normalized_payload.task_custom_id', 'DRIE-21919')
            ->where('data.normalized_payload.task_name', 'Fluxo de cadastro para nova oficina')
            ->where('data.normalized_payload.task_text_content', 'Cadastrar oficina nova e validar fluxo completo.')
            ->where('data.normalized_payload.task_status', 'teste de qualidade')
            ->where('data.normalized_payload.task_status_id', 'p90131743905_gBTMnApJ')
            ->where('data.normalized_payload.task_sprint_points', 5)
            ->where('data.normalized_payload.list_ids.0', '901328281243')
            ->etc());

    expect(PersonDeliveryMetric::query()->count())->toBe(0);
});

it('creates delivery metrics from a mapped clickup webhook', function (): void {
    $token = 'clickup-automation-token';
    $integration = IntegrationSystem::factory()->create([
        'provider' => 'clickup',
        'token_hash' => hash('sha256', $token),
        'token_prefix' => substr($token, 0, 8),
    ]);
    $person = Person::factory()->create();
    PersonExternalIdentity::factory()->create([
        'person_id' => $person->id,
        'integration_system_id' => $integration->id,
        'external_code' => 'clickup_user:230504877',
    ]);

    $this->postJson('/api/v1/clickup-webhooks', clickUpAutomationPayload(), [
        'X-Integration-Token' => $token,
    ])->assertOk()
        ->assertJsonPath('data.person_id', $person->id)
        ->assertJsonPath('data.status', 'processed');

    expect(PersonDeliveryMetric::query()->where('person_id', $person->id)->count())->toBe(4);
    expect(
        PersonDeliveryMetric::query()
            ->where('person_id', $person->id)
            ->where('metric_type', 'task_delivery_count')
            ->value('metric_value'),
    )->toBe('1.00');
    expect(
        PersonDeliveryMetric::query()
            ->where('person_id', $person->id)
            ->where('metric_type', 'delivery_points')
            ->value('metric_value'),
    )->toBe('5.00');
    expect(
        PersonDeliveryMetric::query()
            ->where('person_id', $person->id)
            ->where('metric_type', 'annual_task_delivery_count')
            ->value('metric_value'),
    )->toBe('1.00');
});

it('does not duplicate clickup webhook events with the same trigger id', function (): void {
    $token = 'clickup-automation-token';
    $integration = IntegrationSystem::factory()->create([
        'provider' => 'clickup',
        'token_hash' => hash('sha256', $token),
        'token_prefix' => substr($token, 0, 8),
    ]);

    $payload = clickUpAutomationPayload();

    $this->postJson('/api/v1/clickup-webhooks', $payload, [
        'Authorization' => "Bearer {$token}",
    ])->assertOk();
    $this->postJson('/api/v1/clickup-webhooks', $payload, [
        'Authorization' => "Bearer {$token}",
    ])->assertOk();

    expect($integration->webhookEvents()->count())->toBe(1)
        ->and(PersonDeliveryMetric::query()->count())->toBe(0);
});

it('receives a clickup api webhook payload using a query token', function (): void {
    $token = 'clickup-api-webhook-token';
    $integration = IntegrationSystem::factory()->create([
        'provider' => 'clickup',
        'token_hash' => hash('sha256', $token),
        'token_prefix' => substr($token, 0, 8),
    ]);

    $this->postJson(
        "/api/v1/clickup-webhooks?token={$token}",
        clickUpApiWebhookPayload(),
    )->assertOk()
        ->assertJsonPath('data.event_id', '4b67ac88-5749-4fdb-a975-ec5ed16b66e3:hist_123')
        ->assertJsonPath('data.event_type', 'taskStatusUpdated')
        ->assertJsonPath('data.external_actor_code', 'clickup_user:230504877')
        ->assertJsonPath('data.normalized_payload.history_field', 'status')
        ->assertJsonPath('data.normalized_payload.history_before', 'open')
        ->assertJsonPath('data.normalized_payload.history_after', 'done')
        ->assertJsonPath('data.normalized_payload.user_name', 'Ronan')
        ->assertJsonPath('data.normalized_payload.task_id', '86ak1xv8h');

    expect(PersonDeliveryMetric::query()->count())->toBe(0);
});

it('rejects clickup webhooks for non clickup integrations', function (): void {
    $token = 'github-secret-token';
    $integration = IntegrationSystem::factory()->create([
        'provider' => 'github',
        'token_hash' => hash('sha256', $token),
        'token_prefix' => substr($token, 0, 8),
    ]);

    $this->postJson('/api/v1/clickup-webhooks', clickUpAutomationPayload(), [
        'Authorization' => "Bearer {$token}",
    ])->assertUnauthorized();
});

it('rejects clickup webhooks for inactive clickup integrations resolved by token', function (): void {
    $token = 'inactive-clickup-token';
    IntegrationSystem::factory()->create([
        'provider' => 'clickup',
        'active' => false,
        'token_hash' => hash('sha256', $token),
        'token_prefix' => substr($token, 0, 8),
    ]);

    $this->postJson('/api/v1/clickup-webhooks', clickUpAutomationPayload(), [
        'Authorization' => "Bearer {$token}",
    ])->assertForbidden();
});

it('receives a github pull request webhook and stores the raw payload without generating metrics', function (): void {
    $token = 'github-webhook-secret';
    $integration = IntegrationSystem::factory()->create([
        'provider' => 'github',
        'token_hash' => hash('sha256', $token),
        'token_prefix' => substr($token, 0, 8),
    ]);
    $payload = githubNativePullRequestPayload();
    $rawBody = json_encode($payload, JSON_THROW_ON_ERROR);

    $this->call(
        'POST',
        "/api/v1/github-webhooks?token={$token}",
        server: [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
            'HTTP_X_GITHUB_DELIVERY' => '7c4f3d30-42b5-4d4b-9f8f-8d99555d4c15',
            'HTTP_X_GITHUB_EVENT' => 'pull_request',
            'HTTP_X_GITHUB_HOOK_ID' => '987654',
            'HTTP_X_HUB_SIGNATURE_256' => 'sha256='.hash_hmac('sha256', $rawBody, $token),
        ],
        content: $rawBody,
    )->assertOk()
        ->assertJson(fn (AssertableJson $json) => $json
            ->where('data.integration_system_id', $integration->id)
            ->where('data.person_id', null)
            ->where('data.event_id', '7c4f3d30-42b5-4d4b-9f8f-8d99555d4c15')
            ->where('data.event_type', 'pull_request.opened')
            ->where('data.external_actor_code', 'github_user:lucas-github')
            ->where('data.status', 'unmapped_person')
            ->where('data.payload.pull_request.number', 42)
            ->where('data.normalized_payload.source', 'github')
            ->where('data.normalized_payload.delivery_id', '7c4f3d30-42b5-4d4b-9f8f-8d99555d4c15')
            ->where('data.normalized_payload.hook_id', '987654')
            ->where('data.normalized_payload.repository_full_name', '4techlead/api')
            ->where('data.normalized_payload.sender_login', 'lucas-github')
            ->where('data.normalized_payload.pr_number', 42)
            ->where('data.normalized_payload.pr_title', 'DRIE-21919 entregar fluxo de cadastro')
            ->where('data.normalized_payload.pr_state', 'open')
            ->where('data.normalized_payload.pr_draft', false)
            ->where('data.normalized_payload.pr_merged', false)
            ->where('data.normalized_payload.pr_author', 'lucas-github')
            ->where('data.normalized_payload.head_ref', 'feature/DRIE-21919-cadastro-oficina')
            ->where('data.normalized_payload.base_ref', 'main')
            ->where('data.normalized_payload.task_refs.0', 'DRIE-21919')
            ->etc());

    expect(PersonDeliveryMetric::query()->count())->toBe(0);
});

it('receives a signed github webhook through a url without a token or integration id', function (): void {
    $token = 'github-webhook-secret';
    $integration = IntegrationSystem::factory()->create([
        'provider' => 'github',
        'token_hash' => hash('sha256', $token),
        'webhook_secret' => $token,
        'token_prefix' => substr($token, 0, 8),
    ]);
    $payload = githubNativePullRequestPayload();
    $rawBody = json_encode($payload, JSON_THROW_ON_ERROR);

    $this->call(
        'POST',
        '/api/v1/github-webhooks',
        server: [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
            'HTTP_X_GITHUB_DELIVERY' => 'signed-delivery-1',
            'HTTP_X_GITHUB_EVENT' => 'pull_request',
            'HTTP_X_HUB_SIGNATURE_256' => 'sha256='.hash_hmac('sha256', $rawBody, $token),
        ],
        content: $rawBody,
    )->assertOk()
        ->assertJsonPath('data.integration_system_id', $integration->id)
        ->assertJsonPath('data.event_id', 'signed-delivery-1')
        ->assertJsonPath('data.event_type', 'pull_request.opened')
        ->assertJsonPath('data.normalized_payload.source', 'github');

    expect(PersonDeliveryMetric::query()->count())->toBe(0);
});

it('creates delivery metrics from a mapped merged github pull request webhook', function (): void {
    $token = 'github-webhook-secret';
    $integration = IntegrationSystem::factory()->create([
        'provider' => 'github',
        'token_hash' => hash('sha256', $token),
        'webhook_secret' => $token,
        'token_prefix' => substr($token, 0, 8),
    ]);
    $person = Person::factory()->create();
    PersonExternalIdentity::factory()->create([
        'person_id' => $person->id,
        'integration_system_id' => $integration->id,
        'external_code' => 'github_user:lucas-github',
    ]);
    $payload = githubNativePullRequestPayload([
        'action' => 'closed',
        'pull_request' => [
            'state' => 'closed',
            'merged' => true,
            'closed_at' => '2026-08-28T18:00:00Z',
            'merged_at' => '2026-08-28T18:00:00Z',
            'comments' => 8,
            'review_comments' => 5,
            'changed_files' => 12,
            'additions' => 420,
            'deletions' => 80,
        ],
    ]);
    $rawBody = json_encode($payload, JSON_THROW_ON_ERROR);

    $this->call(
        'POST',
        '/api/v1/github-webhooks',
        server: [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
            'HTTP_X_GITHUB_DELIVERY' => 'merged-delivery-1',
            'HTTP_X_GITHUB_EVENT' => 'pull_request',
            'HTTP_X_HUB_SIGNATURE_256' => 'sha256='.hash_hmac('sha256', $rawBody, $token),
        ],
        content: $rawBody,
    )->assertOk()
        ->assertJsonPath('data.person_id', $person->id)
        ->assertJsonPath('data.status', 'processed')
        ->assertJsonPath('data.normalized_payload.changed_lines', 500)
        ->assertJsonPath('data.normalized_payload.pr_merge_time_hours', 32);

    expect(PersonDeliveryMetric::query()->where('person_id', $person->id)->count())->toBe(20);
    expect(
        PersonDeliveryMetric::query()
            ->where('person_id', $person->id)
            ->where('metric_type', 'pull_request_count')
            ->value('metric_value'),
    )->toBe('1.00');
    expect(
        PersonDeliveryMetric::query()
            ->where('person_id', $person->id)
            ->where('metric_type', 'changed_lines_count')
            ->value('metric_value'),
    )->toBe('500.00');
    expect(
        PersonDeliveryMetric::query()
            ->where('person_id', $person->id)
            ->where('metric_type', 'annual_pr_merge_time_average')
            ->value('metric_value'),
    )->toBe('32.00');
});

it('rejects github webhook urls without a token when the signature is missing', function (): void {
    $token = 'github-webhook-secret';
    IntegrationSystem::factory()->create([
        'provider' => 'github',
        'token_hash' => hash('sha256', $token),
        'webhook_secret' => $token,
        'token_prefix' => substr($token, 0, 8),
    ]);

    $this->postJson('/api/v1/github-webhooks', githubNativePullRequestPayload(), [
        'X-GitHub-Delivery' => 'signed-delivery-1',
        'X-GitHub-Event' => 'pull_request',
    ])->assertForbidden();
});

it('does not duplicate github webhook deliveries', function (): void {
    $token = 'github-webhook-secret';
    $integration = IntegrationSystem::factory()->create([
        'provider' => 'github',
        'token_hash' => hash('sha256', $token),
        'token_prefix' => substr($token, 0, 8),
    ]);
    $payload = githubNativePullRequestPayload();

    $this->postJson('/api/v1/github-webhooks', $payload, [
        'Authorization' => "Bearer {$token}",
        'X-GitHub-Delivery' => '7c4f3d30-42b5-4d4b-9f8f-8d99555d4c15',
        'X-GitHub-Event' => 'pull_request',
    ])->assertOk();
    $this->postJson('/api/v1/github-webhooks', $payload, [
        'Authorization' => "Bearer {$token}",
        'X-GitHub-Delivery' => '7c4f3d30-42b5-4d4b-9f8f-8d99555d4c15',
        'X-GitHub-Event' => 'pull_request',
    ])->assertOk();

    expect($integration->webhookEvents()->count())->toBe(1)
        ->and(PersonDeliveryMetric::query()->count())->toBe(0);
});

it('receives github ci and review webhook events as operational lake data', function (): void {
    $token = 'github-webhook-secret';
    IntegrationSystem::factory()->create([
        'provider' => 'github-actions',
        'token_hash' => hash('sha256', $token),
        'token_prefix' => substr($token, 0, 8),
    ]);

    $this->postJson('/api/v1/github-webhooks', githubNativeReviewPayload(), [
        'X-Integration-Token' => $token,
        'X-GitHub-Delivery' => 'review-delivery-1',
        'X-GitHub-Event' => 'pull_request_review',
    ])->assertOk()
        ->assertJsonPath('data.event_id', 'review-delivery-1')
        ->assertJsonPath('data.event_type', 'pull_request_review.submitted')
        ->assertJsonPath('data.external_actor_code', 'github_user:reviewer-github')
        ->assertJsonPath('data.normalized_payload.review_state', 'changes_requested')
        ->assertJsonPath('data.normalized_payload.pr_number', 42)
        ->assertJsonPath('data.normalized_payload.task_refs.0', 'DRIE-21919');

    $this->postJson('/api/v1/github-webhooks', githubNativeCheckRunPayload(), [
        'X-Integration-Token' => $token,
        'X-GitHub-Delivery' => 'check-run-delivery-1',
        'X-GitHub-Event' => 'check_run',
    ])->assertOk()
        ->assertJsonPath('data.event_id', 'check-run-delivery-1')
        ->assertJsonPath('data.event_type', 'check_run.completed')
        ->assertJsonPath('data.normalized_payload.check_run_name', 'tests')
        ->assertJsonPath('data.normalized_payload.check_run_status', 'completed')
        ->assertJsonPath('data.normalized_payload.check_run_conclusion', 'failure')
        ->assertJsonPath('data.normalized_payload.pr_number', 42)
        ->assertJsonPath('data.normalized_payload.head_ref', 'feature/DRIE-21919-cadastro-oficina')
        ->assertJsonPath('data.normalized_payload.task_refs.0', 'DRIE-21919');

    expect(PersonDeliveryMetric::query()->count())->toBe(0);
});

it('rejects github webhooks with an invalid signature', function (): void {
    $token = 'github-webhook-secret';
    IntegrationSystem::factory()->create([
        'provider' => 'github',
        'token_hash' => hash('sha256', $token),
        'token_prefix' => substr($token, 0, 8),
    ]);

    $this->postJson('/api/v1/github-webhooks', githubNativePullRequestPayload(), [
        'Authorization' => "Bearer {$token}",
        'X-GitHub-Delivery' => '7c4f3d30-42b5-4d4b-9f8f-8d99555d4c15',
        'X-GitHub-Event' => 'pull_request',
        'X-Hub-Signature-256' => 'sha256=invalid',
    ])->assertForbidden();
});

it('rejects github webhooks for inactive integrations resolved by token', function (): void {
    $token = 'inactive-github-token';
    IntegrationSystem::factory()->create([
        'provider' => 'github',
        'active' => false,
        'token_hash' => hash('sha256', $token),
        'token_prefix' => substr($token, 0, 8),
    ]);

    $this->postJson('/api/v1/github-webhooks', githubNativePullRequestPayload(), [
        'Authorization' => "Bearer {$token}",
        'X-GitHub-Delivery' => '7c4f3d30-42b5-4d4b-9f8f-8d99555d4c15',
        'X-GitHub-Event' => 'pull_request',
    ])->assertForbidden();
});

/**
 * @return array<string, mixed>
 */
function clickUpAutomationPayload(array $overrides = []): array
{
    return array_replace_recursive([
        'auto_id' => '4ff67264-298b-4639-b0a8-4c066025f4e1:main',
        'date' => '2026-08-27T01:56:00.000Z',
        'trigger_id' => '80c28fd1-2a2c-46a5-a0d6-67b0dbd27633:tasks',
        'payload' => [
            'id' => '86ak1xv8h',
            'custom_id' => 'DRIE-21919',
            'name' => 'Fluxo de cadastro para nova oficina',
            'text_content' => 'Cadastrar oficina nova e validar fluxo completo.',
            'status_id' => 'p90131743905_gBTMnApJ',
            'workspace_id' => '31134301',
            'sprint_points' => 5,
            'url' => 'https://app.clickup.com/t/86ak1xv8h',
            'status' => [
                'id' => 'p90131743905_gBTMnApJ',
                'status' => 'teste de qualidade',
                'color' => '#e16b16',
            ],
            'lists' => [
                [
                    'list_id' => '901328281243',
                    'name' => 'Sprint atual',
                ],
            ],
            'ownership' => [
                'owner' => '230504877',
            ],
            'users' => [
                [
                    'userid' => 230504877,
                    'username' => 'Ronan',
                ],
            ],
        ],
    ], $overrides);
}

/**
 * @return array<string, mixed>
 */
function clickUpApiWebhookPayload(array $overrides = []): array
{
    return array_replace_recursive([
        'webhook_id' => '4b67ac88-5749-4fdb-a975-ec5ed16b66e3',
        'event' => 'taskStatusUpdated',
        'task_id' => '86ak1xv8h',
        'history_items' => [
            [
                'id' => 'hist_123',
                'date' => '1787795760000',
                'field' => 'status',
                'before' => 'open',
                'after' => 'done',
                'user' => [
                    'id' => 230504877,
                    'username' => 'Ronan',
                ],
            ],
        ],
    ], $overrides);
}

/**
 * @return array<string, mixed>
 */
function githubNativePullRequestPayload(array $overrides = []): array
{
    return array_replace_recursive([
        'action' => 'opened',
        'number' => 42,
        'repository' => [
            'id' => 1001,
            'full_name' => '4techlead/api',
        ],
        'organization' => [
            'login' => '4techlead',
        ],
        'sender' => [
            'login' => 'lucas-github',
        ],
        'pull_request' => [
            'id' => 424242,
            'number' => 42,
            'title' => 'DRIE-21919 entregar fluxo de cadastro',
            'body' => 'Implementa cadastro da tarefa DRIE-21919.',
            'state' => 'open',
            'draft' => false,
            'merged' => false,
            'created_at' => '2026-08-27T10:00:00Z',
            'updated_at' => '2026-08-27T10:05:00Z',
            'closed_at' => null,
            'merged_at' => null,
            'user' => [
                'login' => 'lucas-github',
            ],
            'head' => [
                'ref' => 'feature/DRIE-21919-cadastro-oficina',
                'sha' => 'abc123',
            ],
            'base' => [
                'ref' => 'main',
            ],
        ],
    ], $overrides);
}

/**
 * @return array<string, mixed>
 */
function githubNativeReviewPayload(array $overrides = []): array
{
    return array_replace_recursive(githubNativePullRequestPayload([
        'action' => 'submitted',
        'sender' => [
            'login' => 'reviewer-github',
        ],
        'review' => [
            'id' => 9001,
            'state' => 'changes_requested',
            'submitted_at' => '2026-08-27T11:00:00Z',
            'user' => [
                'login' => 'reviewer-github',
            ],
        ],
    ]), $overrides);
}

/**
 * @return array<string, mixed>
 */
function githubNativeCheckRunPayload(array $overrides = []): array
{
    return array_replace_recursive([
        'action' => 'completed',
        'repository' => [
            'id' => 1001,
            'full_name' => '4techlead/api',
        ],
        'sender' => [
            'login' => 'github-actions[bot]',
        ],
        'check_run' => [
            'id' => 7001,
            'name' => 'tests',
            'status' => 'completed',
            'conclusion' => 'failure',
            'head_branch' => 'feature/DRIE-21919-cadastro-oficina',
            'head_sha' => 'abc123',
            'pull_requests' => [
                [
                    'number' => 42,
                ],
            ],
        ],
    ], $overrides);
}
