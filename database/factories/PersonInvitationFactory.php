<?php

namespace Database\Factories;

use App\Models\Person;
use App\Models\PersonInvitation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PersonInvitation>
 */
class PersonInvitationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'person_id' => Person::factory(),
            'invited_by_user_id' => User::factory(),
            'email' => fake()->unique()->safeEmail(),
            'token_hash' => hash('sha256', fake()->regexify('[A-Z2-9]{6}')),
            'expires_at' => now()->addDays(7),
        ];
    }
}
