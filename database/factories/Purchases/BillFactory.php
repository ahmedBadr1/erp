<?php

namespace Database\Factories\Purchases;

use App\Models\Accounting\Account;
use App\Models\Hr\Branch;
use App\Models\Inventory\Warehouse;
use App\Models\System\Status;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

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


        $grossTotal = $this->faker->numberBetween(10,1000) ;
        $discounts = [1,5,10,15,20,25,30];
        $discount =  $this->faker->boolean(80) ? ($grossTotal / Arr::random($discounts)) : 0 ;
        $subtotal = $grossTotal - $discount ;
        $taxes = [0.01,0.1,0.14];
        $tax =  $this->faker->boolean(100) ? ($subtotal *  Arr::random($taxes))  : 0;
        $date = $this->faker->dateTimeThisYear();
        return [
//            'account_id' => Account::all()->random()->id,
//            'warehouse_id' => Warehouse::all()->random()->id,
//            'branch_id' => Branch::all()->random()->id(),
//            'supplier_id' => Account::all()->random()->id,
            'status_id' => $statues[array_rand($statues)] ,
            'tax_exclusive' => $this->faker->numberBetween(1,3),
            'tax_inclusive' => $this->faker->numberBetween(1.1,3.7),
//            'code' =>$this->faker->numerify,
            'paper_ref' =>$this->faker->numerify,
            'date' => $date,
            'deliver_at' => Carbon::parse($date)->addDays(rand(0,14)),
            'responsible_id' => User::all()->random()->id,
            'paid' => rand(0, 1),
            'gross_total' => $grossTotal,
            'discount' => $discount,
            'sub_total' => $subtotal,
            'tax_total' => $tax,
            'total' => $subtotal - $tax,
            'note' => $this->faker->sentence,
            'cost_allocation' => 0,
        ];
    }
}
