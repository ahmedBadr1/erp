<?php

namespace Database\Factories\Accounting;

use App\Models\Accounting\Expense;
use App\Models\Accounting\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Accounting\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $types = Transaction::$TYPES;
        return [
            'amount' => $this->faker->numberBetween(10,1000),
            'type' => $types[array_rand($types)],
            'description'=> $this->faker->text(),
            'due'=> $this->faker->dateTimeThisYear(),
            'user_id' => User::all()->random()->id
        ];
    }
}
