<?php

namespace App\Services;

use App\Models\IntegrationWebhookEvent;
use App\Models\PersonDeliveryMetric;
use Illuminate\Support\Carbon;

final class DeliveryMetricIngestService
{
    public function ingest(IntegrationWebhookEvent $event): void
    {
        if ($event->person_id !== null) {
            foreach ($this->metricsFor($event) as $metric) {
                $this->persist($event, $metric);
            }
        }

        $this->refreshCrossMetrics($event);
    }

    /**
     * @return array<int, array{type: string, value: float|int, unit: string, metadata?: array<string, mixed>}
     */
    private function metricsFor(IntegrationWebhookEvent $event): array
    {
        $payload = $event->normalized_payload ?? [];
        $type = (string) $event->event_type;
        $metrics = [];

        if (($payload['source'] ?? null) === 'clickup') {
            if (($payload['history_field'] ?? null) === 'status') {
                $metrics[] = $this->metric('task_status_change_count', 1, 'count');
            }

            if ($this->isCompletedStatus($payload['history_after'] ?? $payload['task_status'] ?? null)) {
                $metrics[] = $this->metric('task_completed_count', 1, 'count');

                if (is_numeric($payload['task_sprint_points'] ?? null)) {
                    $metrics[] = $this->metric(
                        'task_delivery_points',
                        (float) $payload['task_sprint_points'],
                        'points',
                    );
                }
            }

            return $metrics;
        }

        if (str_starts_with($type, 'pull_request.')) {
            if ($type === 'pull_request.opened') {
                $metrics[] = $this->metric('pull_request_count', 1, 'count');
            }

            if (($payload['pr_merged'] ?? false) === true) {
                $metrics[] = $this->metric('pull_request_merged_count', 1, 'count');
            } elseif ($type === 'pull_request.closed' && ($payload['closed_without_merge'] ?? false)) {
                $metrics[] = $this->metric('pull_request_closed_without_merge_count', 1, 'count');
            }

            if (($payload['quality_score'] ?? null) !== null) {
                $metrics[] = $this->metric('code_quality_score', $payload['quality_score'], 'score');
            }

            $metrics = array_merge($metrics, $this->optionalMetrics($payload, [
                'pr_open_time_hours' => ['pull_request_open_time', 'hours'],
                'pr_merge_time_hours' => ['pull_request_merge_time', 'hours'],
                'changed_files' => ['pull_request_changed_files', 'files'],
                'changed_lines' => ['pull_request_changed_lines', 'lines'],
            ]));
        }

        if ($type === 'pull_request_review.submitted') {
            $metrics[] = $this->metric('review_count', 1, 'count');
            $reviewMetric = match ($payload['review_state'] ?? null) {
                'approved' => 'review_approved_count',
                'changes_requested' => 'review_changes_requested_count',
                'commented' => 'review_commented_count',
                default => null,
            };

            if ($reviewMetric !== null) {
                $metrics[] = $this->metric($reviewMetric, 1, 'count');
            }
        }

        if (in_array($type, ['check_run.completed', 'check_suite.completed', 'workflow_run.completed'], true)) {
            $metrics[] = $this->metric('ci_run_count', 1, 'count');
            $conclusion = $payload['check_run_conclusion']
                ?? $payload['check_suite_conclusion']
                ?? $payload['workflow_run_conclusion'];
            $metrics[] = $this->metric(
                $conclusion === 'success' ? 'ci_success_count' : 'ci_failure_count',
                1,
                'count',
            );
        }

        if ($type === 'deployment_status.created') {
            $metrics[] = $this->metric('deployment_count', 1, 'count');
            $state = $payload['deployment_status_state'] ?? null;
            $metrics[] = $this->metric(
                $state === 'success' ? 'deployment_success_count' : 'deployment_failure_count',
                1,
                'count',
            );
        }

        return $metrics;
    }

    /**
     * @param  array<string, mixed>  $payload
     * @param  array<string, array{0: string, 1: string}>  $definitions
     * @return array<int, array{type: string, value: float|int, unit: string}>
     */
    private function optionalMetrics(array $payload, array $definitions): array
    {
        $metrics = [];

        foreach ($definitions as $key => [$type, $unit]) {
            if (($payload[$key] ?? null) !== null) {
                $metrics[] = $this->metric($type, $payload[$key], $unit);
            }
        }

        return $metrics;
    }

    /**
     * @return array{type: string, value: float|int, unit: string}
     */
    private function metric(string $type, float|int $value, string $unit): array
    {
        return ['type' => $type, 'value' => $value, 'unit' => $unit];
    }

    private function persist(IntegrationWebhookEvent $event, array $metric, ?array $metadata = null): void
    {
        PersonDeliveryMetric::query()->updateOrCreate(
            [
                'integration_webhook_event_id' => $event->id,
                'metric_type' => $metric['type'],
            ],
            [
                'person_id' => $event->person_id,
                'tenant_id' => $event->tenant_id,
                'integration_system_id' => $event->integration_system_id,
                'metric_value' => $metric['value'],
                'unit' => $metric['unit'],
                'source_ref' => data_get($event->normalized_payload, 'source_ref')
                    ?? data_get($event->normalized_payload, 'task_id'),
                'occurred_at' => $event->normalized_payload['occurred_at'] ?? $event->received_at,
                'metadata' => $metadata,
            ],
        );
    }

    private function refreshCrossMetrics(IntegrationWebhookEvent $event): void
    {
        $references = array_values(array_filter((array) data_get($event->normalized_payload, 'task_refs')));

        if ($references === []) {
            return;
        }

        $events = IntegrationWebhookEvent::query()
            ->where('tenant_id', $event->tenant_id)
            ->whereIn('event_type', ['pull_request.closed', 'clickup_automation', 'taskStatusUpdated'])
            ->get();

        $githubEvents = $events->filter(fn (IntegrationWebhookEvent $candidate): bool => str_starts_with($candidate->event_type, 'pull_request.')
            && $this->sharesReference($references, $candidate)
            && data_get($candidate->normalized_payload, 'pr_merged') === true
            && $candidate->person_id !== null);
        $clickUpEvents = $events->filter(fn (IntegrationWebhookEvent $candidate): bool => ! str_starts_with($candidate->event_type, 'pull_request.')
            && $this->sharesReference($references, $candidate)
            && $this->isCompletedStatus(data_get($candidate->normalized_payload, 'history_after')));

        foreach ($githubEvents as $githubEvent) {
            foreach ($clickUpEvents as $clickUpEvent) {
                $hours = $this->hoursBetween(
                    data_get($clickUpEvent->normalized_payload, 'occurred_at'),
                    data_get($githubEvent->normalized_payload, 'merged_at'),
                );

                $metadata = [
                    'github_event_id' => $githubEvent->id,
                    'clickup_event_id' => $clickUpEvent->id,
                    'task_refs' => array_values(array_intersect(
                        $this->references($githubEvent),
                        $this->references($clickUpEvent),
                    )),
                ];

                $this->persist($githubEvent, $this->metric('task_pull_request_link_count', 1, 'count'), $metadata);

                if ($hours !== null && $hours >= 0) {
                    $this->persist(
                        $githubEvent,
                        $this->metric('task_to_pull_request_hours', $hours, 'hours'),
                        $metadata,
                    );
                }
            }
        }
    }

    private function sharesReference(array $references, IntegrationWebhookEvent $event): bool
    {
        return array_intersect($references, $this->references($event)) !== [];
    }

    /**
     * @return array<int, string>
     */
    private function references(IntegrationWebhookEvent $event): array
    {
        return array_values(array_filter((array) data_get($event->normalized_payload, 'task_refs')));
    }

    private function isCompletedStatus(mixed $status): bool
    {
        return is_string($status) && in_array(strtolower(trim($status)), [
            'done',
            'complete',
            'completed',
            'closed',
            'concluído',
            'concluido',
        ], true);
    }

    private function hoursBetween(mixed $start, mixed $end): ?float
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
}
