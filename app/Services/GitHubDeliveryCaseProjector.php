<?php

namespace App\Services;

use App\Enums\DeliveryAssociationConfidence;
use App\Enums\DeliveryExternalLinkType;
use App\Enums\DeliveryMilestoneType;
use App\Enums\DeliveryParticipantRole;
use App\Models\DeliveryCase;
use App\Models\DeliveryCaseExternalLink;
use App\Models\DeliveryCaseMilestone;
use App\Models\DeliveryCaseParticipant;
use App\Models\IntegrationWebhookEvent;
use App\Models\Person;
use App\Models\PersonExternalIdentity;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

final class GitHubDeliveryCaseProjector
{
    public function project(IntegrationWebhookEvent $event): ?DeliveryCase
    {
        if ($event->tenant_id === null || data_get($event->normalized_payload, 'source') !== 'github') {
            return null;
        }

        $taskReference = $this->taskReference($event);

        if ($taskReference === null) {
            return null;
        }

        return DB::transaction(function () use ($event, $taskReference): DeliveryCase {
            $deliveryCase = DeliveryCase::query()->withoutGlobalScopes()->createOrFirst(
                [
                    'tenant_id' => $event->tenant_id,
                    'task_ref' => $taskReference,
                ],
                [
                    'first_seen_at' => $this->occurredAt($event),
                    'last_activity_at' => $this->occurredAt($event),
                ],
            );
            $deliveryCase = DeliveryCase::query()
                ->withoutGlobalScopes()
                ->lockForUpdate()
                ->findOrFail($deliveryCase->id);
            $events = $this->eventsFor($deliveryCase);

            $this->projectCaseActivity($deliveryCase, $events);
            $this->projectExternalLinks($deliveryCase, $events);
            $this->projectMilestones($deliveryCase, $events);
            $this->projectParticipants($deliveryCase, $events);

            return $deliveryCase->refresh();
        });
    }

    /**
     * @return Collection<int, IntegrationWebhookEvent>
     */
    private function eventsFor(DeliveryCase $deliveryCase): Collection
    {
        $events = IntegrationWebhookEvent::query()
            ->withoutGlobalScope('tenant')
            ->where('tenant_id', $deliveryCase->tenant_id)
            ->where('normalized_payload->source', 'github')
            ->get();
        $caseExternalIds = DeliveryCaseExternalLink::query()
            ->withoutGlobalScopes()
            ->where('delivery_case_id', $deliveryCase->id)
            ->whereIn('link_type', [
                DeliveryExternalLinkType::GitHubPullRequest->value,
                DeliveryExternalLinkType::GitHubHeadSha->value,
            ])
            ->pluck('external_id')
            ->all();

        return $events->filter(function (IntegrationWebhookEvent $event) use ($deliveryCase, $caseExternalIds): bool {
            if ((bool) data_get($event->normalized_payload, 'task_ref_ambiguous', false)) {
                return false;
            }

            if (in_array($deliveryCase->task_ref, $this->taskReferences($event), true)) {
                return true;
            }

            return array_intersect($caseExternalIds, $this->externalIdsFor($event)) !== [];
        })->sort(function (IntegrationWebhookEvent $left, IntegrationWebhookEvent $right): int {
            $occurredAtComparison = strcmp(
                $this->occurredAt($left)->format('U.u'),
                $this->occurredAt($right)->format('U.u'),
            );

            if ($occurredAtComparison !== 0) {
                return $occurredAtComparison;
            }

            return $left->id <=> $right->id;
        })->values();
    }

    /**
     * @param  Collection<int, IntegrationWebhookEvent>  $events
     */
    private function projectCaseActivity(DeliveryCase $deliveryCase, Collection $events): void
    {
        $firstEvent = $events->first();
        $lastEvent = $events->last();
        $title = $deliveryCase->title;

        if ($title === null) {
            $title = $events->pluck('normalized_payload.pr_title')->filter()->last();
        }

        $deliveryCase->forceFill([
            'title' => $title,
            'first_seen_at' => $firstEvent === null ? $deliveryCase->first_seen_at : $this->occurredAt($firstEvent),
            'last_activity_at' => $lastEvent === null ? $deliveryCase->last_activity_at : $this->occurredAt($lastEvent),
        ])->save();
    }

    /**
     * @param  Collection<int, IntegrationWebhookEvent>  $events
     */
    private function projectExternalLinks(DeliveryCase $deliveryCase, Collection $events): void
    {
        foreach ($events as $event) {
            foreach ($this->linksFor($event) as $link) {
                DeliveryCaseExternalLink::query()->withoutGlobalScopes()->updateOrCreate(
                    [
                        'tenant_id' => $deliveryCase->tenant_id,
                        'link_type' => $link['type']->value,
                        'external_id' => $link['external_id'],
                    ],
                    [
                        'delivery_case_id' => $deliveryCase->id,
                        'integration_system_id' => $event->integration_system_id,
                        'integration_webhook_event_id' => $event->id,
                        'source_ref' => $this->sourceReference($event),
                        'metadata' => $link['metadata'],
                    ],
                );
            }
        }
    }

    /**
     * @param  Collection<int, IntegrationWebhookEvent>  $events
     */
    private function projectMilestones(DeliveryCase $deliveryCase, Collection $events): void
    {
        foreach ($events as $event) {
            foreach ($this->milestonesFor($event, $events) as $milestone) {
                DeliveryCaseMilestone::query()->withoutGlobalScopes()->updateOrCreate(
                    [
                        'delivery_case_id' => $deliveryCase->id,
                        'semantic_key' => $milestone['semantic_key'],
                    ],
                    [
                        'tenant_id' => $deliveryCase->tenant_id,
                        'integration_webhook_event_id' => $event->id,
                        'milestone_type' => $milestone['type'],
                        'source_provider' => 'github',
                        'source_ref' => $this->sourceReference($event),
                        'occurred_at' => $this->occurredAt($event),
                        'metadata' => $milestone['metadata'],
                    ],
                );
            }
        }
    }

    /**
     * @param  Collection<int, IntegrationWebhookEvent>  $events
     */
    private function projectParticipants(DeliveryCase $deliveryCase, Collection $events): void
    {
        $participants = [];

        foreach ($events as $event) {
            $eventType = (string) data_get($event->normalized_payload, 'event_type', '');
            $author = data_get($event->normalized_payload, 'pr_author');

            if (is_string($author) && ! $this->isBot($author)) {
                $this->addParticipant($participants, $event, $author, DeliveryParticipantRole::CodeAuthor);
            }

            if (Str::startsWith($eventType, 'pull_request_review.')) {
                $reviewer = data_get($event->normalized_payload, 'external_actor_code');

                if (is_string($reviewer) && Str::startsWith($reviewer, 'github_user:')) {
                    $this->addParticipant(
                        $participants,
                        $event,
                        Str::after($reviewer, 'github_user:'),
                        DeliveryParticipantRole::Reviewer,
                    );
                }
            }
        }

        $projectedIds = [];

        foreach ($participants as $participant) {
            $projected = DeliveryCaseParticipant::query()->withoutGlobalScopes()->updateOrCreate(
                [
                    'delivery_case_id' => $deliveryCase->id,
                    'person_id' => $participant['person_id'],
                    'role' => $participant['role']->value,
                    'valid_from' => $participant['valid_from'],
                ],
                [
                    'tenant_id' => $deliveryCase->tenant_id,
                    'source_provider' => 'github',
                    'source_ref' => $participant['source_ref'],
                    'confidence' => DeliveryAssociationConfidence::High,
                    'valid_to' => null,
                    'metadata' => ['github_login' => $participant['login']],
                ],
            );
            $projectedIds[] = $projected->id;
        }

        $stale = DeliveryCaseParticipant::query()
            ->withoutGlobalScopes()
            ->where('delivery_case_id', $deliveryCase->id)
            ->where('source_provider', 'github')
            ->whereIn('role', [
                DeliveryParticipantRole::CodeAuthor->value,
                DeliveryParticipantRole::Reviewer->value,
            ]);

        if ($projectedIds !== []) {
            $stale->whereNotIn('id', $projectedIds);
        }

        $stale->delete();
    }

    /**
     * @param  array<int, array{person_id: int, role: DeliveryParticipantRole, valid_from: CarbonInterface, source_ref: string, login: string}>  $participants
     */
    private function addParticipant(
        array &$participants,
        IntegrationWebhookEvent $event,
        string $login,
        DeliveryParticipantRole $role,
    ): void {
        if ($this->isBot($login)) {
            return;
        }

        $personId = $this->personIdForLogin($event, $login);

        if ($personId === null) {
            return;
        }

        $key = $personId.':'.$role->value;

        if (isset($participants[$key])) {
            return;
        }

        $participants[$key] = [
            'person_id' => $personId,
            'role' => $role,
            'valid_from' => $this->occurredAt($event),
            'source_ref' => $event->event_id,
            'login' => $login,
        ];
    }

    private function personIdForLogin(IntegrationWebhookEvent $event, string $login): ?int
    {
        $externalCode = 'github_user:'.$login;
        $personId = PersonExternalIdentity::query()
            ->withoutGlobalScopes()
            ->where('tenant_id', $event->tenant_id)
            ->where('external_code', $externalCode)
            ->where('active', true)
            ->whereHas('integrationSystem', fn ($query) => $query
                ->withoutGlobalScope('tenant')
                ->whereIn('provider', ['github', 'github-actions']))
            ->value('person_id');

        if ($personId !== null) {
            return (int) $personId;
        }

        return Person::query()
            ->withoutGlobalScopes()
            ->where('tenant_id', $event->tenant_id)
            ->whereRaw('LOWER(github_username) = ?', [Str::lower($login)])
            ->value('id');
    }

    private function taskReference(IntegrationWebhookEvent $event): ?string
    {
        if ((bool) data_get($event->normalized_payload, 'task_ref_ambiguous', false)) {
            return null;
        }

        $references = $this->taskReferences($event);

        if (count($references) === 1) {
            return $references[0];
        }

        $caseIds = DeliveryCaseExternalLink::query()
            ->withoutGlobalScopes()
            ->where('tenant_id', $event->tenant_id)
            ->whereIn('external_id', $this->externalIdsFor($event))
            ->pluck('delivery_case_id')
            ->unique()
            ->values();

        if ($caseIds->count() !== 1) {
            return null;
        }

        return DeliveryCase::query()
            ->withoutGlobalScopes()
            ->whereKey($caseIds->first())
            ->value('task_ref');
    }

    /**
     * @return list<string>
     */
    private function taskReferences(IntegrationWebhookEvent $event): array
    {
        return array_values(array_unique(array_filter(array_map(
            static fn (mixed $reference): ?string => is_string($reference) && trim($reference) !== ''
                ? Str::upper(trim($reference))
                : null,
            (array) data_get($event->normalized_payload, 'task_refs', []),
        ))));
    }

    /**
     * @return list<array{type: DeliveryExternalLinkType, external_id: string, metadata: array<string, mixed>}>
     */
    private function linksFor(IntegrationWebhookEvent $event): array
    {
        $repositoryId = data_get($event->normalized_payload, 'repository_id');

        if ($repositoryId === null || $repositoryId === '') {
            return [];
        }

        $links = [];
        $prId = data_get($event->normalized_payload, 'pr_id');
        $headSha = data_get($event->normalized_payload, 'head_sha');
        $workflowRunId = data_get($event->normalized_payload, 'workflow_run_id');
        $workflowAttempt = data_get($event->normalized_payload, 'workflow_run_attempt', 1);
        $deploymentId = data_get($event->normalized_payload, 'deployment_id');

        if ($prId !== null && $prId !== '') {
            $links[] = [
                'type' => DeliveryExternalLinkType::GitHubPullRequest,
                'external_id' => "github:{$repositoryId}:pr:{$prId}",
                'metadata' => ['number' => data_get($event->normalized_payload, 'pr_number')],
            ];
        }

        if (is_string($headSha) && $headSha !== '') {
            $links[] = [
                'type' => DeliveryExternalLinkType::GitHubHeadSha,
                'external_id' => "github:{$repositoryId}:sha:{$headSha}",
                'metadata' => [],
            ];
        }

        if ($workflowRunId !== null && $workflowRunId !== '') {
            $links[] = [
                'type' => DeliveryExternalLinkType::GitHubWorkflowRun,
                'external_id' => "github:{$repositoryId}:workflow:{$workflowRunId}:attempt:{$workflowAttempt}",
                'metadata' => ['workflow_id' => data_get($event->normalized_payload, 'workflow_id')],
            ];
        }

        if ($deploymentId !== null && $deploymentId !== '') {
            $links[] = [
                'type' => DeliveryExternalLinkType::GitHubDeployment,
                'external_id' => "github:{$repositoryId}:deployment:{$deploymentId}",
                'metadata' => ['environment' => data_get($event->normalized_payload, 'deployment_environment')],
            ];
        }

        return $links;
    }

    /**
     * @return list<string>
     */
    private function externalIdsFor(IntegrationWebhookEvent $event): array
    {
        return array_column($this->linksFor($event), 'external_id');
    }

    /**
     * @param  Collection<int, IntegrationWebhookEvent>  $events
     * @return list<array{type: DeliveryMilestoneType, semantic_key: string, metadata: array<string, mixed>}>
     */
    private function milestonesFor(IntegrationWebhookEvent $event, Collection $events): array
    {
        $payload = $event->normalized_payload ?? [];
        $eventType = (string) data_get($payload, 'event_type', '');
        $repositoryId = data_get($payload, 'repository_id');
        $milestones = [];

        if ($repositoryId === null || $repositoryId === '') {
            return $milestones;
        }

        $prId = data_get($payload, 'pr_id');

        if ($eventType === 'pull_request.opened' && $prId !== null) {
            $milestones[] = $this->milestone(
                DeliveryMilestoneType::PullRequestOpened,
                "github:pr:{$repositoryId}:{$prId}:opened",
                ['pr_number' => data_get($payload, 'pr_number')],
            );
        }

        if ($eventType === 'pull_request.closed' && (bool) data_get($payload, 'pr_merged', false) && $prId !== null) {
            $milestones[] = $this->milestone(
                DeliveryMilestoneType::PullRequestMerged,
                "github:pr:{$repositoryId}:{$prId}:merged",
                ['pr_number' => data_get($payload, 'pr_number')],
            );
        }

        $reviewId = data_get($payload, 'review_id');

        if ($eventType === 'pull_request_review.submitted' && $reviewId !== null) {
            $milestones[] = $this->milestone(
                DeliveryMilestoneType::ReviewSubmitted,
                "github:review:{$repositoryId}:{$reviewId}:submitted",
                ['state' => data_get($payload, 'review_state')],
            );
        }

        $workflowRunId = data_get($payload, 'workflow_run_id');

        if ($eventType === 'workflow_run.completed' && $workflowRunId !== null) {
            $attempt = data_get($payload, 'workflow_run_attempt', 1);
            $milestones[] = $this->milestone(
                DeliveryMilestoneType::CiCompleted,
                "github:workflow:{$repositoryId}:{$workflowRunId}:{$attempt}:completed",
                ['conclusion' => data_get($payload, 'workflow_run_conclusion')],
            );
        }

        $checkSuiteId = data_get($payload, 'check_suite_id');

        if ($eventType === 'check_suite.completed' && $checkSuiteId !== null && ! $this->hasWorkflowForSuite($events, $checkSuiteId)) {
            $milestones[] = $this->milestone(
                DeliveryMilestoneType::CiCompleted,
                "github:check-suite:{$repositoryId}:{$checkSuiteId}:completed",
                ['conclusion' => data_get($payload, 'check_suite_conclusion'), 'fallback' => true],
            );
        }

        $deploymentStatusId = data_get($payload, 'deployment_status_id');

        if ($eventType === 'deployment_status.created'
            && $deploymentStatusId !== null
            && $this->isTerminalDeploymentState(data_get($payload, 'deployment_status_state'))) {
            $milestones[] = $this->milestone(
                DeliveryMilestoneType::DeploymentCompleted,
                "github:deployment-status:{$repositoryId}:{$deploymentStatusId}",
                [
                    'environment' => data_get($payload, 'deployment_environment'),
                    'state' => data_get($payload, 'deployment_status_state'),
                ],
            );
        }

        return $milestones;
    }

    /**
     * @return array{type: DeliveryMilestoneType, semantic_key: string, metadata: array<string, mixed>}
     */
    private function milestone(DeliveryMilestoneType $type, string $semanticKey, array $metadata): array
    {
        return ['type' => $type, 'semantic_key' => $semanticKey, 'metadata' => $metadata];
    }

    /**
     * @param  Collection<int, IntegrationWebhookEvent>  $events
     */
    private function hasWorkflowForSuite(Collection $events, mixed $checkSuiteId): bool
    {
        return $events->contains(fn (IntegrationWebhookEvent $event): bool => data_get(
            $event->normalized_payload,
            'workflow_check_suite_id',
        ) === $checkSuiteId);
    }

    private function isTerminalDeploymentState(mixed $state): bool
    {
        return in_array($state, ['success', 'failure', 'error', 'inactive'], true);
    }

    private function sourceReference(IntegrationWebhookEvent $event): ?string
    {
        $repository = data_get($event->normalized_payload, 'repository_full_name');
        $prNumber = data_get($event->normalized_payload, 'pr_number');

        return is_string($repository) && $repository !== '' && $prNumber !== null
            ? $repository.'#'.$prNumber
            : null;
    }

    private function isBot(string $login): bool
    {
        return Str::endsWith(Str::lower($login), '[bot]');
    }

    private function occurredAt(IntegrationWebhookEvent $event): CarbonInterface
    {
        $occurredAt = data_get($event->normalized_payload, 'occurred_at');

        if (is_string($occurredAt) && $occurredAt !== '') {
            try {
                return CarbonImmutable::parse($occurredAt);
            } catch (Throwable) {
            }
        }

        $persistedAt = $event->received_at ?? $event->created_at;

        return $persistedAt instanceof DateTimeInterface
            ? CarbonImmutable::instance($persistedAt)
            : CarbonImmutable::createFromTimestampUTC(0);
    }
}
