<?php

namespace App\Services;

use App\Enums\DeliveryAssociationConfidence;
use App\Enums\DeliveryExternalLinkType;
use App\Enums\DeliveryMilestoneType;
use App\Enums\DeliveryParticipantRole;
use App\Enums\DeliveryStage;
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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

final class DeliveryCaseProjector
{
    public function project(IntegrationWebhookEvent $event): ?DeliveryCase
    {
        if ($event->tenant_id === null || data_get($event->normalized_payload, 'source') !== 'clickup') {
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
            $events = $this->eventsFor($event, $taskReference);

            $this->projectCaseState($deliveryCase, $events);
            $this->projectClickUpTaskLink($deliveryCase, $events);
            $this->projectStatusMilestones($deliveryCase, $events);
            $this->projectTagConfirmations($deliveryCase, $events);
            $this->projectParticipants($deliveryCase, $events);

            return $deliveryCase->refresh();
        });
    }

    /**
     * @return Collection<int, IntegrationWebhookEvent>
     */
    private function eventsFor(IntegrationWebhookEvent $event, string $taskReference): Collection
    {
        $events = IntegrationWebhookEvent::query()
            ->withoutGlobalScope('tenant')
            ->where('tenant_id', $event->tenant_id)
            ->whereHas('integrationSystem', fn ($query) => $query
                ->withoutGlobalScope('tenant')
                ->where('provider', 'clickup'))
            ->whereNotNull('normalized_payload')
            ->where('normalized_payload->source', 'clickup')
            ->where(function (Builder $query) use ($taskReference): void {
                $query
                    ->whereJsonContains('normalized_payload->task_refs', $taskReference)
                    ->orWhere('normalized_payload->task_custom_id', $taskReference);
            })
            ->get()
            ->filter(fn (IntegrationWebhookEvent $candidate): bool => in_array(
                $taskReference,
                $this->taskReferences($candidate),
                true,
            ));

        return $events->sort(function (IntegrationWebhookEvent $left, IntegrationWebhookEvent $right): int {
            $occurredAtComparison = strcmp(
                $this->occurredAt($left)->format('U.u'),
                $this->occurredAt($right)->format('U.u'),
            );

            if ($occurredAtComparison !== 0) {
                return $occurredAtComparison;
            }

            $eventIdComparison = strcmp($left->event_id, $right->event_id);

            return $eventIdComparison !== 0 ? $eventIdComparison : $left->id <=> $right->id;
        })->values();
    }

    /**
     * @param  Collection<int, IntegrationWebhookEvent>  $events
     */
    private function projectCaseState(DeliveryCase $deliveryCase, Collection $events): void
    {
        $firstEvent = $events->first();
        $lastEvent = $events->last();
        $latestStageEvent = $events->reverse()->first(
            fn (IntegrationWebhookEvent $event): bool => $this->stageFor($event) !== null,
        );
        $latestStage = $latestStageEvent === null ? null : $this->stageFor($latestStageEvent);

        $deliveryCase->forceFill([
            'title' => $this->latestValue($events, 'task_name'),
            'current_stage' => $latestStage,
            'sprint_ref' => $this->latestListId($events),
            'story_points' => $this->latestNumericValue($events, 'task_sprint_points'),
            'first_seen_at' => $firstEvent === null ? $deliveryCase->first_seen_at : $this->occurredAt($firstEvent),
            'last_activity_at' => $lastEvent === null ? $deliveryCase->last_activity_at : $this->occurredAt($lastEvent),
            'completed_at' => $latestStage === DeliveryStage::Published && $latestStageEvent !== null
                ? $this->stageEnteredAt($events, DeliveryStage::Published, $latestStageEvent)
                : null,
            'canceled_at' => $latestStage === DeliveryStage::Rejected && $latestStageEvent !== null
                ? $this->stageEnteredAt($events, DeliveryStage::Rejected, $latestStageEvent)
                : null,
        ])->save();
    }

    /**
     * @param  Collection<int, IntegrationWebhookEvent>  $events
     */
    private function projectClickUpTaskLink(DeliveryCase $deliveryCase, Collection $events): void
    {
        $linkEvent = $events->reverse()->first(
            fn (IntegrationWebhookEvent $event): bool => $this->stringValue($event, 'task_id') !== null,
        );

        if ($linkEvent === null) {
            return;
        }

        $taskId = $this->stringValue($linkEvent, 'task_id');

        if ($taskId === null) {
            return;
        }

        DeliveryCaseExternalLink::query()->withoutGlobalScopes()->updateOrCreate(
            [
                'tenant_id' => $deliveryCase->tenant_id,
                'link_type' => DeliveryExternalLinkType::ClickUpTask->value,
                'external_id' => $taskId,
            ],
            [
                'delivery_case_id' => $deliveryCase->id,
                'integration_system_id' => $linkEvent->integration_system_id,
                'integration_webhook_event_id' => $linkEvent->id,
                'source_ref' => $this->stringValue($linkEvent, 'task_url')
                    ?? $this->stringValue($linkEvent, 'source_ref'),
                'metadata' => ['task_ref' => $deliveryCase->task_ref],
            ],
        );
    }

    /**
     * @param  Collection<int, IntegrationWebhookEvent>  $events
     */
    private function projectStatusMilestones(DeliveryCase $deliveryCase, Collection $events): void
    {
        $previousStage = null;
        $projectedMilestoneIds = [];

        foreach ($events as $event) {
            $stage = $this->stageFor($event);

            if ($stage === null || $stage === $previousStage) {
                continue;
            }

            foreach ($this->milestonesForTransition($previousStage, $stage) as $milestoneType) {
                $occurredAt = $this->occurredAt($event);

                $semanticKey = implode(':', [
                    'clickup',
                    'status',
                    $milestoneType->value,
                    $occurredAt->format('YmdHis.u'),
                ],
                );
                $milestone = DeliveryCaseMilestone::query()->withoutGlobalScopes()->updateOrCreate(
                    [
                        'delivery_case_id' => $deliveryCase->id,
                        'semantic_key' => $semanticKey,
                    ],
                    [
                        'tenant_id' => $deliveryCase->tenant_id,
                        'integration_webhook_event_id' => $event->id,
                        'milestone_type' => $milestoneType,
                        'source_provider' => 'clickup',
                        'source_ref' => $this->stringValue($event, 'task_id'),
                        'occurred_at' => $occurredAt,
                        'metadata' => [
                            'from_stage' => $previousStage?->value,
                            'to_stage' => $stage->value,
                            'status' => $this->stringValue($event, 'history_after')
                                ?? $this->stringValue($event, 'task_status'),
                        ],
                    ],
                );
                $projectedMilestoneIds[] = $milestone->id;
            }

            $previousStage = $stage;
        }

        $this->deleteStaleMilestones(
            $deliveryCase,
            $this->clickUpStatusMilestoneTypes(),
            $projectedMilestoneIds,
        );
    }

    /**
     * @param  Collection<int, IntegrationWebhookEvent>  $events
     */
    private function projectParticipants(DeliveryCase $deliveryCase, Collection $events): void
    {
        $personIdsByAssignee = $this->personIdsByAssignee($deliveryCase, $events);
        $rolesByPerson = [];
        $activeIntervalByPerson = [];
        $intervals = [];

        foreach ($events as $event) {
            $stage = $this->stageFor($event);
            $normalizedPayload = $event->normalized_payload ?? [];

            if ($stage === null || ! array_key_exists('task_assignees', $normalizedPayload)) {
                continue;
            }

            if ($normalizedPayload['task_assignees'] === []
                && ($normalizedPayload['task_assignees_authoritative'] ?? null) === false) {
                continue;
            }

            $currentPersonIds = [];
            $hadKnownImplementerBeforeSnapshot = in_array(
                DeliveryParticipantRole::Implementer,
                $rolesByPerson,
                true,
            );

            foreach ((array) data_get($normalizedPayload, 'task_assignees', []) as $assignee) {
                if (! is_array($assignee)) {
                    continue;
                }

                $externalCode = Arr::get($assignee, 'external_code');

                if (! is_string($externalCode) || $externalCode === '') {
                    continue;
                }

                $personId = $personIdsByAssignee[$event->integration_system_id.':'.$externalCode] ?? null;

                if ($personId === null) {
                    continue;
                }

                $role = $rolesByPerson[$personId]
                    ?? $this->roleForNewAssignee(
                        $stage,
                        $hadKnownImplementerBeforeSnapshot,
                    );

                if ($role === null) {
                    continue;
                }

                $rolesByPerson[$personId] = $role;
                $currentPersonIds[$personId] = true;

                if (! isset($activeIntervalByPerson[$personId])) {
                    $intervals[] = [
                        'person_id' => $personId,
                        'role' => $role,
                        'source_ref' => $event->event_id,
                        'confidence' => $role === DeliveryParticipantRole::Implementer
                            ? DeliveryAssociationConfidence::High
                            : DeliveryAssociationConfidence::Medium,
                        'valid_from' => $this->occurredAt($event),
                        'valid_to' => null,
                        'metadata' => ['external_code' => $externalCode],
                    ];
                    $activeIntervalByPerson[$personId] = array_key_last($intervals);
                }
            }

            foreach ($activeIntervalByPerson as $personId => $intervalIndex) {
                if (! isset($currentPersonIds[$personId])) {
                    $intervals[$intervalIndex]['valid_to'] = $this->occurredAt($event);
                    unset($activeIntervalByPerson[$personId]);
                }
            }
        }

        $projectedParticipantIds = [];

        foreach ($intervals as $participant) {
            $projectedParticipant = DeliveryCaseParticipant::query()->withoutGlobalScopes()->updateOrCreate(
                [
                    'delivery_case_id' => $deliveryCase->id,
                    'person_id' => $participant['person_id'],
                    'role' => $participant['role'],
                    'valid_from' => $participant['valid_from'],
                ],
                [
                    'tenant_id' => $deliveryCase->tenant_id,
                    'source_provider' => 'clickup',
                    'source_ref' => $participant['source_ref'],
                    'confidence' => $participant['confidence'],
                    'valid_to' => $participant['valid_to'],
                    'metadata' => $participant['metadata'],
                ],
            );
            $projectedParticipantIds[] = $projectedParticipant->id;
        }

        $staleParticipants = DeliveryCaseParticipant::query()
            ->withoutGlobalScopes()
            ->where('delivery_case_id', $deliveryCase->id)
            ->where('source_provider', 'clickup')
            ->whereIn('role', [
                DeliveryParticipantRole::Implementer->value,
                DeliveryParticipantRole::Qa->value,
            ]);

        if ($projectedParticipantIds !== []) {
            $staleParticipants->whereNotIn('id', $projectedParticipantIds);
        }

        $staleParticipants->delete();
    }

    private function roleForNewAssignee(
        DeliveryStage $stage,
        bool $hasKnownImplementer,
    ): ?DeliveryParticipantRole {
        if (in_array($stage, [
            DeliveryStage::Analysis,
            DeliveryStage::InProgress,
            DeliveryStage::Blocked,
        ], true)) {
            return DeliveryParticipantRole::Implementer;
        }

        if ($stage === DeliveryStage::ReadyForQa) {
            return $hasKnownImplementer
                ? DeliveryParticipantRole::Qa
                : DeliveryParticipantRole::Implementer;
        }

        if (in_array($stage, [
            DeliveryStage::InQa,
            DeliveryStage::QaFailed,
            DeliveryStage::ReadyToPublish,
        ], true)) {
            return DeliveryParticipantRole::Qa;
        }

        return null;
    }

    /**
     * @param  Collection<int, IntegrationWebhookEvent>  $events
     */
    private function projectTagConfirmations(DeliveryCase $deliveryCase, Collection $events): void
    {
        $observedTags = [];
        $projectedMilestoneIds = [];

        foreach ($events as $event) {
            $normalizedPayload = $event->normalized_payload ?? [];

            if (! array_key_exists('task_tags', $normalizedPayload)) {
                continue;
            }

            $currentTags = array_values(array_unique(array_filter(array_map(
                static fn (mixed $tag): ?string => is_string($tag) && trim($tag) !== ''
                    ? Str::upper(trim($tag))
                    : null,
                (array) data_get($normalizedPayload, 'task_tags', []),
            ))));

            foreach ([
                'HOMOLOG' => DeliveryMilestoneType::Homologated,
                'MERGIADO' => DeliveryMilestoneType::PullRequestMerged,
            ] as $tag => $milestoneType) {
                if (! in_array($tag, $currentTags, true) || isset($observedTags[$tag])) {
                    continue;
                }

                $occurredAt = $this->occurredAt($event);

                $semanticKey = implode(':', [
                    'clickup',
                    'tag',
                    $milestoneType->value,
                    $occurredAt->format('YmdHis.u'),
                ],
                );
                $milestone = DeliveryCaseMilestone::query()->withoutGlobalScopes()->updateOrCreate(
                    [
                        'delivery_case_id' => $deliveryCase->id,
                        'semantic_key' => $semanticKey,
                    ],
                    [
                        'tenant_id' => $deliveryCase->tenant_id,
                        'integration_webhook_event_id' => $event->id,
                        'milestone_type' => $milestoneType,
                        'source_provider' => 'clickup',
                        'source_ref' => $this->stringValue($event, 'task_id'),
                        'occurred_at' => $occurredAt,
                        'metadata' => [
                            'tag' => $tag,
                            'evidence_role' => 'confirmation',
                        ],
                    ],
                );
                $projectedMilestoneIds[] = $milestone->id;

                $observedTags[$tag] = true;
            }
        }

        $this->deleteStaleMilestones(
            $deliveryCase,
            [
                DeliveryMilestoneType::Homologated,
                DeliveryMilestoneType::PullRequestMerged,
            ],
            $projectedMilestoneIds,
        );
    }

    /**
     * @param  array<int, DeliveryMilestoneType>  $milestoneTypes
     * @param  array<int, int>  $projectedMilestoneIds
     */
    private function deleteStaleMilestones(
        DeliveryCase $deliveryCase,
        array $milestoneTypes,
        array $projectedMilestoneIds,
    ): void {
        $staleMilestones = DeliveryCaseMilestone::query()
            ->withoutGlobalScopes()
            ->where('delivery_case_id', $deliveryCase->id)
            ->where('source_provider', 'clickup')
            ->whereIn('milestone_type', array_map(
                static fn (DeliveryMilestoneType $type): string => $type->value,
                $milestoneTypes,
            ));

        if ($projectedMilestoneIds !== []) {
            $staleMilestones->whereNotIn('id', $projectedMilestoneIds);
        }

        $staleMilestones->delete();
    }

    /**
     * @param  Collection<int, IntegrationWebhookEvent>  $events
     * @return array<string, int>
     */
    private function personIdsByAssignee(DeliveryCase $deliveryCase, Collection $events): array
    {
        $integrationSystemIds = $events->pluck('integration_system_id')->unique()->values();
        $externalCodes = $events->flatMap(fn (IntegrationWebhookEvent $event): array => array_values(array_filter(
            array_map(
                static fn (mixed $assignee): ?string => is_array($assignee)
                    ? Arr::get($assignee, 'external_code')
                    : null,
                (array) data_get($event->normalized_payload, 'task_assignees', []),
            ),
            static fn (mixed $externalCode): bool => is_string($externalCode) && $externalCode !== '',
        )))->unique()->values();

        if ($externalCodes->isEmpty()) {
            return [];
        }

        $identities = PersonExternalIdentity::query()
            ->withoutGlobalScopes()
            ->where('tenant_id', $deliveryCase->tenant_id)
            ->whereIn('integration_system_id', $integrationSystemIds)
            ->whereIn('external_code', $externalCodes)
            ->where('active', true)
            ->get();
        $clickUpUserIds = $externalCodes
            ->filter(fn (string $externalCode): bool => Str::startsWith($externalCode, 'clickup_user:'))
            ->map(fn (string $externalCode): string => Str::after($externalCode, 'clickup_user:'))
            ->filter()
            ->values();
        $fallbackPersonIds = Person::query()
            ->withoutGlobalScopes()
            ->where('tenant_id', $deliveryCase->tenant_id)
            ->whereIn('clickup_user_id', $clickUpUserIds)
            ->pluck('id', 'clickup_user_id');
        $personIds = [];

        foreach ($events as $event) {
            foreach ($externalCodes as $externalCode) {
                $identity = $identities->first(fn (PersonExternalIdentity $candidate): bool => $candidate->integration_system_id === $event->integration_system_id
                    && $candidate->external_code === $externalCode);
                $clickUpUserId = Str::startsWith($externalCode, 'clickup_user:')
                    ? Str::after($externalCode, 'clickup_user:')
                    : null;
                $personId = $identity?->person_id
                    ?? ($clickUpUserId === null ? null : $fallbackPersonIds->get($clickUpUserId));

                if ($personId !== null) {
                    $personIds[$event->integration_system_id.':'.$externalCode] = (int) $personId;
                }
            }
        }

        return $personIds;
    }

    private function taskReference(IntegrationWebhookEvent $event): ?string
    {
        $reference = data_get($event->normalized_payload, 'task_custom_id')
            ?? Arr::first((array) data_get($event->normalized_payload, 'task_refs', []));

        if (! is_string($reference) || trim($reference) === '') {
            return null;
        }

        return Str::upper(trim($reference));
    }

    /**
     * @return array<int, string>
     */
    private function taskReferences(IntegrationWebhookEvent $event): array
    {
        $references = (array) data_get($event->normalized_payload, 'task_refs', []);
        $customId = data_get($event->normalized_payload, 'task_custom_id');

        if (is_string($customId)) {
            $references[] = $customId;
        }

        return array_values(array_unique(array_filter(array_map(
            static fn (mixed $reference): ?string => is_string($reference) && trim($reference) !== ''
                ? Str::upper(trim($reference))
                : null,
            $references,
        ))));
    }

    private function stageFor(IntegrationWebhookEvent $event): ?DeliveryStage
    {
        foreach (['history_after_stage', 'task_stage'] as $key) {
            $stage = data_get($event->normalized_payload, $key);

            if (is_string($stage) && DeliveryStage::tryFrom($stage) !== null) {
                return DeliveryStage::from($stage);
            }
        }

        return DeliveryStage::fromClickUpStatus(
            data_get($event->normalized_payload, 'history_after')
                ?? data_get($event->normalized_payload, 'task_status'),
        );
    }

    /**
     * @return array<int, DeliveryMilestoneType>
     */
    private function milestonesForTransition(?DeliveryStage $from, DeliveryStage $to): array
    {
        $milestones = $from === DeliveryStage::Blocked && $to !== DeliveryStage::Blocked
            ? [DeliveryMilestoneType::Unblocked]
            : [];
        $next = match ($to) {
            DeliveryStage::Analysis => [DeliveryMilestoneType::AnalysisStarted],
            DeliveryStage::InProgress => [DeliveryMilestoneType::DevelopmentStarted],
            DeliveryStage::Blocked => [DeliveryMilestoneType::Blocked],
            DeliveryStage::ReadyForQa => [DeliveryMilestoneType::ImplementationCompleted],
            DeliveryStage::InQa => [DeliveryMilestoneType::QaStarted],
            DeliveryStage::QaFailed => [DeliveryMilestoneType::QaFailed],
            DeliveryStage::ReadyToPublish => [
                DeliveryMilestoneType::QaApproved,
                DeliveryMilestoneType::ReadyToPublish,
            ],
            DeliveryStage::Published => [DeliveryMilestoneType::Published],
            DeliveryStage::Rejected => [DeliveryMilestoneType::Rejected],
            DeliveryStage::Pending => [],
        };

        return [...$milestones, ...$next];
    }

    /**
     * @return array<int, DeliveryMilestoneType>
     */
    private function clickUpStatusMilestoneTypes(): array
    {
        return [
            DeliveryMilestoneType::AnalysisStarted,
            DeliveryMilestoneType::DevelopmentStarted,
            DeliveryMilestoneType::Blocked,
            DeliveryMilestoneType::Unblocked,
            DeliveryMilestoneType::ImplementationCompleted,
            DeliveryMilestoneType::QaStarted,
            DeliveryMilestoneType::QaFailed,
            DeliveryMilestoneType::QaApproved,
            DeliveryMilestoneType::ReadyToPublish,
            DeliveryMilestoneType::Published,
            DeliveryMilestoneType::Rejected,
        ];
    }

    /**
     * @param  Collection<int, IntegrationWebhookEvent>  $events
     */
    private function stageEnteredAt(
        Collection $events,
        DeliveryStage $stage,
        IntegrationWebhookEvent $fallbackEvent,
    ): CarbonInterface {
        $previousStage = null;
        $enteredAt = null;

        foreach ($events as $event) {
            $eventStage = $this->stageFor($event);

            if ($eventStage === $stage && $previousStage !== $stage) {
                $enteredAt = $this->occurredAt($event);
            }

            if ($eventStage !== null) {
                $previousStage = $eventStage;
            }
        }

        return $enteredAt ?? $this->occurredAt($fallbackEvent);
    }

    /**
     * @param  Collection<int, IntegrationWebhookEvent>  $events
     */
    private function latestValue(Collection $events, string $key): mixed
    {
        foreach ($events->reverse() as $event) {
            $value = data_get($event->normalized_payload, $key);

            if ($value !== null && $value !== '') {
                return $value;
            }
        }

        return null;
    }

    /**
     * @param  Collection<int, IntegrationWebhookEvent>  $events
     */
    private function latestNumericValue(Collection $events, string $key): float|int|null
    {
        $value = $this->latestValue($events, $key);

        return is_numeric($value) ? $value + 0 : null;
    }

    /**
     * @param  Collection<int, IntegrationWebhookEvent>  $events
     */
    private function latestListId(Collection $events): ?string
    {
        foreach ($events->reverse() as $event) {
            $listId = Arr::first((array) data_get($event->normalized_payload, 'list_ids', []));

            if ($listId !== null && $listId !== '') {
                return (string) $listId;
            }
        }

        return null;
    }

    private function stringValue(IntegrationWebhookEvent $event, string $key): ?string
    {
        $value = data_get($event->normalized_payload, $key);

        return is_string($value) && $value !== '' ? $value : null;
    }

    private function occurredAt(IntegrationWebhookEvent $event): CarbonInterface
    {
        $occurredAt = data_get($event->normalized_payload, 'occurred_at');

        if ($occurredAt !== null && $occurredAt !== '') {
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
