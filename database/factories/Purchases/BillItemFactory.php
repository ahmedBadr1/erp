<?php

namespace Database\Factories\Purchases;

use App\Models\Accounting\Tax;
use App\Models\Inventory\Product;
use App\Models\Inventory\Unit;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Inventory\Item>
 */
class BillItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $price = $this->faker->numberBetween(5,100);
        $quantity =  $this->faker->numberBetween(1,5);

        return [
            'quantity' =>$quantity,
            'comment' => $this->faker->sentence(),
            'price' => $price,
            'cost' => $price -  $this->faker->numberBetween(1,$price),
            'tax_value' => $quantity * $price * 0.14 ,
            'sub_total' => $quantity * $price,
            'total' => $quantity * $price * 1.14,
            'tax_id' => 1,
            'product_id' => Product::all()->random()->id,
            'expire_at' => $this->faker->dateTimeThisYear(now()->addYear()),
        ];
    }

    public function invoice($warehouseId,$quantity = 10): Factory
    {
        $productId = Product::whereHas('stocks',fn($q)=>$q->where('warehouse_id',$warehouseId)->where('balance','>=',$quantity))->get()->random()->id;
        if (!$productId){
            return  false ;
        }
        return $this->state(function (array $attributes) use($productId) {
            return [
                'product_id' =>  $productId
            ];
        });
    }
}
