<?php

namespace Database\Factories\Inventory;

use App\Models\Inventory\ItemHistory;
use App\Models\Inventory\Product;
use App\Models\Inventory\Unit;
use App\Models\Inventory\Warehouse;
use App\Models\Purchases\Bill;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Inventory\ItemHistory>
 */
class ItemHistoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $price = $this->faker->numberBetween(5,100);
        $quantity =  $this->faker->numberBetween(1,10);

        return [
            'quantity' =>$quantity,
            'price' => $price,

            'unit_id' => Unit::all()->random()->id,
            'product_id' => Product::all()->random()->id,
            'warehouse_id' => Warehouse::all()->random()->id,
//            'expire_at' => $this->faker->dateTimeThisYear(now()->addYear()),

        ];
    }
}
