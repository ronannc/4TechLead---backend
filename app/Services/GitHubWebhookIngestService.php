<?php

namespace App\Services;

use App\Models\IntegrationSystem;
use App\Models\IntegrationWebhookEvent;
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

        return DB::transaction(function () use ($integrationSystem, $payload, $headers): IntegrationWebhookEvent {
            $normalizedPayload = $this->normalize($payload, $headers);

            $event = IntegrationWebhookEvent::query()->createOrFirst(
                [
                    'integration_system_id' => $integrationSystem->id,
                    'event_id' => $normalizedPayload['event_id'],
                ],
                [
                    'tenant_id' => $integrationSystem->tenant_id,
                    'person_id' => null,
                    'event_type' => $normalizedPayload['event_type'],
                    'external_actor_code' => $normalizedPayload['external_actor_code'],
                    'status' => 'received',
                    'failure_reason' => null,
                    'payload' => $payload,
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
        $repository = (array) Arr::get($payload, 'repository', []);

        return [
            'source' => 'github',
            'event_id' => $this->eventId($payload, $headers, $event, $action),
            'event_type' => $action === null ? $event : $event.'.'.$action,
            'external_actor_code' => $this->externalActorCode($payload),
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
            'head_ref' => Arr::get($pullRequest, 'head.ref', Arr::get($checkRun, 'head_branch', Arr::get($workflowRun, 'head_branch'))),
            'head_sha' => Arr::get($pullRequest, 'head.sha', Arr::get($checkRun, 'head_sha', Arr::get($workflowRun, 'head_sha'))),
            'base_ref' => Arr::get($pullRequest, 'base.ref'),
            'created_at' => $this->timestamp(Arr::get($pullRequest, 'created_at')),
            'updated_at' => $this->timestamp(Arr::get($pullRequest, 'updated_at')),
            'closed_at' => $this->timestamp(Arr::get($pullRequest, 'closed_at')),
            'merged_at' => $this->timestamp(Arr::get($pullRequest, 'merged_at')),
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
            'check_suite_id' => Arr::get($checkSuite, 'id'),
            'check_suite_conclusion' => Arr::get($checkSuite, 'conclusion'),
            'workflow_run_id' => Arr::get($workflowRun, 'id'),
            'workflow_run_name' => Arr::get($workflowRun, 'name'),
            'workflow_run_status' => Arr::get($workflowRun, 'status'),
            'workflow_run_conclusion' => Arr::get($workflowRun, 'conclusion'),
            'task_refs' => $this->taskRefs($payload),
        ];
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
            'workflow_run' => Arr::get($payload, 'workflow_run.id'),
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
            Arr::get($payload, 'workflow_run.head_branch'),
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
