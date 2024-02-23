<?php

namespace Database\Seeders;

use App\Enums\OtherPartyType;
use App\Models\Inventory\OtherParty;
use App\Models\Inventory\Product;
use App\Models\Inventory\ProductCategory;
use App\Models\Inventory\Unit;
use App\Models\Inventory\Warehouse;
use Illuminate\Database\Seeder;

class InventorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        Warehouse::factory()->create([
            'name'=> 'مخزن 1'
        ]); // ACCOUNT WILL not CREATE IT

        ProductCategory::factory()->create([
            'name' => 'main'
        ]);

        $this->seedUnits();

        $this->seedOtherParties();

        Product::factory(1)->create();

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
                'type' => 'weight'
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
                'type' => 'length'
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
                'type' => 'liquid'
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
                'type' => 'packing'
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
                'type' => 'time'
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
                'type' => 'volume'
            ]);
        }
    }

    public function seedOtherParties()
    {
        $inOthers = [
            'إستبدالات',
            'تسوية زيادة الجرد',
        ];

        $outOthers = [
            'أصول ثابتة',
            'تسوية عجز الجرد',
        ];

        foreach ($inOthers as $inOther) {
            OtherParty::create([
                'name' => $inOther,
                'type' =>  OtherPartyType::IN
            ]);
        }

        foreach ($outOthers as $inOther) {
            OtherParty::create([
                'name' => $inOther,
                'type' => OtherPartyType::OUT
            ]);
        }
    }
}
