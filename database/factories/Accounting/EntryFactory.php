<?php

namespace Database\Factories\Accounting;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Accounting\Entry>
 */
class EntryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'debit' => $this->faker->randomNumber(),
            'credit' => $this->faker->randomNumber(),
            'description'=>$this->faker->realText(),
            'account_id'=> rand(1,100)
        ];
    }
}
