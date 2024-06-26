<?php

namespace Database\Factories\Accounting;

use App\Models\Accounting\Account;
use App\Models\Accounting\Expense;
use App\Models\Accounting\Ledger;
use App\Models\Accounting\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

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
        $amount = $this->faker->numberBetween(10,1000);
        return [
            'amount' =>$amount,
            'type' => Arr::random($types),
            'note'=> $this->faker->text(),
            'due'=> $this->faker->dateTimeThisYear(),
            'paper_ref' => $this->faker->randomLetter . '-' .$this->faker->randomNumber(7),
            'currency_id' => 1,
            'ex_rate' => 1,
            'currency_total' =>  $amount ,
            'responsible_id' => 1,//User::all()->random()->id,
            'created_by' => 1,
//            'edited_by' => 1,
//            'ledger_id'=> Ledger::all()->random()->id,
            'first_party_id'=> rand(1,100),// Account::all()->random()->id,
            'second_party_id'=> rand(1,100),
        ];
    }
}
