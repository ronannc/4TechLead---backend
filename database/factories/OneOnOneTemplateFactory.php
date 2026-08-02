<?php

namespace Database\Factories;

use App\Models\OneOnOneTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OneOnOneTemplate>
 */
class OneOnOneTemplateFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'questions' => [
                'Como você está se sentindo neste ciclo?',
                'Qual foi o principal aprendizado desde o último 1:1?',
                'Qual apoio você precisa do tech lead?',
            ],
            'is_default' => false,
            'active' => true,
        ];
    }
}
