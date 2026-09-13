<?php

namespace App\Services;

use App\Enums\DeliveryAssociationConfidence;
use App\Enums\DeliveryMilestoneType;
use App\Enums\DeliveryParticipantRole;
use App\Enums\DeliveryStage;
use App\Models\DeliveryCase;
use App\Models\DeliveryCaseMilestone;
use App\Models\DeliveryCaseParticipant;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use InvalidArgumentException;

final class DeliveryKpiSummaryService
{
    public const DEFINITION_VERSION = 'delivery-kpis.v1';

    /**
     * @param  array{
     *     person_id?: int,
     *     period_start?: CarbonInterface|string,
     *     period_end?: CarbonInterface|string,
     *     sprint?: string,
     *     minimum_confidence?: DeliveryAssociationConfidence|string
     * }  $filters
     * @return array<string, mixed>
     */
    public function summarize(int $tenantId, array $filters = []): array
    {
        if ($tenantId <= 0) {
            throw new InvalidArgumentException('A valid tenant id is required.');
        }

        $minimumConfidence = $this->minimumConfidence($filters['minimum_confidence'] ?? null);
        $includedConfidenceValues = $this->includedConfidenceValues($minimumConfidence);
        $periodStart = $this->filterDate($filters['period_start'] ?? null, false);
        $periodEnd = $this->filterDate($filters['period_end'] ?? null, true);

        if ($periodStart !== null && $periodEnd !== null && $periodEnd->lt($periodStart)) {
            throw new InvalidArgumentException('The period end must not be before the period start.');
        }

        $scopeQuery = DeliveryCase::query()
            ->withoutGlobalScopes()
            ->where('tenant_id', $tenantId);

        $this->applyCohortFilters($scopeQuery, $filters, $periodStart, $periodEnd);

        $casesInScope = (clone $scopeQuery)->count();
        $canceledCases = (clone $scopeQuery)
            ->where(function (Builder $query): void {
                $query
                    ->whereNotNull('canceled_at')
                    ->orWhere('current_stage', DeliveryStage::Rejected->value);
            })
            ->count();

        $eligibleQuery = (clone $scopeQuery)
            ->whereNull('canceled_at')
            ->where(function (Builder $query): void {
                $query
                    ->whereNull('current_stage')
                    ->orWhere('current_stage', '!=', DeliveryStage::Rejected->value);
            });

        $eligibleCases = (clone $eligibleQuery)->count();

        $this->applyParticipantFilter(
            $eligibleQuery,
            $tenantId,
            $includedConfidenceValues,
            $filters['person_id'] ?? null,
        );

        $cases = $eligibleQuery
            ->with([
                'milestones' => fn ($query) => $query
                    ->withoutGlobalScopes()
                    ->where('tenant_id', $tenantId)
                    ->oldest('occurred_at')
                    ->oldest('id'),
                'participants' => function ($query) use (
                    $tenantId,
                    $includedConfidenceValues,
                    $filters,
                ): void {
                    $query
                        ->withoutGlobalScopes()
                        ->where('tenant_id', $tenantId)
                        ->whereIn('role', $this->developerRoleValues())
                        ->whereIn('confidence', $includedConfidenceValues);

                    if (isset($filters['person_id'])) {
                        $query->where('person_id', (int) $filters['person_id']);
                    }
                },
            ])
            ->get();

        $developmentCycleHours = [];
        $qaWaitHours = [];
        $qaFirstPassCount = 0;
        $qaReworkCount = 0;
        $qaOutcomeSampleSize = 0;
        $publishedCases = collect();
        $blockedTotalHours = 0.0;
        $blockedClosedIntervalCount = 0;
        $blockedOpenIntervalCount = 0;
        $blockedCaseCount = 0;
        $completeFlowTimestampCount = 0;

        foreach ($cases as $case) {
            $milestones = $case->milestones;
            $developmentDuration = $this->durationBetween(
                $milestones,
                DeliveryMilestoneType::DevelopmentStarted,
                DeliveryMilestoneType::ImplementationCompleted,
            );
            $qaWaitDuration = $this->durationBetween(
                $milestones,
                DeliveryMilestoneType::ImplementationCompleted,
                DeliveryMilestoneType::QaStarted,
            );

            if ($developmentDuration !== null) {
                $developmentCycleHours[] = $developmentDuration;
            }

            if ($qaWaitDuration !== null) {
                $qaWaitHours[] = $qaWaitDuration;
            }

            if ($developmentDuration !== null && $qaWaitDuration !== null) {
                $completeFlowTimestampCount++;
            }

            $qaOutcome = $this->qaOutcome($milestones);

            if ($qaOutcome !== null) {
                $qaOutcomeSampleSize++;

                if ($qaOutcome === 'first_pass') {
                    $qaFirstPassCount++;
                } else {
                    $qaReworkCount++;
                }
            }

            if ($this->hasMilestone($milestones, DeliveryMilestoneType::Published)) {
                $publishedCases->push($case);
            }

            $blocked = $this->blockedIntervals($milestones);
            $blockedTotalHours += $blocked['total_hours'];
            $blockedClosedIntervalCount += $blocked['closed_intervals'];
            $blockedOpenIntervalCount += $blocked['open_intervals'];

            if ($blocked['closed_intervals'] > 0) {
                $blockedCaseCount++;
            }
        }

        $associatedCases = $cases->count();
        $publishedWithPoints = $publishedCases->filter(
            fn (DeliveryCase $case): bool => $case->story_points !== null,
        );

        return [
            'definition_version' => self::DEFINITION_VERSION,
            'filters' => [
                'tenant_id' => $tenantId,
                'person_id' => isset($filters['person_id']) ? (int) $filters['person_id'] : null,
                'period_start' => $periodStart?->toIso8601String(),
                'period_end' => $periodEnd?->toIso8601String(),
                'sprint' => $filters['sprint'] ?? null,
            ],
            'confidence' => [
                'minimum' => $minimumConfidence->value,
                'included_levels' => $includedConfidenceValues,
                'case_distribution' => $this->confidenceDistribution($cases),
            ],
            'coverage' => [
                'cases_in_scope' => $casesInScope,
                'canceled_cases_excluded' => $canceledCases,
                'eligible_cases' => $eligibleCases,
                'associated_cases' => $associatedCases,
                'association_rate_pct' => $this->percentage($associatedCases, $eligibleCases),
                'cases_with_complete_flow_timestamps' => $completeFlowTimestampCount,
                'timestamp_completeness_rate_pct' => $this->percentage(
                    $completeFlowTimestampCount,
                    $associatedCases,
                ),
            ],
            'published_delivery_count' => [
                'value' => $publishedCases->count(),
                'sample_size' => $publishedCases->count(),
                'coverage_rate_pct' => $this->percentage($publishedCases->count(), $associatedCases),
            ],
            'published_story_points' => [
                'value' => round($publishedWithPoints->sum(
                    fn (DeliveryCase $case): float => (float) $case->story_points,
                ), 2),
                'sample_size' => $publishedWithPoints->count(),
                'coverage_rate_pct' => $this->percentage(
                    $publishedWithPoints->count(),
                    $publishedCases->count(),
                ),
            ],
            'development_cycle_time_hours' => [
                'p50' => $this->median($developmentCycleHours),
                'sample_size' => count($developmentCycleHours),
                'coverage_rate_pct' => $this->percentage(count($developmentCycleHours), $associatedCases),
            ],
            'qa_wait_time_hours' => [
                'p50' => $this->median($qaWaitHours),
                'sample_size' => count($qaWaitHours),
                'coverage_rate_pct' => $this->percentage(count($qaWaitHours), $associatedCases),
            ],
            'qa_first_pass_rate' => [
                'value_pct' => $this->percentage($qaFirstPassCount, $qaOutcomeSampleSize),
                'numerator' => $qaFirstPassCount,
                'denominator' => $qaOutcomeSampleSize,
                'sample_size' => $qaOutcomeSampleSize,
                'coverage_rate_pct' => $this->percentage($qaOutcomeSampleSize, $associatedCases),
            ],
            'qa_rework_rate' => [
                'value_pct' => $this->percentage($qaReworkCount, $qaOutcomeSampleSize),
                'numerator' => $qaReworkCount,
                'denominator' => $qaOutcomeSampleSize,
                'sample_size' => $qaOutcomeSampleSize,
                'coverage_rate_pct' => $this->percentage($qaOutcomeSampleSize, $associatedCases),
            ],
            'blocked_time_hours' => [
                'total' => round($blockedTotalHours, 2),
                'sample_size' => $blockedCaseCount,
                'closed_intervals' => $blockedClosedIntervalCount,
                'open_intervals' => $blockedOpenIntervalCount,
                'coverage_rate_pct' => $this->percentage($blockedCaseCount, $associatedCases),
            ],
        ];
    }

    /**
     * @param  Builder<DeliveryCase>  $query
     * @param  array<string, mixed>  $filters
     */
    private function applyCohortFilters(
        Builder $query,
        array $filters,
        ?CarbonInterface $periodStart,
        ?CarbonInterface $periodEnd,
    ): void {
        if (isset($filters['sprint'])) {
            $query->where('sprint_ref', (string) $filters['sprint']);
        }

        if ($periodStart !== null) {
            $query->where(function (Builder $query) use ($periodStart): void {
                $query
                    ->where('completed_at', '>=', $periodStart)
                    ->orWhere(function (Builder $query) use ($periodStart): void {
                        $query
                            ->whereNull('completed_at')
                            ->where('last_activity_at', '>=', $periodStart);
                    });
            });
        }

        if ($periodEnd !== null) {
            $query->where(function (Builder $query) use ($periodEnd): void {
                $query
                    ->where('completed_at', '<=', $periodEnd)
                    ->orWhere(function (Builder $query) use ($periodEnd): void {
                        $query
                            ->whereNull('completed_at')
                            ->where('last_activity_at', '<=', $periodEnd);
                    });
            });
        }
    }

    /**
     * @param  Builder<DeliveryCase>  $query
     * @param  array<int, string>  $confidenceValues
     */
    private function applyParticipantFilter(
        Builder $query,
        int $tenantId,
        array $confidenceValues,
        mixed $personId,
    ): void {
        $query->whereHas('participants', function (Builder $query) use (
            $tenantId,
            $confidenceValues,
            $personId,
        ): void {
            $query
                ->withoutGlobalScopes()
                ->where('tenant_id', $tenantId)
                ->whereIn('role', $this->developerRoleValues())
                ->whereIn('confidence', $confidenceValues);

            if ($personId !== null) {
                $query->where('person_id', (int) $personId);
            }
        });
    }

    /**
     * @param  Collection<int, DeliveryCaseMilestone>  $milestones
     */
    private function durationBetween(
        Collection $milestones,
        DeliveryMilestoneType $startType,
        DeliveryMilestoneType $endType,
    ): ?float {
        $start = $milestones->first(
            fn (DeliveryCaseMilestone $milestone): bool => $milestone->milestone_type === $startType,
        )?->occurred_at;

        if (! $start instanceof CarbonInterface) {
            return null;
        }

        $end = $milestones->first(
            fn (DeliveryCaseMilestone $milestone): bool => $milestone->milestone_type === $endType
                && $milestone->occurred_at->greaterThanOrEqualTo($start),
        )?->occurred_at;

        if (! $end instanceof CarbonInterface) {
            return null;
        }

        return round(($end->getTimestamp() - $start->getTimestamp()) / 3600, 2);
    }

    /**
     * @param  Collection<int, DeliveryCaseMilestone>  $milestones
     */
    private function qaOutcome(Collection $milestones): ?string
    {
        $outcome = $milestones->first(
            fn (DeliveryCaseMilestone $milestone): bool => in_array(
                $milestone->milestone_type,
                [DeliveryMilestoneType::QaApproved, DeliveryMilestoneType::Published],
                true,
            ),
        );

        if (! $outcome instanceof DeliveryCaseMilestone) {
            return null;
        }

        $failedBeforeOutcome = $milestones->contains(
            fn (DeliveryCaseMilestone $milestone): bool => $milestone->milestone_type === DeliveryMilestoneType::QaFailed
                && $milestone->occurred_at->lt($outcome->occurred_at),
        );

        return $failedBeforeOutcome ? 'rework' : 'first_pass';
    }

    /**
     * @param  Collection<int, DeliveryCaseMilestone>  $milestones
     * @return array{total_hours: float, closed_intervals: int, open_intervals: int}
     */
    private function blockedIntervals(Collection $milestones): array
    {
        $openAt = null;
        $totalSeconds = 0;
        $closedIntervals = 0;

        foreach ($milestones as $milestone) {
            if ($milestone->milestone_type === DeliveryMilestoneType::Blocked && $openAt === null) {
                $openAt = $milestone->occurred_at;

                continue;
            }

            if ($milestone->milestone_type !== DeliveryMilestoneType::Unblocked || $openAt === null) {
                continue;
            }

            if ($milestone->occurred_at->greaterThanOrEqualTo($openAt)) {
                $totalSeconds += $milestone->occurred_at->getTimestamp() - $openAt->getTimestamp();
                $closedIntervals++;
            }

            $openAt = null;
        }

        return [
            'total_hours' => round($totalSeconds / 3600, 2),
            'closed_intervals' => $closedIntervals,
            'open_intervals' => $openAt === null ? 0 : 1,
        ];
    }

    /**
     * @param  Collection<int, DeliveryCaseMilestone>  $milestones
     */
    private function hasMilestone(Collection $milestones, DeliveryMilestoneType $type): bool
    {
        return $milestones->contains(
            fn (DeliveryCaseMilestone $milestone): bool => $milestone->milestone_type === $type,
        );
    }

    /**
     * @param  array<int, float>  $values
     */
    private function median(array $values): ?float
    {
        if ($values === []) {
            return null;
        }

        sort($values, SORT_NUMERIC);
        $count = count($values);
        $middle = intdiv($count, 2);

        if ($count % 2 === 1) {
            return round($values[$middle], 2);
        }

        return round(($values[$middle - 1] + $values[$middle]) / 2, 2);
    }

    private function percentage(int $numerator, int $denominator): ?float
    {
        if ($denominator === 0) {
            return null;
        }

        return round(($numerator / $denominator) * 100, 2);
    }

    private function minimumConfidence(mixed $value): DeliveryAssociationConfidence
    {
        if ($value instanceof DeliveryAssociationConfidence) {
            return $value;
        }

        if ($value === null) {
            return DeliveryAssociationConfidence::Medium;
        }

        $confidence = DeliveryAssociationConfidence::tryFrom((string) $value);

        if ($confidence === null) {
            throw new InvalidArgumentException('The minimum confidence must be high, medium, or low.');
        }

        return $confidence;
    }

    /**
     * @return array<int, string>
     */
    private function includedConfidenceValues(DeliveryAssociationConfidence $minimum): array
    {
        return match ($minimum) {
            DeliveryAssociationConfidence::High => [DeliveryAssociationConfidence::High->value],
            DeliveryAssociationConfidence::Medium => [
                DeliveryAssociationConfidence::High->value,
                DeliveryAssociationConfidence::Medium->value,
            ],
            DeliveryAssociationConfidence::Low => array_map(
                fn (DeliveryAssociationConfidence $confidence): string => $confidence->value,
                DeliveryAssociationConfidence::cases(),
            ),
        };
    }

    /**
     * @return array<int, string>
     */
    private function developerRoleValues(): array
    {
        return [
            DeliveryParticipantRole::Implementer->value,
            DeliveryParticipantRole::CodeAuthor->value,
        ];
    }

    private function filterDate(mixed $value, bool $endOfDay): ?CarbonImmutable
    {
        if ($value === null || $value === '') {
            return null;
        }

        $date = $value instanceof CarbonInterface
            ? CarbonImmutable::instance($value)
            : CarbonImmutable::parse((string) $value);

        if (is_string($value) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $value) === 1) {
            return $endOfDay ? $date->endOfDay() : $date->startOfDay();
        }

        return $date;
    }

    /**
     * @param  Collection<int, DeliveryCase>  $cases
     * @return array{high: int, medium: int, low: int}
     */
    private function confidenceDistribution(Collection $cases): array
    {
        $distribution = [
            DeliveryAssociationConfidence::High->value => 0,
            DeliveryAssociationConfidence::Medium->value => 0,
            DeliveryAssociationConfidence::Low->value => 0,
        ];

        foreach ($cases as $case) {
            $highestConfidence = $case->participants
                ->map(fn (DeliveryCaseParticipant $participant): DeliveryAssociationConfidence => $participant->confidence)
                ->sortBy(fn (DeliveryAssociationConfidence $confidence): int => match ($confidence) {
                    DeliveryAssociationConfidence::High => 0,
                    DeliveryAssociationConfidence::Medium => 1,
                    DeliveryAssociationConfidence::Low => 2,
                })
                ->first();

            if ($highestConfidence instanceof DeliveryAssociationConfidence) {
                $distribution[$highestConfidence->value]++;
            }
        }

        return $distribution;
    }
}
