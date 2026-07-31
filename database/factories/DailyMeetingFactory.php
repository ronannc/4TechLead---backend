<?php

namespace Database\Factories;

use App\Models\DailyMeeting;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DailyMeeting>
 */
class DailyMeetingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startedAt = fake()->dateTimeBetween('-30 days', 'now');
        $endedAt = (clone $startedAt)->modify('+15 minutes');

        return [
            'team_id' => Team::factory(),
            'time_limit_seconds' => fake()->randomElement([60, 90, 120, 150]),
            'started_at' => $startedAt,
            'ended_at' => $endedAt,
        ];
    }
}
