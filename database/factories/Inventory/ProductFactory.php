<?php

namespace Database\Factories\Inventory;

use App\Models\Inventory\ProductCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $price = rand(10, 500);
        $d_price = $price - $this->faker->numberBetween(1, $price);
        return [
            'name' => $this->faker->streetName(),
            'short_name' => $this->faker->name(),
            'code' => $this->faker->unique()->numerify(),
//            'name', 'name_2', 'code', 'warehouse_id', 'origin_number', 'type', 'name_2', 'price', 'd_price', 'sd_price', 'min_price', 'ref_price',
//            'last_cost', 'avg_cost', 'first_cost', 'profit_margin', 'warranty', 'expire_date', 'barcode', 'hs_code', 'batch_number',
            // 'brand_id', 'supplier_id', 'employee_id', 'weight', 'width', 'length', 'height', 'max_limit', 'min_limit',
//            'require_barcode', 'repeat_barcode', 'negative_stock', 'can_be_sold', 'can_be_purchased', 'returnable', 'active', 'category_id',
            's_price' => $price,
            'd_price' => $d_price,
            'sd_price' => $d_price - $this->faker->numberBetween(1, $d_price),
            'min_price' => $d_price - $this->faker->numberBetween(1, $d_price),
            'product_category_id' => ProductCategory::all()->random()->id,
        ];
    }
}
