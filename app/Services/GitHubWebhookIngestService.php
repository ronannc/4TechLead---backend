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

final class GitHubWebhookIngestService
{
    /**
     * @param  array<string, mixed>  $payload
     * @param  array<string, mixed>  $headers
     *
     * @throws Throwable
     */
    public function ingest(string $token, array $payload, string $rawBody, array $headers): IntegrationWebhookEvent
    {
        $integrationSystem = $this->integrationSystem($token);

        $this->assertCanReceive($integrationSystem, $token, $rawBody, (string) ($headers['signature_256'] ?? ''));

        return $this->storeEvent($integrationSystem, $payload, $rawBody, $headers);
    }

    /**
     * @param  array<string, mixed>  $payload
     * @param  array<string, mixed>  $headers
     *
     * @throws Throwable
     */
    public function ingestSigned(
        array $payload,
        string $rawBody,
        array $headers,
    ): IntegrationWebhookEvent {
        $integrationSystem = $this->integrationSystemBySignature(
            $rawBody,
            (string) ($headers['signature_256'] ?? ''),
        );

        return $this->storeEvent($integrationSystem, $payload, $rawBody, $headers);
    }

    /**
     * @param  array<string, mixed>  $payload
     * @param  array<string, mixed>  $headers
     *
     * @throws Throwable
     */
    protected function storeEvent(
        IntegrationSystem $integrationSystem,
        array $payload,
        string $rawBody,
        array $headers,
    ): IntegrationWebhookEvent {
        return DB::transaction(function () use ($integrationSystem, $payload, $rawBody, $headers): IntegrationWebhookEvent {
            $normalizedPayload = $this->normalize($payload, $headers);
            $personId = $this->personIdFor($integrationSystem, $normalizedPayload);

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
    }

    protected function integrationSystem(string $token): IntegrationSystem
    {
        $integrationSystem = IntegrationSystem::query()
            ->whereIn('provider', ['github', 'github-actions'])
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
            throw new AccessDeniedHttpException('Missing GitHub signature.');
        }

        $integrationSystems = IntegrationSystem::query()
            ->whereIn('provider', ['github', 'github-actions'])
            ->where('active', true)
            ->whereNotNull('webhook_secret')
            ->get();

        foreach ($integrationSystems as $integrationSystem) {
            $secret = $integrationSystem->webhook_secret;

            if (! is_string($secret) || $secret === '') {
                continue;
            }

            $expectedSignature = 'sha256='.hash_hmac('sha256', $rawBody, $secret);

            if (hash_equals($expectedSignature, $signature)) {
                return $integrationSystem;
            }
        }

        throw new AccessDeniedHttpException('Invalid GitHub signature.');
    }

    protected function assertCanReceive(
        IntegrationSystem $integrationSystem,
        string $token,
        string $rawBody,
        string $signature,
    ): void {
        if (! in_array($integrationSystem->provider, ['github', 'github-actions'], true)) {
            throw new AccessDeniedHttpException('Integration provider must be github.');
        }

        if (! $integrationSystem->active) {
            throw new AccessDeniedHttpException('Integration is inactive.');
        }

        if ($token === '' || ! hash_equals($integrationSystem->token_hash, hash('sha256', $token))) {
            throw new UnauthorizedHttpException('Bearer', 'Invalid integration token.');
        }

        if ($signature !== '') {
            $expectedSignature = 'sha256='.hash_hmac('sha256', $rawBody, $token);

            if (! hash_equals($expectedSignature, $signature)) {
                throw new AccessDeniedHttpException('Invalid GitHub signature.');
            }
        }
    }

    /**
     * @param  array<string, mixed>  $payload
     * @param  array<string, mixed>  $headers
     * @return array<string, mixed>
     */
    protected function normalize(array $payload, array $headers): array
    {
        $event = (string) ($headers['event'] ?? 'github');
        $action = Arr::get($payload, 'action');
        $pullRequest = (array) Arr::get($payload, 'pull_request', []);
        $review = (array) Arr::get($payload, 'review', []);
        $comment = (array) Arr::get($payload, 'comment', []);
        $checkRun = (array) Arr::get($payload, 'check_run', []);
        $checkSuite = (array) Arr::get($payload, 'check_suite', []);
        $workflowRun = (array) Arr::get($payload, 'workflow_run', []);
        $deployment = (array) Arr::get($payload, 'deployment', []);
        $deploymentStatus = (array) Arr::get($payload, 'deployment_status', []);
        $repository = (array) Arr::get($payload, 'repository', []);
        $closedAt = $this->timestamp(Arr::get($pullRequest, 'closed_at'));
        $mergedAt = $this->timestamp(Arr::get($pullRequest, 'merged_at'));
        $completedAt = $mergedAt
            ?? $closedAt
            ?? $this->timestamp(Arr::get($review, 'submitted_at'))
            ?? $this->timestamp(Arr::get($checkRun, 'completed_at'))
            ?? $this->timestamp(Arr::get($workflowRun, 'updated_at'))
            ?? $this->timestamp(Arr::get($deploymentStatus, 'created_at'));

        return [
            'source' => 'github',
            'event_id' => $this->eventId($payload, $headers, $event, $action),
            'event_type' => $action === null ? $event : $event.'.'.$action,
            'external_actor_code' => $this->externalActorCode($payload),
            'occurred_at' => $completedAt ?? $this->timestamp(Arr::get($payload, 'repository.pushed_at')),
            'delivery_id' => $headers['delivery'] ?? null,
            'hook_id' => $headers['hook_id'] ?? null,
            'repository_id' => Arr::get($repository, 'id'),
            'repository_full_name' => Arr::get($repository, 'full_name'),
            'organization' => Arr::get($payload, 'organization.login'),
            'sender_login' => Arr::get($payload, 'sender.login'),
            'pr_id' => Arr::get($pullRequest, 'id'),
            'pr_number' => $this->pullRequestNumber($payload),
            'pr_title' => Arr::get($pullRequest, 'title'),
            'pr_state' => Arr::get($pullRequest, 'state'),
            'pr_draft' => Arr::get($pullRequest, 'draft'),
            'pr_merged' => Arr::get($pullRequest, 'merged'),
            'pr_author' => Arr::get($pullRequest, 'user.login'),
            'source_ref' => Arr::get($payload, 'repository.full_name') === null || $this->pullRequestNumber($payload) === null
                ? null
                : Arr::get($payload, 'repository.full_name').'#'.$this->pullRequestNumber($payload),
            'head_ref' => Arr::get($pullRequest, 'head.ref', Arr::get($checkRun, 'head_branch', Arr::get($workflowRun, 'head_branch'))),
            'head_sha' => Arr::get($pullRequest, 'head.sha', Arr::get($checkRun, 'head_sha', Arr::get($workflowRun, 'head_sha'))),
            'base_ref' => Arr::get($pullRequest, 'base.ref'),
            'created_at' => $this->timestamp(Arr::get($pullRequest, 'created_at')),
            'updated_at' => $this->timestamp(Arr::get($pullRequest, 'updated_at')),
            'closed_at' => $closedAt,
            'merged_at' => $mergedAt,
            'closed_without_merge' => $closedAt !== null && $mergedAt === null,
            'review_id' => Arr::get($review, 'id'),
            'review_state' => Arr::get($review, 'state'),
            'review_submitted_at' => $this->timestamp(Arr::get($review, 'submitted_at')),
            'comment_id' => Arr::get($comment, 'id'),
            'comment_author' => Arr::get($comment, 'user.login'),
            'comment_path' => Arr::get($comment, 'path'),
            'check_run_id' => Arr::get($checkRun, 'id'),
            'check_run_name' => Arr::get($checkRun, 'name'),
            'check_run_status' => Arr::get($checkRun, 'status'),
            'check_run_conclusion' => Arr::get($checkRun, 'conclusion'),
            'check_run_started_at' => $this->timestamp(Arr::get($checkRun, 'started_at')),
            'check_run_completed_at' => $this->timestamp(Arr::get($checkRun, 'completed_at')),
            'check_suite_id' => Arr::get($checkSuite, 'id'),
            'check_suite_status' => Arr::get($checkSuite, 'status'),
            'check_suite_conclusion' => Arr::get($checkSuite, 'conclusion'),
            'check_suite_head_branch' => Arr::get($checkSuite, 'head_branch'),
            'check_suite_head_sha' => Arr::get($checkSuite, 'head_sha'),
            'workflow_run_id' => Arr::get($workflowRun, 'id'),
            'workflow_run_name' => Arr::get($workflowRun, 'name'),
            'workflow_run_status' => Arr::get($workflowRun, 'status'),
            'workflow_run_conclusion' => Arr::get($workflowRun, 'conclusion'),
            'workflow_run_started_at' => $this->timestamp(Arr::get($workflowRun, 'run_started_at')),
            'workflow_run_updated_at' => $this->timestamp(Arr::get($workflowRun, 'updated_at')),
            'deployment_id' => Arr::get($deployment, 'id'),
            'deployment_environment' => Arr::get($deployment, 'environment'),
            'deployment_ref' => Arr::get($deployment, 'ref'),
            'deployment_sha' => Arr::get($deployment, 'sha'),
            'deployment_status_id' => Arr::get($deploymentStatus, 'id'),
            'deployment_status_state' => Arr::get($deploymentStatus, 'state'),
            'deployment_status_created_at' => $this->timestamp(Arr::get($deploymentStatus, 'created_at')),
            'deployment_status_updated_at' => $this->timestamp(Arr::get($deploymentStatus, 'updated_at')),
            'task_refs' => $this->taskRefs($payload),
            'quality_score' => $this->qualityScore(
                reviewComments: (int) $this->number($pullRequest, 'review_comments'),
                ciFailures: $this->isFailure(Arr::get($checkRun, 'conclusion'))
                    || $this->isFailure(Arr::get($checkSuite, 'conclusion'))
                    || $this->isFailure(Arr::get($workflowRun, 'conclusion'))
                    || $this->isFailure(Arr::get($deploymentStatus, 'state')) ? 1 : 0,
                rework: 0,
            ),
            'review_comments_count' => (int) $this->number($pullRequest, 'review_comments'),
            'comments_count' => (int) $this->number($pullRequest, 'comments'),
            'review_count' => (int) $this->number($pullRequest, 'review_count'),
            'unique_reviewer_count' => (int) $this->number($pullRequest, 'unique_reviewer_count'),
            'approvals_count' => (int) $this->number($pullRequest, 'approvals_count'),
            'changes_requested_count' => (int) $this->number($pullRequest, 'changes_requested_count'),
            'ci_failures_count' => $this->isFailure(Arr::get($checkRun, 'conclusion'))
                || $this->isFailure(Arr::get($checkSuite, 'conclusion'))
                || $this->isFailure(Arr::get($workflowRun, 'conclusion')) ? 1 : 0,
            'rework_count' => 0,
            'story_points' => $this->number($pullRequest, 'story_points'),
            'changed_files' => (int) $this->number($pullRequest, 'changed_files'),
            'changed_lines' => $this->number($pullRequest, 'additions') + $this->number($pullRequest, 'deletions'),
            'additions' => $this->number($pullRequest, 'additions'),
            'deletions' => $this->number($pullRequest, 'deletions'),
            'review_acceptance_rate' => null,
            'ci_success_rate' => $this->ciSuccessRate($checkRun, $checkSuite, $workflowRun),
            'pr_open_time_hours' => $this->hoursBetween(
                Arr::get($pullRequest, 'created_at'),
                Arr::get($pullRequest, 'merged_at', Arr::get($pullRequest, 'closed_at')),
            ),
            'pr_merge_time_hours' => Arr::get($pullRequest, 'merged_at') === null
                ? null
                : $this->hoursBetween(Arr::get($pullRequest, 'created_at'), Arr::get($pullRequest, 'merged_at')),
        ];
    }

    /**
     * @param  array<string, mixed>  $pullRequest
     */
    protected function number(array $pullRequest, string $key): float|int
    {
        $value = Arr::get($pullRequest, $key, 0);

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
        } catch (Throwable) {
            return null;
        }
    }

    /**
     * @param  array<string, mixed>  $normalizedPayload
     */
    protected function metricActorCode(array $normalizedPayload): ?string
    {
        if (str_starts_with((string) $normalizedPayload['event_type'], 'pull_request_review.')) {
            return $normalizedPayload['external_actor_code'];
        }

        $author = $normalizedPayload['pr_author'] ?? null;

        return is_string($author) && $author !== ''
            ? 'github_user:'.$author
            : $normalizedPayload['external_actor_code'];
    }

    /**
     * @param  array<string, mixed>  $normalizedPayload
     */
    protected function personIdFor(IntegrationSystem $integrationSystem, array $normalizedPayload): ?int
    {
        $externalCode = $this->metricActorCode($normalizedPayload);
        $identity = $this->identityFor($integrationSystem, $externalCode);

        if ($identity !== null) {
            return $identity->person_id;
        }

        if (! is_string($externalCode) || ! str_starts_with($externalCode, 'github_user:')) {
            return null;
        }

        $githubUsername = substr($externalCode, strlen('github_user:'));

        if ($githubUsername === '') {
            return null;
        }

        return Person::query()
            ->where('tenant_id', $integrationSystem->tenant_id)
            ->where('github_username', strtolower($githubUsername))
            ->value('id');
    }

    protected function identityFor(IntegrationSystem $integrationSystem, ?string $externalCode): ?PersonExternalIdentity
    {
        if ($externalCode === null || $externalCode === '') {
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
            'actor' => array_filter([
                'external_code' => $normalizedPayload['external_actor_code'],
                'sender_login' => $normalizedPayload['sender_login'],
            ], fn (mixed $value): bool => $value !== null && $value !== ''),
            'repository' => array_filter([
                'id' => $normalizedPayload['repository_id'],
                'full_name' => $normalizedPayload['repository_full_name'],
                'organization' => $normalizedPayload['organization'],
            ], fn (mixed $value): bool => $value !== null && $value !== ''),
            'pull_request' => array_filter([
                'id' => $normalizedPayload['pr_id'],
                'number' => $normalizedPayload['pr_number'],
                'title' => $normalizedPayload['pr_title'],
                'state' => $normalizedPayload['pr_state'],
                'draft' => $normalizedPayload['pr_draft'],
                'merged' => $normalizedPayload['pr_merged'],
                'author_login' => $normalizedPayload['pr_author'],
                'source_ref' => $normalizedPayload['source_ref'],
                'head_ref' => $normalizedPayload['head_ref'],
                'head_sha' => $normalizedPayload['head_sha'],
                'base_ref' => $normalizedPayload['base_ref'],
                'created_at' => $normalizedPayload['created_at'],
                'updated_at' => $normalizedPayload['updated_at'],
                'closed_at' => $normalizedPayload['closed_at'],
                'merged_at' => $normalizedPayload['merged_at'],
                'closed_without_merge' => $normalizedPayload['closed_without_merge'],
                'review_comments_count' => $normalizedPayload['review_comments_count'],
                'comments_count' => $normalizedPayload['comments_count'],
                'changed_files' => $normalizedPayload['changed_files'],
                'changed_lines' => $normalizedPayload['changed_lines'],
                'additions' => $normalizedPayload['additions'],
                'deletions' => $normalizedPayload['deletions'],
                'open_time_hours' => $normalizedPayload['pr_open_time_hours'],
                'merge_time_hours' => $normalizedPayload['pr_merge_time_hours'],
            ], fn (mixed $value): bool => $value !== null && $value !== ''),
            'review' => array_filter([
                'id' => $normalizedPayload['review_id'],
                'state' => $normalizedPayload['review_state'],
                'submitted_at' => $normalizedPayload['review_submitted_at'],
            ], fn (mixed $value): bool => $value !== null && $value !== ''),
            'check_run' => array_filter([
                'id' => $normalizedPayload['check_run_id'],
                'name' => $normalizedPayload['check_run_name'],
                'status' => $normalizedPayload['check_run_status'],
                'conclusion' => $normalizedPayload['check_run_conclusion'],
                'started_at' => $normalizedPayload['check_run_started_at'],
                'completed_at' => $normalizedPayload['check_run_completed_at'],
            ], fn (mixed $value): bool => $value !== null && $value !== ''),
            'check_suite' => array_filter([
                'id' => $normalizedPayload['check_suite_id'],
                'status' => $normalizedPayload['check_suite_status'],
                'conclusion' => $normalizedPayload['check_suite_conclusion'],
                'head_branch' => $normalizedPayload['check_suite_head_branch'],
                'head_sha' => $normalizedPayload['check_suite_head_sha'],
            ], fn (mixed $value): bool => $value !== null && $value !== ''),
            'workflow_run' => array_filter([
                'id' => $normalizedPayload['workflow_run_id'],
                'name' => $normalizedPayload['workflow_run_name'],
                'status' => $normalizedPayload['workflow_run_status'],
                'conclusion' => $normalizedPayload['workflow_run_conclusion'],
                'started_at' => $normalizedPayload['workflow_run_started_at'],
                'updated_at' => $normalizedPayload['workflow_run_updated_at'],
            ], fn (mixed $value): bool => $value !== null && $value !== ''),
            'deployment' => array_filter([
                'id' => $normalizedPayload['deployment_id'],
                'environment' => $normalizedPayload['deployment_environment'],
                'ref' => $normalizedPayload['deployment_ref'],
                'sha' => $normalizedPayload['deployment_sha'],
                'status_id' => $normalizedPayload['deployment_status_id'],
                'status_state' => $normalizedPayload['deployment_status_state'],
                'status_created_at' => $normalizedPayload['deployment_status_created_at'],
                'status_updated_at' => $normalizedPayload['deployment_status_updated_at'],
            ], fn (mixed $value): bool => $value !== null && $value !== ''),
            'task_refs' => $normalizedPayload['task_refs'],
        ], fn (mixed $value): bool => $value !== null && $value !== [] && $value !== '');
    }

    protected function isFailure(mixed $value): bool
    {
        return in_array($value, ['failure', 'timed_out', 'cancelled', 'startup_failure', 'error'], true);
    }

    /**
     * @param  array<string, mixed>  $checkRun
     * @param  array<string, mixed>  $checkSuite
     * @param  array<string, mixed>  $workflowRun
     */
    protected function ciSuccessRate(array $checkRun, array $checkSuite, array $workflowRun): ?int
    {
        $conclusion = Arr::get($checkRun, 'conclusion')
            ?? Arr::get($checkSuite, 'conclusion')
            ?? Arr::get($workflowRun, 'conclusion');

        if ($conclusion === null) {
            return null;
        }

        return $conclusion === 'success' ? 100 : 0;
    }

    /**
     * @param  array<string, mixed>  $payload
     * @param  array<string, mixed>  $headers
     */
    protected function eventId(array $payload, array $headers, string $event, mixed $action): string
    {
        if (($headers['delivery'] ?? null) !== null && $headers['delivery'] !== '') {
            return (string) $headers['delivery'];
        }

        return hash('sha256', json_encode([
            'event' => $event,
            'action' => $action,
            'repository' => Arr::get($payload, 'repository.full_name'),
            'pull_request' => Arr::get($payload, 'pull_request.number'),
            'check_run' => Arr::get($payload, 'check_run.id'),
            'check_suite' => Arr::get($payload, 'check_suite.id'),
            'workflow_run' => Arr::get($payload, 'workflow_run.id'),
            'deployment_status' => Arr::get($payload, 'deployment_status.id'),
        ], JSON_THROW_ON_ERROR));
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    protected function externalActorCode(array $payload): ?string
    {
        $login = Arr::get($payload, 'sender.login')
            ?? Arr::get($payload, 'pull_request.user.login')
            ?? Arr::get($payload, 'review.user.login')
            ?? Arr::get($payload, 'comment.user.login');

        return $login === null ? null : 'github_user:'.$login;
    }

    protected function timestamp(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        try {
            return Carbon::parse((string) $value)->toISOString();
        } catch (Throwable) {
            return null;
        }
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    protected function pullRequestNumber(array $payload): mixed
    {
        return Arr::get($payload, 'pull_request.number')
            ?? Arr::get($payload, 'number')
            ?? Arr::get($payload, 'check_run.pull_requests.0.number')
            ?? Arr::get($payload, 'check_suite.pull_requests.0.number')
            ?? Arr::get($payload, 'workflow_run.pull_requests.0.number');
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<int, string>
     */
    protected function taskRefs(array $payload): array
    {
        $values = [
            Arr::get($payload, 'pull_request.title'),
            Arr::get($payload, 'pull_request.body'),
            Arr::get($payload, 'pull_request.head.ref'),
            Arr::get($payload, 'pull_request.base.ref'),
            Arr::get($payload, 'check_run.head_branch'),
            Arr::get($payload, 'check_suite.head_branch'),
            Arr::get($payload, 'workflow_run.head_branch'),
            Arr::get($payload, 'deployment.ref'),
            Arr::get($payload, 'head_commit.message'),
        ];

        foreach ((array) Arr::get($payload, 'commits', []) as $commit) {
            if (is_array($commit)) {
                $values[] = Arr::get($commit, 'message');
            }
        }

        $refs = [];

        foreach ($values as $value) {
            if (! is_string($value)) {
                continue;
            }

            preg_match_all('/\b[A-Z][A-Z0-9]+-\d+\b/', $value, $matches);

            foreach ($matches[0] as $match) {
                $refs[] = $match;
            }
        }

        return array_values(array_unique($refs));
    }
}
