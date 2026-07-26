<?php

namespace Database\Factories;

use App\Enums\ContractType;
use App\Enums\SeniorityLevel;
use App\Models\Person;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Person>
 */
class PersonFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $birthDate = fake()->dateTimeBetween('-55 years', '-19 years');
        $earliestAdmission = (clone $birthDate)->modify('+18 years');

        return [
            'name' => fake()->name(),
            'team_id' => Team::factory(),
            'birth_date' => $birthDate,
            'position' => fake()->jobTitle(),
            'contract_type' => fake()->randomElement(ContractType::cases()),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'admission_date' => fake()->dateTimeBetween($earliestAdmission, 'now'),
            'seniority' => fake()->randomElement(SeniorityLevel::cases()),
        ];
    }
}
