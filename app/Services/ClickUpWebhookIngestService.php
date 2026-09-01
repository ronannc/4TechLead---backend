<?php

namespace App\Services;

use App\Models\IntegrationSystem;
use App\Models\IntegrationWebhookEvent;
use App\Models\PersonExternalIdentity;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Throwable;

final class ClickUpWebhookIngestService
{
    public function __construct(private readonly DeliveryMetricIngestService $metricIngestService) {}

    /**
     * @param  array<string, mixed>  $payload
     *
     * @throws Throwable
     */
    public function ingest(string $token, array $payload): IntegrationWebhookEvent
    {
        $integrationSystem = $this->integrationSystem($token);

        return $this->ingestForIntegration($integrationSystem, $token, $payload);
    }

    /**
     * @param  array<string, mixed>  $payload
     *
     * @throws Throwable
     */
    public function ingestForIntegration(IntegrationSystem $integrationSystem, string $token, array $payload): IntegrationWebhookEvent
    {
        $this->assertCanReceive($integrationSystem, $token);

        return DB::transaction(function () use ($integrationSystem, $payload): IntegrationWebhookEvent {
            $normalizedPayload = $this->normalize($payload);
            $identity = $this->identityFor($integrationSystem, $normalizedPayload['external_actor_code']);

            $event = IntegrationWebhookEvent::query()->createOrFirst(
                [
                    'integration_system_id' => $integrationSystem->id,
                    'event_id' => $normalizedPayload['event_id'],
                ],
                [
                    'tenant_id' => $integrationSystem->tenant_id,
                    'person_id' => $identity?->person_id,
                    'event_type' => $normalizedPayload['event_type'],
                    'external_actor_code' => $normalizedPayload['external_actor_code'],
                    'status' => $identity === null ? 'unmapped_person' : 'processed',
                    'failure_reason' => $identity === null ? 'No active person mapping for external code.' : null,
                    'payload' => $payload,
                    'normalized_payload' => $normalizedPayload,
                    'received_at' => now(),
                ],
            );

            if ($event->wasRecentlyCreated) {
                $integrationSystem->forceFill(['last_received_at' => now()])->save();

                if ($identity !== null) {
                    $this->metricIngestService->createClickUpTaskMetrics($event, $normalizedPayload);
                }
            }

            return $event->refresh();
        });
    }

    protected function integrationSystem(string $token): IntegrationSystem
    {
        $integrationSystem = IntegrationSystem::query()
            ->where('provider', 'clickup')
            ->where('token_hash', hash('sha256', $token))
            ->first();

        if ($integrationSystem === null) {
            throw new UnauthorizedHttpException('Bearer', 'Invalid integration token.');
        }

        return $integrationSystem;
    }

    protected function assertCanReceive(IntegrationSystem $integrationSystem, string $token): void
    {
        if ($integrationSystem->provider !== 'clickup') {
            throw new AccessDeniedHttpException('Integration provider must be clickup.');
        }

        if (! $integrationSystem->active) {
            throw new AccessDeniedHttpException('Integration is inactive.');
        }

        if ($token === '' || ! hash_equals($integrationSystem->token_hash, hash('sha256', $token))) {
            throw new UnauthorizedHttpException('Bearer', 'Invalid integration token.');
        }
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    protected function normalize(array $payload): array
    {
        $task = (array) Arr::get($payload, 'payload', []);
        $historyItem = (array) Arr::first((array) Arr::get($payload, 'history_items', []));
        $user = (array) Arr::get($historyItem, 'user', []);
        $eventType = (string) ($payload['event'] ?? 'clickup_automation');
        $occurredAt = $this->occurredAt($payload, $historyItem, $task);
        $taskId = $payload['task_id'] ?? Arr::get($task, 'id');
        $customId = Arr::get($task, 'custom_id');
        $webhookId = $payload['webhook_id'] ?? null;
        $historyItemId = Arr::get($historyItem, 'id');

        return [
            'source' => 'clickup',
            'event_id' => $this->eventId($payload, $eventType, $taskId, $webhookId, $historyItemId),
            'event_type' => $eventType,
            'external_actor_code' => $this->externalActorCode($payload, $historyItem, $task),
            'occurred_at' => $occurredAt,
            'webhook_id' => $webhookId,
            'trigger_id' => $payload['trigger_id'] ?? null,
            'automation_id' => $payload['auto_id'] ?? null,
            'workspace_id' => $payload['workspace_id'] ?? Arr::get($task, 'workspace_id'),
            'task_id' => $taskId,
            'task_custom_id' => $customId,
            'task_name' => Arr::get($task, 'name'),
            'task_text_content' => Arr::get($task, 'text_content'),
            'task_status' => Arr::get($task, 'status.status', Arr::get($task, 'status')),
            'task_status_id' => Arr::get($task, 'status_id'),
            'task_sprint_points' => Arr::get($task, 'sprint_points'),
            'task_refs' => array_values(array_filter([(string) $customId])),
            'source_ref' => Arr::get($task, 'url'),
            'list_ids' => $this->listIds($task, $payload),
            'history_item_id' => $historyItemId,
            'history_field' => Arr::get($historyItem, 'field'),
            'history_before' => Arr::get($historyItem, 'before'),
            'history_after' => Arr::get($historyItem, 'after'),
            'user_id' => Arr::get($user, 'id'),
            'user_name' => Arr::get($user, 'username'),
            'task_url' => Arr::get($task, 'url'),
        ];
    }

    /**
     * @param  array<string, mixed>  $payload
     * @param  array<string, mixed>  $historyItem
     * @param  array<string, mixed>  $task
     */
    protected function occurredAt(array $payload, array $historyItem, array $task): ?string
    {
        $value = $payload['date']
            ?? Arr::get($historyItem, 'date')
            ?? Arr::get($task, 'time_mgmt.date_updated')
            ?? Arr::get($task, 'date_updated');

        if ($value === null || $value === '') {
            return null;
        }

        if (is_numeric($value)) {
            return Carbon::createFromTimestampMs((int) $value)->toISOString();
        }

        try {
            return Carbon::parse((string) $value)->toISOString();
        } catch (Throwable) {
            return null;
        }
    }

    protected function eventId(
        array $payload,
        string $eventType,
        mixed $taskId,
        mixed $webhookId,
        mixed $historyItemId,
    ): string {
        if (isset($payload['trigger_id']) && $payload['trigger_id'] !== '') {
            return (string) $payload['trigger_id'];
        }

        if ($webhookId !== null && $historyItemId !== null) {
            return $webhookId.':'.$historyItemId;
        }

        if ($webhookId !== null && $taskId !== null) {
            return $webhookId.':'.$eventType.':'.$taskId;
        }

        return hash('sha256', json_encode($payload, JSON_THROW_ON_ERROR));
    }

    /**
     * @param  array<string, mixed>  $payload
     * @param  array<string, mixed>  $historyItem
     * @param  array<string, mixed>  $task
     */
    protected function externalActorCode(array $payload, array $historyItem, array $task): ?string
    {
        $userId = Arr::get($historyItem, 'user.id')
            ?? Arr::get($task, 'ownership.owner')
            ?? Arr::get((array) Arr::first((array) Arr::get($task, 'users', [])), 'userid')
            ?? $payload['user_id']
            ?? null;

        return $userId === null ? null : 'clickup_user:'.$userId;
    }

    protected function identityFor(IntegrationSystem $integrationSystem, mixed $externalCode): ?PersonExternalIdentity
    {
        if (! is_string($externalCode) || $externalCode === '') {
            return null;
        }

        return PersonExternalIdentity::query()
            ->where('integration_system_id', $integrationSystem->id)
            ->where('external_code', $externalCode)
            ->where('active', true)
            ->first();
    }

    /**
     * @param  array<string, mixed>  $task
     * @param  array<string, mixed>  $payload
     * @return array<int, string>
     */
    protected function listIds(array $task, array $payload): array
    {
        $lists = Arr::get($task, 'lists');

        if (is_array($lists)) {
            return array_values(array_filter(array_map(
                fn (mixed $list): ?string => is_array($list) && isset($list['list_id'])
                    ? (string) $list['list_id']
                    : null,
                $lists,
            )));
        }

        return isset($payload['list_id']) ? [(string) $payload['list_id']] : [];
    }
}
