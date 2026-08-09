<?php

namespace App\Models;

use App\Enums\DailyAnnotationType;
use Database\Factories\DailyMeetingAnnotationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'daily_meeting_id',
    'person_id',
    'type',
    'text',
    'resolved',
])]
class DailyMeetingAnnotation extends Model
{
    /** @use HasFactory<DailyMeetingAnnotationFactory> */
    use HasFactory;

    /**
     * @return BelongsTo<DailyMeeting, $this>
     */
    public function dailyMeeting(): BelongsTo
    {
        return $this->belongsTo(DailyMeeting::class);
    }

    /**
     * @return BelongsTo<Person, $this>
     */
    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => DailyAnnotationType::class,
            'resolved' => 'boolean',
        ];
    }
}
