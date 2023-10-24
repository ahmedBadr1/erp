<?php

namespace Database\Factories\System;

use Illuminate\Database\Eloquent\Factories\Factory;

class TaxFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'code' => $this->faker->languageCode(),
            'rate' => $this->faker->numberBetween(0,100),
        ];
    }
}
