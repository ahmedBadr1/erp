<?php

namespace Database\Factories\Crm;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Crm\Client>
 */
class ClientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'code' => $this->faker->countryCode() .  $this->faker->randomNumber(5),
            'email' => $this->faker->email(),
            'phone' => $this->faker->e164PhoneNumber(),
            'status_id' => 1,
            'address' => $this->faker->address(),
        ];
    }
}
