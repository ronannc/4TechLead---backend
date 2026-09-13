<?php

namespace App\Services;

use App\Enums\DeliveryStage;
use App\Models\IntegrationSystem;
use App\Models\IntegrationWebhookEvent;
use App\Models\Person;
use App\Models\PersonExternalIdentity;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
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
        $normalizedPayload = $this->normalize($payload);
        $existingEvent = IntegrationWebhookEvent::query()
            ->where('integration_system_id', $integrationSystem->id)
            ->where('event_id', $normalizedPayload['event_id'])
            ->first();

        if ($existingEvent !== null) {
            app(DeliveryCaseProjector::class)->project($existingEvent);
            app(DeliveryMetricIngestService::class)->ingest($existingEvent);

            return $existingEvent->refresh();
        }

        try {
            $normalizedPayload = $this->enrichTaskSnapshot($integrationSystem, $normalizedPayload);
        } catch (Throwable $exception) {
            $normalizedPayload['task_enrichment_status'] = 'failed';
            $normalizedPayload['task_enrichment_error'] = class_basename($exception);
        }

        $event = DB::transaction(function () use ($integrationSystem, $normalizedPayload, $rawBody): IntegrationWebhookEvent {
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
            }

            return $event->refresh();
        });

        app(DeliveryCaseProjector::class)->project($event);
        app(DeliveryMetricIngestService::class)->ingest($event);

        return $event->refresh();
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
        $historyField = Arr::get($historyItem, 'field');
        $historyBefore = $this->historyValue(Arr::get($historyItem, 'before'), $historyField);
        $historyAfter = $this->historyValue(Arr::get($historyItem, 'after'), $historyField);
        $taskStatus = $this->statusValue(Arr::get($task, 'status'));
        $taskAssignees = $this->taskAssignees($task);
        $taskTags = $this->tagNames(Arr::get($task, 'tags', []));
        $assigneeChange = in_array($historyField, ['assignee', 'assignees'], true);
        $tagChange = $historyField === 'tag';

        if ($taskAssignees === [] && $assigneeChange) {
            $taskAssignees = (array) $historyAfter;
        }

        if ($taskTags === [] && $tagChange) {
            $taskTags = (array) $historyAfter;
        }

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
            'task_status' => $taskStatus,
            'task_stage' => DeliveryStage::fromClickUpStatus($taskStatus)?->value,
            'task_status_id' => Arr::get($task, 'status_id'),
            'task_sprint_points' => Arr::get($task, 'sprint_points', Arr::get($task, 'points')),
            'task_assignees' => $taskAssignees,
            'task_assignees_authoritative' => $taskAssignees !== [] || $assigneeChange,
            'task_assignees_source' => $taskAssignees === [] ? null : ($assigneeChange ? 'history_change' : 'webhook'),
            'task_tags' => $taskTags,
            'task_tags_authoritative' => array_key_exists('tags', $task) || $tagChange,
            'task_tags_source' => $taskTags === [] ? null : ($tagChange ? 'history_change' : 'webhook'),
            'task_refs' => array_values(array_filter([(string) $customId])),
            'source_ref' => Arr::get($task, 'url'),
            'list_ids' => $this->listIds($task, $payload),
            'history_item_id' => $historyItemId,
            'history_field' => $historyField,
            'history_before' => $historyBefore,
            'history_after' => $historyAfter,
            'history_before_stage' => $historyField === 'status'
                ? DeliveryStage::fromClickUpStatus($historyBefore)?->value
                : null,
            'history_after_stage' => $historyField === 'status'
                ? DeliveryStage::fromClickUpStatus($historyAfter)?->value
                : null,
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
                'stage' => $normalizedPayload['task_stage'],
                'status_id' => $normalizedPayload['task_status_id'],
                'sprint_points' => $normalizedPayload['task_sprint_points'],
                'url' => $normalizedPayload['task_url'],
                'assignees' => $normalizedPayload['task_assignees'],
                'tags' => $normalizedPayload['task_tags'],
                'enrichment_status' => $normalizedPayload['task_enrichment_status'] ?? null,
                'enrichment_error' => $normalizedPayload['task_enrichment_error'] ?? null,
            ], fn (mixed $value): bool => $value !== null && $value !== '' && $value !== []),
            'change' => array_filter([
                'history_item_id' => $normalizedPayload['history_item_id'],
                'field' => $normalizedPayload['history_field'],
                'before' => $normalizedPayload['history_before'],
                'after' => $normalizedPayload['history_after'],
                'before_stage' => $normalizedPayload['history_before_stage'],
                'after_stage' => $normalizedPayload['history_after_stage'],
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

        $listId = Arr::get($task, 'list.id', $payload['list_id'] ?? null);

        return $listId === null || $listId === '' ? [] : [(string) $listId];
    }

    protected function statusValue(mixed $status): ?string
    {
        if (is_string($status) && $status !== '') {
            return $status;
        }

        if (! is_array($status)) {
            return null;
        }

        $value = Arr::get($status, 'status', Arr::get($status, 'name', Arr::get($status, 'value')));

        return is_string($value) && $value !== '' ? $value : null;
    }

    protected function historyValue(mixed $value, mixed $field): mixed
    {
        if ($field === 'status') {
            return $this->statusValue($value);
        }

        if ($field === 'tag') {
            return $this->tagNames($value);
        }

        if (in_array($field, ['assignee', 'assignees'], true)) {
            return $this->assigneeHistory($value);
        }

        return $value;
    }

    /**
     * @return array<int, array{external_code: string, id: string, name?: string}>
     */
    protected function assigneeHistory(mixed $value): array
    {
        if ($value === null || $value === '') {
            return [];
        }

        $users = is_array($value) && ! Arr::isAssoc($value) ? $value : [$value];
        $normalizedUsers = array_map(function (mixed $user): array {
            if (is_array($user)) {
                return $user;
            }

            return ['id' => $user];
        }, $users);

        return $this->taskAssignees(['users' => $normalizedUsers]);
    }

    /**
     * @return array<int, string>
     */
    protected function tagNames(mixed $tags): array
    {
        if (! is_array($tags)) {
            return [];
        }

        if (Arr::isAssoc($tags)) {
            $tags = [$tags];
        }

        return array_values(array_unique(array_filter(array_map(
            function (mixed $tag): ?string {
                if (is_string($tag) && $tag !== '') {
                    return $tag;
                }

                if (! is_array($tag)) {
                    return null;
                }

                $name = Arr::get($tag, 'name', Arr::get($tag, 'tag'));

                return is_string($name) && $name !== '' ? $name : null;
            },
            $tags,
        ))));
    }

    /**
     * @param  array<string, mixed>  $task
     * @return array<int, array{external_code: string, id: string, name?: string}>
     */
    protected function taskAssignees(array $task): array
    {
        $users = Arr::get($task, 'users', Arr::get($task, 'assignees', []));

        if (! is_array($users)) {
            return [];
        }

        $assignees = [];

        foreach ($users as $user) {
            if (! is_array($user)) {
                continue;
            }

            $id = Arr::get($user, 'userid', Arr::get($user, 'id'));

            if ($id === null || $id === '') {
                continue;
            }

            $assignee = [
                'external_code' => 'clickup_user:'.$id,
                'id' => (string) $id,
            ];
            $name = Arr::get($user, 'username', Arr::get($user, 'name'));

            if (is_string($name) && $name !== '') {
                $assignee['name'] = $name;
            }

            $assignees[] = $assignee;
        }

        return $assignees;
    }

    /**
     * @param  array<string, mixed>  $normalizedPayload
     * @return array<int, array{external_code: string, id: string, name?: string}>
     */
    protected function enrichTaskSnapshot(IntegrationSystem $integrationSystem, array $normalizedPayload): array
    {
        $taskId = $normalizedPayload['task_id'];

        if ($taskId === null || $taskId === '') {
            return $normalizedPayload;
        }

        $latestKnownSnapshot = IntegrationWebhookEvent::query()
            ->where('tenant_id', $integrationSystem->tenant_id)
            ->where('integration_system_id', $integrationSystem->id)
            ->whereNotNull('normalized_payload')
            ->where('normalized_payload->task_id', (string) $taskId)
            ->orderByDesc('received_at')
            ->orderByDesc('id')
            ->limit(100)
            ->get()
            ->first(function (IntegrationWebhookEvent $event) use ($taskId, $normalizedPayload): bool {
                return (string) data_get($event->normalized_payload, 'task_id') === (string) $taskId
                    && $this->snapshotIsNotAfter($event->normalized_payload ?? [], $normalizedPayload);
            })
            ?->normalized_payload ?? [];

        $normalizedPayload = $this->mergeMissingTaskSnapshot($normalizedPayload, $latestKnownSnapshot);

        if (! $this->needsProviderEnrichment($normalizedPayload)) {
            return $normalizedPayload;
        }

        return $this->mergeMissingTaskSnapshot(
            $normalizedPayload,
            $this->fetchTaskSnapshot($integrationSystem, (string) $taskId),
        );
    }

    /**
     * @param  array<string, mixed>  $normalizedPayload
     * @param  array<string, mixed>  $snapshot
     * @return array<string, mixed>
     */
    protected function mergeMissingTaskSnapshot(array $normalizedPayload, array $snapshot): array
    {
        foreach (['task_enrichment_status', 'task_enrichment_error', 'task_snapshot_observed_at'] as $field) {
            if (array_key_exists($field, $snapshot)) {
                $normalizedPayload[$field] = $snapshot[$field];
            }
        }

        foreach ([
            'task_custom_id',
            'task_name',
            'task_status',
            'task_stage',
            'task_sprint_points',
            'task_url',
            'source_ref',
        ] as $field) {
            if (($normalizedPayload[$field] ?? null) === null || $normalizedPayload[$field] === '') {
                $normalizedPayload[$field] = $snapshot[$field] ?? null;
            }
        }

        foreach (['task_assignees', 'task_tags', 'list_ids'] as $field) {
            $authoritativeField = match ($field) {
                'task_assignees' => 'task_assignees_authoritative',
                'task_tags' => 'task_tags_authoritative',
                default => null,
            };
            $isAuthoritative = $authoritativeField !== null
                && ($normalizedPayload[$authoritativeField] ?? false) === true;

            if (($normalizedPayload[$field] ?? []) === [] && ! $isAuthoritative) {
                $normalizedPayload[$field] = (array) ($snapshot[$field] ?? []);

                if ($field !== 'list_ids' && $normalizedPayload[$field] !== []) {
                    $sourceField = $field.'_source';
                    $normalizedPayload[$sourceField] = $snapshot[$sourceField] ?? 'cached_event';
                }
            }
        }

        if (($normalizedPayload['task_refs'] ?? []) === [] && ($normalizedPayload['task_custom_id'] ?? null) !== null) {
            $normalizedPayload['task_refs'] = [(string) $normalizedPayload['task_custom_id']];
        }

        return $normalizedPayload;
    }

    /**
     * @param  array<string, mixed>  $candidate
     * @param  array<string, mixed>  $current
     */
    protected function snapshotIsNotAfter(array $candidate, array $current): bool
    {
        $candidateOccurredAt = $candidate['occurred_at'] ?? null;
        $currentOccurredAt = $current['occurred_at'] ?? null;

        if ($candidateOccurredAt === null || $currentOccurredAt === null) {
            return true;
        }

        try {
            return Carbon::parse((string) $candidateOccurredAt)
                ->lessThanOrEqualTo(Carbon::parse((string) $currentOccurredAt));
        } catch (Throwable) {
            return false;
        }
    }

    /**
     * @param  array<string, mixed>  $normalizedPayload
     */
    protected function needsProviderEnrichment(array $normalizedPayload): bool
    {
        return ($normalizedPayload['task_custom_id'] ?? null) === null
            || ($normalizedPayload['task_name'] ?? null) === null
            || ($normalizedPayload['task_status'] ?? null) === null
            || ($normalizedPayload['task_url'] ?? null) === null
            || ($normalizedPayload['list_ids'] ?? []) === []
            || ($normalizedPayload['task_tags_authoritative'] ?? false) !== true
            || (($normalizedPayload['task_assignees'] ?? []) === []
                && ($normalizedPayload['task_assignees_authoritative'] ?? false) !== true);
    }

    /**
     * @return array<string, mixed>
     */
    protected function fetchTaskSnapshot(IntegrationSystem $integrationSystem, string $taskId): array
    {
        $token = $integrationSystem->provider_api_token;

        if (! is_string($token) || $token === '') {
            return [];
        }

        try {
            $response = Http::withHeaders(['Authorization' => $token])
                ->acceptJson()
                ->timeout(5)
                ->connectTimeout(2)
                ->get("https://api.clickup.com/api/v2/task/{$taskId}");
        } catch (ConnectionException) {
            return ['task_enrichment_status' => 'connection_failed'];
        }

        if (! $response->successful()) {
            return [
                'task_enrichment_status' => 'http_error',
                'task_enrichment_error' => 'http_'.$response->status(),
            ];
        }

        $task = (array) $response->json();
        $taskStatus = $this->statusValue(Arr::get($task, 'status'));

        return [
            'task_enrichment_status' => 'enriched',
            'task_snapshot_observed_at' => now()->toISOString(),
            'task_custom_id' => Arr::get($task, 'custom_id'),
            'task_name' => Arr::get($task, 'name'),
            'task_status' => $taskStatus,
            'task_stage' => DeliveryStage::fromClickUpStatus($taskStatus)?->value,
            'task_sprint_points' => Arr::get($task, 'sprint_points', Arr::get($task, 'points')),
            'task_assignees' => $this->taskAssignees($task),
            'task_assignees_source' => 'provider_api',
            'task_tags' => $this->tagNames(Arr::get($task, 'tags', [])),
            'task_tags_source' => 'provider_api',
            'task_url' => Arr::get($task, 'url'),
            'source_ref' => Arr::get($task, 'url'),
            'list_ids' => $this->listIds($task, []),
        ];
    }
}
