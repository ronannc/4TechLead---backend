<?php

namespace App\Services;

use App\Models\IntegrationSystem;
use App\Models\IntegrationWebhookEvent;
use App\Models\Person;
use App\Models\PersonExternalIdentity;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Throwable;

final class ClickUpWebhookIngestService
{
    /**
     * @param  array<string, mixed>  $payload
     *
     * @throws Throwable
     */
    public function ingest(string $token, array $payload, string $rawBody): IntegrationWebhookEvent
    {
        $integrationSystem = $this->integrationSystem($token);

        $this->assertCanReceive($integrationSystem, $token);

        return $this->storeEvent($integrationSystem, $payload, $rawBody);
    }

    /**
     * @param  array<string, mixed>  $payload
     *
     * @throws Throwable
     */
    public function ingestSigned(array $payload, string $rawBody, string $signature): IntegrationWebhookEvent
    {
        $integrationSystem = $this->integrationSystemBySignature($rawBody, $signature);

        return $this->storeEvent($integrationSystem, $payload, $rawBody);
    }

    /**
     * @param  array<string, mixed>  $payload
     *
     * @throws Throwable
     */
    public function ingestForIntegration(IntegrationSystem $integrationSystem, string $token, array $payload, string $rawBody): IntegrationWebhookEvent
    {
        $this->assertCanReceive($integrationSystem, $token);

        return $this->storeEvent($integrationSystem, $payload, $rawBody);
    }

    /**
     * @param  array<string, mixed>  $payload
     *
     * @throws Throwable
     */
    protected function storeEvent(IntegrationSystem $integrationSystem, array $payload, string $rawBody): IntegrationWebhookEvent
    {
        return DB::transaction(function () use ($integrationSystem, $payload, $rawBody): IntegrationWebhookEvent {
            $normalizedPayload = $this->normalize($payload);
            $personId = $this->personIdFor($integrationSystem, $normalizedPayload['external_actor_code']);

            $event = IntegrationWebhookEvent::query()->createOrFirst(
                [
                    'integration_system_id' => $integrationSystem->id,
                    'event_id' => $normalizedPayload['event_id'],
                ],
                [
                    'tenant_id' => $integrationSystem->tenant_id,
                    'person_id' => $personId,
                    'event_type' => $normalizedPayload['event_type'],
                    'external_actor_code' => $normalizedPayload['external_actor_code'],
                    'status' => $personId === null ? 'unmapped_person' : 'processed',
                    'failure_reason' => $personId === null ? 'No active person mapping for external code.' : null,
                    'payload' => $this->processedPayload($normalizedPayload),
                    'payload_hash' => hash('sha256', $rawBody),
                    'payload_size_bytes' => strlen($rawBody),
                    'normalized_payload' => $normalizedPayload,
                    'received_at' => now(),
                ],
            );

            if ($event->wasRecentlyCreated) {
                $integrationSystem->forceFill(['last_received_at' => now()])->save();
                app(DeliveryMetricIngestService::class)->ingest($event);
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

    protected function integrationSystemBySignature(string $rawBody, string $signature): IntegrationSystem
    {
        if ($signature === '') {
            throw new AccessDeniedHttpException('Missing ClickUp signature.');
        }

        $normalizedSignature = str_starts_with($signature, 'sha256=')
            ? substr($signature, strlen('sha256='))
            : $signature;

        $integrationSystems = IntegrationSystem::query()
            ->where('provider', 'clickup')
            ->where('active', true)
            ->whereNotNull('webhook_secret')
            ->get();

        foreach ($integrationSystems as $integrationSystem) {
            $secret = $integrationSystem->webhook_secret;

            if (! is_string($secret) || $secret === '') {
                continue;
            }

            if (hash_equals(hash_hmac('sha256', $rawBody, $secret), $normalizedSignature)) {
                return $integrationSystem;
            }
        }

        throw new AccessDeniedHttpException('Invalid ClickUp signature.');
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
        if ($eventType === 'clickup_automation') {
            return hash('sha256', json_encode($payload, JSON_THROW_ON_ERROR));
        }

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

    protected function personIdFor(IntegrationSystem $integrationSystem, mixed $externalCode): ?int
    {
        $identity = $this->identityFor($integrationSystem, $externalCode);

        if ($identity !== null) {
            return $identity->person_id;
        }

        if (! is_string($externalCode) || ! str_starts_with($externalCode, 'clickup_user:')) {
            return null;
        }

        $clickUpUserId = substr($externalCode, strlen('clickup_user:'));

        if ($clickUpUserId === '') {
            return null;
        }

        return Person::query()
            ->where('tenant_id', $integrationSystem->tenant_id)
            ->where('clickup_user_id', $clickUpUserId)
            ->value('id');
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
     * @param  array<string, mixed>  $normalizedPayload
     * @return array<string, mixed>
     */
    protected function processedPayload(array $normalizedPayload): array
    {
        return array_filter([
            'source' => $normalizedPayload['source'],
            'event_id' => $normalizedPayload['event_id'],
            'event_type' => $normalizedPayload['event_type'],
            'occurred_at' => $normalizedPayload['occurred_at'],
            'workspace_id' => $normalizedPayload['workspace_id'],
            'task' => array_filter([
                'id' => $normalizedPayload['task_id'],
                'custom_id' => $normalizedPayload['task_custom_id'],
                'name' => $normalizedPayload['task_name'],
                'status' => $normalizedPayload['task_status'],
                'status_id' => $normalizedPayload['task_status_id'],
                'sprint_points' => $normalizedPayload['task_sprint_points'],
                'url' => $normalizedPayload['task_url'],
            ], fn (mixed $value): bool => $value !== null && $value !== ''),
            'change' => array_filter([
                'history_item_id' => $normalizedPayload['history_item_id'],
                'field' => $normalizedPayload['history_field'],
                'before' => $normalizedPayload['history_before'],
                'after' => $normalizedPayload['history_after'],
            ], fn (mixed $value): bool => $value !== null && $value !== ''),
            'actor' => array_filter([
                'external_code' => $normalizedPayload['external_actor_code'],
                'id' => $normalizedPayload['user_id'],
                'name' => $normalizedPayload['user_name'],
            ], fn (mixed $value): bool => $value !== null && $value !== ''),
            'list_ids' => $normalizedPayload['list_ids'],
            'task_refs' => $normalizedPayload['task_refs'],
            'source_ref' => $normalizedPayload['source_ref'],
        ], fn (mixed $value): bool => $value !== null && $value !== [] && $value !== '');
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
