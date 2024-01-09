<?php

namespace Database\Seeders;

use App\Models\Inventory\Item;
use App\Models\Inventory\Product;
use App\Models\Inventory\Unit;
use App\Models\Inventory\Warehouse;
use App\Models\Purchases\Bill;
use App\Models\Purchases\Supplier;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InventorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Warehouse::factory()->create([
            'name' => 'main',
            'type' => 'materials',
            'address' => 'factory',
        ]);


        $this->seedUnits();
        Supplier::factory(3)->create();
        Product::factory(100)->create();




        Warehouse::factory(2)->create();
        Bill::factory(10)->create()->each(function ($bill){
            $bill->items()->saveMany(Item::factory(rand(2,5))->create());
        });
    }

    /**
     * @return void
     */
    public function seedUnits(): void
    {
        $weightUnits = [
            'milligram' => 0.000001,
            'gram' => 0.001,
            'kilogram' => 1,
            'ounce' => 0.0283495,
            'pound' => 0.453592,
            'stone' => 6.35029,
            'tonne' => 1000,
        ];
        foreach ($weightUnits as $key => $unit) {
            Unit::factory()->create([
                'name' => $key,
                'conversion_factor' => $unit,
                'group' => 'weight'
            ]);
        }
        $distanceUnits = [
            'millimeter' => 0.001,
            'centimeter' => 0.01,
            'meter' => 1,
            'inch' => 0.0254,
            'feet' => 0.3048,
            'yard' => 0.9144,
            'mile' => 1609.34,
        ];
        foreach ($distanceUnits as $key => $unit) {
            Unit::factory()->create([
                'name' => $key,
                'conversion_factor' => $unit,
                'group' => 'length'
            ]);
        }
        $liquidUnits = [
            'milliliter' => 0.001,
            'centiliter' => 0.01,
            'liter' => 1,
            'fluid ounce' => 0.0295735,
            'pint' => 0.473176,
            'quart' => 0.946353,
            'gallon' => 3.78541,
        ];
        foreach ($liquidUnits as $key => $unit) {
            Unit::factory()->create([
                'name' => $key,
                'conversion_factor' => $unit,
                'group' => 'liquid'
            ]);
        }
        $packingUnits = [
            'piece' => 1,
            'pack' => 6,
            'dozen' => 12,
            'case' => 24,
        ];
        foreach ($packingUnits as $key => $unit) {
            Unit::factory()->create([
                'name' => $key,
                'conversion_factor' => $unit,
                'group' => 'packing'
            ]);
        }

        $timeUnits = [
            'second' => 1,
            'minute' => 60,
            'hour' => 3600,
            'day' => 86400,
            'week' => 604800,
            'month' => 2592000,
            'year' => 31536000,
        ];
        foreach ($timeUnits as $key => $unit) {
            Unit::factory()->create([
                'name' => $key,
                'conversion_factor' => $unit,
                'group' => 'time'
            ]);
        }

        $volumeUnits = [
            'cubic_millimeter' => 1,
            'cubic_centimeter' => 1000,
            'milliliter' => 1,
            'liter' => 1000,
            'cubic_meter' => 1000000,
            'gallon' => 3785.411784,
            'quart' => 946.352946,
            'pint' => 473.176473,
            'fluid_ounce' => 29.5735296,
            'tablespoon' => 14.7867648,
            'teaspoon' => 4.9289216,
        ];
        foreach ($volumeUnits as $key => $unit) {
            Unit::factory()->create([
                'name' => $key,
                'conversion_factor' => $unit,
                'group' => 'volume'
            ]);
        }
    }
}