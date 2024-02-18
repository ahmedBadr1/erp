<?php

namespace Database\Factories\Inventory;

use App\Models\Accounting\Tax;
use App\Models\Inventory\Brand;
use App\Models\Inventory\ProductCategory;
use App\Models\Inventory\Unit;
use App\Models\Inventory\Warehouse;
use App\Models\Inventory\WarehouseShelf;
use App\Models\Purchases\Supplier;
use App\Models\User;
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
            'name' => $this->faker->name(),
            'short_name' => $this->faker->firstName(),
            'part_number' => $this->faker->unique()->randomNumber(6),
            'origin_number' => $this->faker->numerify(),
            'batch_number' => $this->faker->numerify(),
            'barcode' => $this->faker->numerify(),
            'hs_code' => $this->faker->numerify(),

//            'name', 'name_2', 'code', 'warehouse_id', 'origin_number', 'type', 'name_2', 'price', 'd_price', 'sd_price', 'min_price', 'ref_price',
//            'last_cost', 'avg_cost', 'first_cost', 'profit_margin', 'warranty', 'expire_date', 'barcode', 'hs_code', 'batch_number',
            // 'brand_id', 'supplier_id', 'employee_id', 'weight', 'width', 'length', 'height', 'max_limit', 'min_limit',
//            'require_barcode', 'repeat_barcode', 'negative_stock', 'can_be_sold', 'can_be_purchased', 'returnable', 'active', 'category_id',
            's_price' => $price,
            'd_price' => $d_price,
            'sd_price' => $d_price - $this->faker->numberBetween(1, $d_price),
            'min_price' => $d_price - $this->faker->numberBetween(1, $d_price),
            'product_category_id' => ProductCategory::all()->random()->id,
            'warehouse_id' => Warehouse::all()->random()?->id,
//            'warehouse_shelf_id' => WarehouseShelf::all()?->random()?->id,
            'unit_id' => Unit::all()?->random()?->id,
            'tax_id' => Tax::all()?->random()?->id ,
//            'withholding_tax_id' => Tax::all()?->random()?->id ,

//            'brand_id' => Brand::all()?->random()?->id ,
            'supplier_id' => Supplier::all()->random()?->id,
            'user_id' => User::all()->random()?->id,

        ];
    }
}
