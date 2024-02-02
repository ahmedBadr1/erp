<?php

namespace Database\Factories\Inventory;

use App\Models\Accounting\Transaction;
use App\Models\Inventory\Warehouse;
use App\Models\Purchases\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Inventory\InvTransaction>
 */
class InvTransactionFactory extends Factory
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
            'accepted_at'=> $this->faker->boolean(30) ? $this->faker->dateTimeThisYear() : null,

            'paper_ref' => $this->faker->randomLetter . '-' .$this->faker->randomNumber(7),
            'responsible_id' => 1,//User::all()->random()->id,
            'created_by' => 1,
//            'edited_by' => 1,
//            'supplier_id'=> Supplier::all()->random()->id,
            'from_id'=>  Warehouse::all()->random()->id,
//            'to_id'=>  Warehouse::all()->random()->id,
        ];
    }
}
