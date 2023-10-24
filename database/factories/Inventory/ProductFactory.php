<?php

namespace Database\Factories\Inventory;

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
        return [
            'name' => $this->faker->name(),
            'name_2' => $this->faker->name(),
            'code' => $this->faker->name(),
//            'name', 'name_2', 'code', 'warehouse_id', 'origin_number', 'type', 'name_2', 'price', 'd_price', 'sd_price', 'min_price', 'ref_price',
//            'last_cost', 'avg_cost', 'first_cost', 'profit_margin', 'warranty', 'expire_date', 'barcode', 'hs_code', 'batch_number',
//            'tax1_id', 'tax2_id', 'unit_id', 'brand_id', 'vendor_id', 'employee_id', 'weight', 'width', 'length', 'height', 'max_limit', 'min_limit',
//            'require_barcode', 'repeat_barcode', 'negative_stock', 'can_be_sold', 'can_be_purchased', 'returnable', 'active', 'category_id',
            'price' => rand(10,50),
        ];
    }
}
