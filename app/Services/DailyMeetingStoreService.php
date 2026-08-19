<?php

namespace App\Services;

use App\Contracts\Services\StoreServiceContract;
use App\Enums\DailyAnnotationType;
use App\Models\DailyMeeting;
use App\Models\DailyMeetingAnnotation;
use App\Models\DailyMeetingEntry;
use App\Models\Person;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * The project's first custom Store service: creating a DailyMeeting also creates every one of its
 * entries in the same transaction, which GenericStoreService's plain `create($attributes)` can't do
 * (the nested `entries` array would otherwise be silently dropped by #[Fillable]).
 *
 * Entries are written via a single bulk `insert()` (one query for N rows) rather than one `create()`
 * per entry (N queries) — a daily can have as many entries as the team has people, and this is a
 * write path that runs at the end of every single daily.
 */
final class DailyMeetingStoreService implements StoreServiceContract
{
    /**
     * @param  array<string, mixed>  $attributes
     *
     * @throws Throwable
     */
    public function store(array $attributes): Model
    {
        return DB::transaction(function () use ($attributes): DailyMeeting {
            $personTeamIds = Person::query()
                ->whereIn('id', Arr::pluck($attributes['entries'], 'person_id'))
                ->pluck('team_id', 'id');

            $meetingAttributes = Arr::except($attributes, ['entries', 'annotations']);
            $uniqueTeamIds = $personTeamIds->unique()->values();
            $meetingAttributes['team_id'] = $uniqueTeamIds->count() === 1 ? $uniqueTeamIds->first() : null;

            $meeting = DailyMeeting::query()->create($meetingAttributes);
            $tenantId = $meeting->tenant_id;

            $now = now();
            $rows = array_map(
                fn (array $entry, int $index): array => [
                    'daily_meeting_id' => $meeting->id,
                    'tenant_id' => $tenantId,
                    'team_id' => $personTeamIds[$entry['person_id']],
                    'person_id' => $entry['person_id'],
                    'speaking_order' => $index,
                    'allotted_seconds' => $meeting->time_limit_seconds,
                    'actual_seconds' => $entry['actual_seconds'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                $attributes['entries'],
                array_keys($attributes['entries']),
            );

            DailyMeetingEntry::query()->insert($rows);

            $annotations = $attributes['annotations'] ?? [];
            if ($annotations !== []) {
                DailyMeetingAnnotation::query()->insert(array_map(
                    fn (array $annotation): array => [
                        'daily_meeting_id' => $meeting->id,
                        'tenant_id' => $tenantId,
                        'person_id' => $annotation['person_id'] ?? null,
                        'type' => $annotation['type'],
                        'text' => $annotation['text'],
                        'resolved' => $annotation['type'] === DailyAnnotationType::Blocker->value
                            ? (bool) ($annotation['resolved'] ?? false)
                            : false,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ],
                    $annotations,
                ));
            }

            return $meeting->load(['entries', 'annotations']);
        });
    }
}
