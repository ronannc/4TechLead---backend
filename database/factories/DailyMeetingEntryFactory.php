<?php

namespace Database\Factories;

use App\Models\DailyMeeting;
use App\Models\DailyMeetingEntry;
use App\Models\Person;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DailyMeetingEntry>
 */
class DailyMeetingEntryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * Defaults to a brand new, unrelated DailyMeeting/Team/Person — use forMeeting() whenever the
     * entry must belong to a specific meeting, so team_id/person_id/daily_meeting_id stay consistent.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'daily_meeting_id' => DailyMeeting::factory(),
            'team_id' => Team::factory(),
            'person_id' => Person::factory(),
            'speaking_order' => 0,
            'allotted_seconds' => 90,
            'actual_seconds' => fake()->numberBetween(30, 120),
            'note_type' => null,
            'note' => null,
        ];
    }

    /**
     * Derives team_id/daily_meeting_id from an existing meeting, and person_id from a person on that
     * same team, so the entry is internally consistent (never a person from a different team).
     */
    public function forMeeting(DailyMeeting $meeting, ?Person $person = null): static
    {
        return $this->state(fn (): array => [
            'daily_meeting_id' => $meeting->id,
            'team_id' => $meeting->team_id,
            'person_id' => $person?->id ?? Person::factory(['team_id' => $meeting->team_id]),
        ]);
    }
}
