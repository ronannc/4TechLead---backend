<?php

namespace App\Services;

use App\Models\IntegrationWebhookEvent;
use App\Models\PersonDeliveryMetric;
use Illuminate\Support\Carbon;

final class DeliveryMetricIngestService
{
    /**
     * @param  array<string, mixed>  $normalizedPayload
     */
    public function createPullRequestMetrics(
        IntegrationWebhookEvent $event,
        array $normalizedPayload,
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

        $this->createMetrics($event, $normalizedPayload, $metrics);
        $this->recalculatePersonStatistics($event);
    }

    /**
     * @param  array<string, mixed>  $normalizedPayload
     */
    public function createClickUpTaskMetrics(
        IntegrationWebhookEvent $event,
        array $normalizedPayload,
    ): void {
        $metrics = [
            ['task_delivery_count', 1, 'task'],
        ];

        if ((float) ($normalizedPayload['task_sprint_points'] ?? 0) > 0) {
            $metrics[] = ['delivery_points', $normalizedPayload['task_sprint_points'], 'points'];
        }

        $this->createMetrics($event, $normalizedPayload, $metrics);
        $this->recalculatePersonStatistics($event);
    }

    /**
     * @param  array<string, mixed>  $normalizedPayload
     * @param  array<int, array{0: string, 1: mixed, 2: string}>  $metrics
     */
    private function createMetrics(
        IntegrationWebhookEvent $event,
        array $normalizedPayload,
        array $metrics,
    ): void {
        foreach ($metrics as [$type, $value, $unit]) {
            PersonDeliveryMetric::query()->createOrFirst([
                'integration_webhook_event_id' => $event->id,
                'metric_type' => $type,
            ], [
                'person_id' => $event->person_id,
                'tenant_id' => $event->tenant_id,
                'integration_system_id' => $event->integration_system_id,
                'metric_value' => $value,
                'unit' => $unit,
                'source_ref' => $normalizedPayload['source_ref'] ?? null,
                'occurred_at' => $normalizedPayload['occurred_at'] ?? null,
                'metadata' => [
                    'event_type' => $event->event_type,
                    'task_refs' => $normalizedPayload['task_refs'] ?? [],
                ],
            ]);
        }
    }

    private function recalculatePersonStatistics(IntegrationWebhookEvent $event): void
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
        $taskCount = (clone $baseQuery)->where('metric_type', 'task_delivery_count')->sum('metric_value');
        $deliveryPoints = (clone $baseQuery)->where('metric_type', 'delivery_points')->sum('metric_value');

        if ((float) $prCount <= 0 && (float) $taskCount <= 0 && (float) $deliveryPoints <= 0) {
            return;
        }

        $statistics = [
            ['annual_delivery_points_total', $deliveryPoints, 'points'],
        ];

        if ((float) $taskCount > 0) {
            $statistics[] = ['annual_task_delivery_count', $taskCount, 'task'];
        }

        if ((float) $prCount > 0) {
            $statistics = [
                ...$statistics,
                ['annual_pull_request_count', $prCount, 'pr'],
                ['annual_quality_average', (clone $baseQuery)->where('metric_type', 'code_quality_score')->avg('metric_value'), 'score'],
                ['annual_review_comment_average', (clone $baseQuery)->where('metric_type', 'review_comments_count')->avg('metric_value'), 'comments/pr'],
                ['annual_ci_failure_average', (clone $baseQuery)->where('metric_type', 'ci_failures_count')->avg('metric_value'), 'failures/pr'],
                ['annual_rework_average', (clone $baseQuery)->where('metric_type', 'rework_count')->avg('metric_value'), 'times/pr'],
                ['annual_pr_size_average', (clone $baseQuery)->where('metric_type', 'changed_lines_count')->avg('metric_value'), 'lines/pr'],
                ['annual_pr_merge_time_average', (clone $baseQuery)->where('metric_type', 'pr_merge_time_hours')->avg('metric_value'), 'hours/pr'],
                ['annual_review_acceptance_rate', (clone $baseQuery)->where('metric_type', 'review_acceptance_rate')->avg('metric_value'), 'percent'],
                ['annual_ci_success_rate', (clone $baseQuery)->where('metric_type', 'ci_success_rate')->avg('metric_value'), 'percent'],
            ];
        }

        foreach ($statistics as [$type, $value, $unit]) {
            PersonDeliveryMetric::query()->updateOrCreate(
                [
                    'person_id' => $event->person_id,
                    'tenant_id' => $event->tenant_id,
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
                        'task_delivery_count' => (float) $taskCount,
                    ],
                ],
            );
        }
    }
}
