<?php

namespace App\Models;

use App\Models\Concerns\Filterable;
use Database\Factories\DailyMeetingFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'team_id',
    'time_limit_seconds',
    'started_at',
    'ended_at',
])]
class DailyMeeting extends Model
{
    /** @use HasFactory<DailyMeetingFactory> */
    use Filterable, HasFactory;

    /**
     * Always eager-loaded — CrudControllerTrait/GenericIndexService do not eager-load relations
     * themselves, and every consumer of this resource needs the entries with names.
     *
     * @var array<int, string>
     */
    protected $with = ['entries.person'];

    /**
     * @return BelongsTo<Team, $this>
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * @return HasMany<DailyMeetingEntry, $this>
     */
    public function entries(): HasMany
    {
        return $this->hasMany(DailyMeetingEntry::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'ended_at' => 'datetime',
        ];
    }

    /**
     * @return array<int, string>
     */
    protected function filterableFields(): array
    {
        return ['team_id'];
    }

    /**
     * @return array<int, string>
     */
    protected function sortableFields(): array
    {
        return ['started_at', 'created_at'];
    }
}
