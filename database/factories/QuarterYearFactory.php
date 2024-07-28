<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\QuarterYear>
 */
class QuarterYearFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    public function definition(): array
    {
        return [
            'created_by' => fake()->name(),
            'year' => fake()->unique(),
            'start_tw_1' => fake()->name(),
            'end_tw_1' => fake()->name(),
            'start_tw_2' => fake()->name(),
            'end_tw_2' => fake()->name(),
            'start_tw_3' => fake()->name(),
            'end_tw_3' => fake()->name(),
            'start_tw_4' => fake()->name(),
            'end_tw_4' => fake()->name(),
        ];
    }
}
