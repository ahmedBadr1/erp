<?php

namespace Database\Factories\Accounting;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Accounting\Ledger>
 */
class LedgerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'amount' => $this->faker->numberBetween(10,1000),
            'description'=> $this->faker->text(),
            'due'=> $this->faker->dateTimeThisYear(),
            'responsible_id' => 1,//User::all()->random()->id,
            'created_by' => 1,
//            'edited_by' => 1,
        ];
    }
}
