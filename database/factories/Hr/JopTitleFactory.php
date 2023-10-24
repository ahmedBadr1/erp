<?php

namespace Database\Factories\Hr;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Hr\JopTitle>
 */
class JopTitleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->jobTitle(),
            'salary' => $this->faker->numberBetween(1500,15000),
            'overtime' => $this->faker->numberBetween(10,200),
        ];
    }
}
