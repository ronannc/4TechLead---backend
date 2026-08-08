<?php

namespace App\Services;

use App\Models\IntegrationSystem;
use App\Models\IntegrationWebhookEvent;
use App\Models\PersonDeliveryMetric;
use App\Models\PersonExternalIdentity;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class IntegrationWebhookIngestService
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function ingest(IntegrationSystem $integrationSystem, string $token, array $data): IntegrationWebhookEvent
    {
        $this->assertCanReceive($integrationSystem, $token);

        return DB::transaction(function () use ($integrationSystem, $data): IntegrationWebhookEvent {
            $identity = PersonExternalIdentity::query()
                ->where('integration_system_id', $integrationSystem->id)
                ->where('external_code', $data['external_actor_code'])
                ->where('active', true)
                ->first();

            $normalizedPayload = $this->normalize($data);

            $event = IntegrationWebhookEvent::query()->createOrFirst(
                [
                    'integration_system_id' => $integrationSystem->id,
                    'event_id' => $data['event_id'],
                ],
                [
                    'person_id' => $identity?->person_id,
                    'event_type' => $data['event_type'],
                    'external_actor_code' => $data['external_actor_code'],
                    'status' => $identity === null ? 'unmapped_person' : 'processed',
                    'failure_reason' => $identity === null ? 'No active person mapping for external code.' : null,
                    'payload' => $data['payload'],
                    'normalized_payload' => $normalizedPayload,
                    'received_at' => now(),
                ],
            );

            if (! $event->wasRecentlyCreated) {
                return $event;
            }

            $integrationSystem->forceFill(['last_received_at' => now()])->save();

            if ($identity !== null) {
                $this->createMetrics($event, $normalizedPayload, $data);
            }

            return $event->refresh();
        });
    }

    protected function assertCanReceive(IntegrationSystem $integrationSystem, string $token): void
    {
        if (! $integrationSystem->active) {
            throw new AccessDeniedHttpException('Integration is inactive.');
        }

        if ($token === '' || ! hash_equals($integrationSystem->token_hash, hash('sha256', $token))) {
            throw new UnauthorizedHttpException('Bearer', 'Invalid integration token.');
        }
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function normalize(array $data): array
    {
        $payload = $data['payload'];
        $pullRequest = (array) Arr::get($payload, 'pull_request', []);
        $reviewComments = (int) $this->number($pullRequest, $payload, 'review_comments_count');
        $comments = (int) $this->number($pullRequest, $payload, 'comments_count');
        $ciFailures = (int) $this->number($pullRequest, $payload, 'ci_failures_count');
        $rework = (int) $this->number($pullRequest, $payload, 'rework_count');
        $changedFiles = (int) $this->number($pullRequest, $payload, 'changed_files');
        $storyPoints = $this->number($pullRequest, $payload, 'story_points');

        return [
            'event_type' => $data['event_type'],
            'source_ref' => $data['source_ref'] ?? Arr::get($pullRequest, 'url'),
            'occurred_at' => $data['occurred_at'] ?? Arr::get($pullRequest, 'merged_at'),
            'task_refs' => Arr::get($payload, 'task_refs', Arr::get($pullRequest, 'task_refs', [])),
            'quality_score' => $this->qualityScore(
                reviewComments: $reviewComments,
                ciFailures: $ciFailures,
                rework: $rework,
            ),
            'review_comments_count' => $reviewComments,
            'comments_count' => $comments,
            'ci_failures_count' => $ciFailures,
            'rework_count' => $rework,
            'story_points' => $storyPoints,
            'changed_files' => $changedFiles,
            'additions' => $this->number($pullRequest, $payload, 'additions'),
            'deletions' => $this->number($pullRequest, $payload, 'deletions'),
        ];
    }

    /**
     * @param  array<string, mixed>  $pullRequest
     * @param  array<string, mixed>  $payload
     */
    protected function number(array $pullRequest, array $payload, string $key): float|int
    {
        $value = Arr::get($pullRequest, $key, Arr::get($payload, $key, 0));

        return is_numeric($value) ? $value + 0 : 0;
    }

    protected function qualityScore(int $reviewComments, int $ciFailures, int $rework): int
    {
        return max(0, min(100, 100 - ($ciFailures * 15) - ($reviewComments * 2) - ($rework * 20)));
    }

    /**
     * @param  array<string, mixed>  $normalizedPayload
     * @param  array<string, mixed>  $data
     */
    protected function createMetrics(
        IntegrationWebhookEvent $event,
        array $normalizedPayload,
        array $data,
    ): void {
        $metrics = [
            ['code_quality_score', $normalizedPayload['quality_score'], 'score'],
            ['pull_request_count', 1, 'pr'],
            ['review_comments_count', $normalizedPayload['review_comments_count'], 'comments'],
            ['ci_failures_count', $normalizedPayload['ci_failures_count'], 'failures'],
            ['rework_count', $normalizedPayload['rework_count'], 'times'],
            ['changed_files_count', $normalizedPayload['changed_files'], 'files'],
        ];

        if ((float) $normalizedPayload['story_points'] > 0) {
            $metrics[] = ['delivery_points', $normalizedPayload['story_points'], 'points'];
        }

        foreach ($metrics as [$type, $value, $unit]) {
            PersonDeliveryMetric::query()->createOrFirst([
                'integration_webhook_event_id' => $event->id,
                'metric_type' => $type,
            ], [
                'person_id' => $event->person_id,
                'integration_system_id' => $event->integration_system_id,
                'metric_value' => $value,
                'unit' => $unit,
                'source_ref' => $normalizedPayload['source_ref'] ?? $data['source_ref'] ?? null,
                'occurred_at' => $normalizedPayload['occurred_at'] ?? $data['occurred_at'] ?? null,
                'metadata' => [
                    'event_type' => $event->event_type,
                    'task_refs' => $normalizedPayload['task_refs'],
                ],
            ]);
        }
    }
}
