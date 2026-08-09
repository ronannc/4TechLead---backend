<?php

namespace Database\Factories;

use App\Enums\DailyAnnotationType;
use App\Models\DailyMeeting;
use App\Models\DailyMeetingAnnotation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DailyMeetingAnnotation>
 */
class DailyMeetingAnnotationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'daily_meeting_id' => DailyMeeting::factory(),
            'person_id' => null,
            'type' => fake()->randomElement(DailyAnnotationType::cases()),
            'text' => fake()->sentence(),
            'resolved' => false,
        ];
    }
}
