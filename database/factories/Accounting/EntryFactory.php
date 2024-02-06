<?php

namespace Database\Factories\Accounting;

use App\Models\Accounting\Account;
use App\Models\Accounting\Ledger;
use App\Models\Accounting\Transaction;
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
//            'amount' => $this->faker->numberBetween(100,200000),
            'credit' => $this->faker->boolean(30),
//            'description'=>$this->faker->realText(),
            'account_id'=> rand(1,100),// Account::all()->random()->id,
            'cost_center_id'=> rand(1,11),
//            'ledger_id'=> Ledger::all()->random()->id,
        'created_at' => $this->faker->dateTimeThisYear(),
        ];
    }
}
