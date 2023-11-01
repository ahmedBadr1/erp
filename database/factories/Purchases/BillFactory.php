<?php

namespace Database\Factories\Purchases;

use App\Models\Accounting\Account;
use App\Models\Hr\Branch;
use App\Models\Inventory\Warehouse;
use App\Models\System\Status;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Purchases\Bill>
 */
class BillFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $statues = Status::where('type','paid')->pluck('id')->toArray();
        $subtotal = $this->faker->numberBetween(10,300) ;
        $discount = $this->faker->numberBetween(1,10) ;
        $date = $this->faker->dateTimeThisYear();
        return [
            'account_id' => Account::all()->random()->id,
            'warehouse_id' => Warehouse::all()->random()->id,
//            'branch_id' => Branch::all()->random()->id(),
            'supplier_id' => Account::all()->random()->id,
            'status_id' => $statues[array_rand($statues)] ,
            'tax_exclusive' => $this->faker->numberBetween(1,3),
            'tax_inclusive' => $this->faker->numberBetween(1.1,3.7),
            'code' =>$this->faker->numerify,
            'number' =>$this->faker->numerify,
            'billed_at' => $date,
            'due_at' => Carbon::parse($date)->addDays(rand(0,14)),
            'responsible_id' => User::all()->random()->id,
            'paid' => rand(0, 1),
            'sub_total' => $subtotal,
            'tax_total' => $subtotal * 0.14,
            'discount' => $discount,
            'total' => $subtotal  - ($subtotal * 0.14) + $discount,
            'notes' => $this->faker->text,
        ];
    }
}
