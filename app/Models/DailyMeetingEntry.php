<?php

namespace App\Models;

use App\Enums\DailyEntryStatus;
use App\Models\Concerns\Filterable;
use Database\Factories\DailyMeetingEntryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One person's speaking turn within a DailyMeeting.
 *
 * `allotted_seconds` is a copy of the parent meeting's `time_limit_seconds` at the time the turn was
 * recorded — today it is always identical across every entry of the same meeting, kept as its own
 * column to leave room for a future per-person time limit without a schema change.
 */
#[Fillable([
    'daily_meeting_id',
    'team_id',
    'person_id',
    'speaking_order',
    'allotted_seconds',
    'actual_seconds',
])]
class DailyMeetingEntry extends Model
{
    /** @use HasFactory<DailyMeetingEntryFactory> */
    use Filterable, HasFactory;

    /**
     * Below this fraction of `allotted_seconds`, a turn is considered "spoke too little" rather than
     * "on time". Shared with the frontend, which replicates this exact ratio for its own display.
     */
    public const float SPOKE_TOO_LITTLE_RATIO = 0.2;

    /**
     * @return BelongsTo<DailyMeeting, $this>
     */
    public function dailyMeeting(): BelongsTo
    {
        return $this->belongsTo(DailyMeeting::class);
    }

    /**
     * @return BelongsTo<Team, $this>
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * @return BelongsTo<Person, $this>
     */
    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    /**
     * Derived from actual_seconds vs allotted_seconds — never stored, always computed on read.
     *
     * @return Attribute<DailyEntryStatus, never>
     */
    protected function status(): Attribute
    {
        return Attribute::make(get: function (): DailyEntryStatus {
            if ($this->actual_seconds > $this->allotted_seconds) {
                return DailyEntryStatus::Burned;
            }

            if ($this->actual_seconds < $this->allotted_seconds * self::SPOKE_TOO_LITTLE_RATIO) {
                return DailyEntryStatus::SpokeTooLittle;
            }

            return DailyEntryStatus::OnTime;
        });
    }

    /**
     * @return array<int, string>
     */
    protected function filterableFields(): array
    {
        return ['team_id', 'person_id', 'daily_meeting_id'];
    }

    /**
     * @return array<int, string>
     */
    protected function sortableFields(): array
    {
        return ['created_at', 'actual_seconds', 'speaking_order'];
    }
}
