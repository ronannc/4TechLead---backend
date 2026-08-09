<?php

namespace App\Services;

use App\Models\IntegrationSystem;
use App\Models\IntegrationWebhookEvent;
use App\Models\PersonDeliveryMetric;
use App\Models\PersonExternalIdentity;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class IntegrationWebhookIngestService
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function ingestByToken(string $token, array $data): IntegrationWebhookEvent
    {
        $integrationSystem = IntegrationSystem::query()
            ->where('token_hash', hash('sha256', $token))
            ->first();

        if ($integrationSystem === null) {
            throw new UnauthorizedHttpException('Bearer', 'Invalid integration token.');
        }

        return $this->ingest($integrationSystem, $token, $data);
    }

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
                $this->recalculatePersonStatistics($event);
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
        $additions = $this->number($pullRequest, $payload, 'additions');
        $deletions = $this->number($pullRequest, $payload, 'deletions');
        $storyPoints = $this->number($pullRequest, $payload, 'story_points');
        $createdAt = Arr::get($pullRequest, 'created_at');
        $mergedAt = Arr::get($pullRequest, 'merged_at', $data['occurred_at'] ?? null);

        return [
            'event_type' => $data['event_type'],
            'source_ref' => $data['source_ref'] ?? Arr::get($pullRequest, 'url'),
            'occurred_at' => $data['occurred_at'] ?? $mergedAt,
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
            'changed_lines' => $additions + $deletions,
            'additions' => $additions,
            'deletions' => $deletions,
            'review_acceptance_rate' => $rework === 0 ? 100 : 0,
            'ci_success_rate' => $ciFailures === 0 ? 100 : 0,
            'pr_merge_time_hours' => $this->hoursBetween($createdAt, $mergedAt),
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

    protected function hoursBetween(mixed $start, mixed $end): ?float
    {
        if ($start === null || $end === null) {
            return null;
        }

        try {
            return round(Carbon::parse($start)->floatDiffInHours(Carbon::parse($end)), 2);
        } catch (\Throwable) {
            return null;
        }
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
            ['changed_lines_count', $normalizedPayload['changed_lines'], 'lines'],
            ['review_acceptance_rate', $normalizedPayload['review_acceptance_rate'], 'percent'],
            ['ci_success_rate', $normalizedPayload['ci_success_rate'], 'percent'],
        ];

        if ($normalizedPayload['pr_merge_time_hours'] !== null) {
            $metrics[] = ['pr_merge_time_hours', $normalizedPayload['pr_merge_time_hours'], 'hours'];
        }

        if ((float) $normalizedPayload['story_points'] > 0) {
            $metrics[] = ['delivery_points', $normalizedPayload['story_points'], 'points'];
        }

        foreach ($metrics as $metric) {
            [$type, $value, $unit] = $metric;
            $metadata = [
                'event_type' => $event->event_type,
                'task_refs' => $normalizedPayload['task_refs'],
                ...(array) ($metric[3] ?? []),
            ];

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
                'metadata' => $metadata,
            ]);
        }
    }

    protected function recalculatePersonStatistics(IntegrationWebhookEvent $event): void
    {
        if ($event->person_id === null) {
            return;
        }

        $occurredAtValue = PersonDeliveryMetric::query()
            ->where('integration_webhook_event_id', $event->id)
            ->whereNotNull('occurred_at')
            ->value('occurred_at');

        $occurredAt = $occurredAtValue !== null
            ? Carbon::parse($occurredAtValue)
            : Carbon::parse($event->received_at);
        $periodStart = $occurredAt->copy()->startOfYear()->toDateString();
        $periodEnd = $occurredAt->copy()->endOfYear()->toDateString();

        $baseQuery = PersonDeliveryMetric::query()
            ->where('person_id', $event->person_id)
            ->whereNotNull('integration_webhook_event_id')
            ->whereBetween('occurred_at', [$periodStart, $periodEnd]);

        $prCount = (clone $baseQuery)->where('metric_type', 'pull_request_count')->sum('metric_value');

        if ((float) $prCount <= 0) {
            return;
        }

        $statistics = [
            ['annual_pull_request_count', $prCount, 'pr'],
            ['annual_quality_average', (clone $baseQuery)->where('metric_type', 'code_quality_score')->avg('metric_value'), 'score'],
            ['annual_review_comment_average', (clone $baseQuery)->where('metric_type', 'review_comments_count')->avg('metric_value'), 'comments/pr'],
            ['annual_ci_failure_average', (clone $baseQuery)->where('metric_type', 'ci_failures_count')->avg('metric_value'), 'failures/pr'],
            ['annual_rework_average', (clone $baseQuery)->where('metric_type', 'rework_count')->avg('metric_value'), 'times/pr'],
            ['annual_delivery_points_total', (clone $baseQuery)->where('metric_type', 'delivery_points')->sum('metric_value'), 'points'],
            ['annual_pr_size_average', (clone $baseQuery)->where('metric_type', 'changed_lines_count')->avg('metric_value'), 'lines/pr'],
            ['annual_pr_merge_time_average', (clone $baseQuery)->where('metric_type', 'pr_merge_time_hours')->avg('metric_value'), 'hours/pr'],
            ['annual_review_acceptance_rate', (clone $baseQuery)->where('metric_type', 'review_acceptance_rate')->avg('metric_value'), 'percent'],
            ['annual_ci_success_rate', (clone $baseQuery)->where('metric_type', 'ci_success_rate')->avg('metric_value'), 'percent'],
        ];

        foreach ($statistics as [$type, $value, $unit]) {
            PersonDeliveryMetric::query()->updateOrCreate(
                [
                    'person_id' => $event->person_id,
                    'integration_system_id' => null,
                    'integration_webhook_event_id' => null,
                    'metric_type' => $type,
                    'period_start' => $periodStart,
                    'period_end' => $periodEnd,
                ],
                [
                    'metric_value' => $value ?? 0,
                    'unit' => $unit,
                    'source_ref' => 'year:'.$occurredAt->year,
                    'occurred_at' => now(),
                    'metadata' => [
                        'kind' => 'annual_statistic',
                        'year' => $occurredAt->year,
                        'pull_request_count' => (float) $prCount,
                    ],
                ],
            );
        }
    }
}
