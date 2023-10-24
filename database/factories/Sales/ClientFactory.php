<?php

namespace Database\Factories\Sales;

use Illuminate\Database\Eloquent\Factories\Factory;

class ClientFactory extends Factory
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
            'code' => $this->faker->countryCode(),
            'phone' => $this->faker->e164PhoneNumber(),
            'status_id' => 1,

        ];
    }
}
