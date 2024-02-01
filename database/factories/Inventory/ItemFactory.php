<?php

namespace Database\Factories\Inventory;

use App\Models\Inventory\Item;
use App\Models\Inventory\Product;
use App\Models\Inventory\Unit;
use App\Models\Inventory\Warehouse;
use App\Models\Purchases\Bill;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Inventory\Item>
 */
class ItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        return [

            'quantity' => $this->faker->numberBetween(1,200),
            'comment' => $this->faker->text(),
            'price' => $this->faker->numberBetween(5,300),
//            'tax_exclusive' => $this->faker->numberBetween(1,3),
//            'tax_inclusive' => $this->faker->numberBetween(1.1,3.7),
            'unit_id' => Unit::all()->random()->id,
//            'cost' => $this->faker->numberBetween(5,200),
//            'bill_id' => Bill::all()->random()->id,
//            'product_id' => Product::all()->random()->id,
//            'warehouse_id' => Warehouse::all()->random()->id,
            'user_id' => User::all()->random()->id,
            'expire_at' => $this->faker->dateTimeThisYear(now()->addYear()),
        ];
    }
}
