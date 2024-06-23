<?php

namespace Database\Seeders;

use App\Enums\OtherPartyType;
use App\Models\Inventory\OtherParty;
use App\Models\Inventory\Product;
use App\Models\Inventory\ProductCategory;
use App\Models\Inventory\Unit;
use App\Models\Inventory\UnitCategory;
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
            'name' => 'مخزن 1'
        ]); // ACCOUNT WILL not CREATE IT

        ProductCategory::factory()->create([
            'name' => 'main'
        ]);

        $this->seedUnits();

        $this->seedOtherParties();

        for ($i = 0; $i < 10; $i++) {
            Product::factory(1)->create();
        }

    }

    /**
     * @return void
     */
    public function seedUnits(): void
    {
        $unitCategories = [];

        $unitCategories['Unit'] = [
            [
                'name' => 'Unit',
                'code' => 'unit',

                'type' => 1,
                'ratio' => 1,
            ],
            [
                'name' => 'Dozen',
                'code' => 'dozen',
                'type' => 3,
                'ratio' => 12.0000,
            ],
        ];

        $unitCategories['Weight'] = [
            [
                'name' => 'milligram',
                'code' => 'mg',
                'type' => 2,
                'ratio' => 1000000,
            ],
            [
                'name' => 'gram',
                'code' => 'g',
                'type' => 2,
                'ratio' => 1000,
            ],
            [
                'name' => 'ounce',
                'code' => 'o',
                'type' => 2,
                'ratio' => 35.274,
            ],

            [
                'name' => 'pound',
                'code' => 'lb',
                'type' => 2,
                'ratio' => 2.20462
            ],
            [
                'name' => 'kilogram',
                'code' => 'kg',
                'type' => 1,
                'ratio' => 1,
            ],

            [
                'name' => 'tonne',
                'code' => 't',
                'type' => 3,
                'ratio' => 1000,
            ],
        ];

        $unitCategories['Working Hours'] = [

            [
                'name' => 'Hour',
                'code' => 'hour',
                'type' => 1,
                'ratio' => 1,
            ],
            [
                'name' => 'Day',
                'code' => 'working day',
                'type' => 3,
                'ratio' => 8.0000,
            ],

        ];

        $unitCategories['Time'] = [

            [
                'name' => 'Second',
                'code' => 's',
                'type' => 2,
                'ratio' => 3600,
            ],
            [
                'name' => 'Minute',
                'code' => 'm',
                'type' => 2,
                'ratio' => 60,
            ],
            [
                'name' => 'Hour',
                'code' => 'hour',
                'type' => 1,
                'ratio' => 1,
            ],
            [
                'name' => 'Day',
                'code' => 'day',
                'type' => 3,
                'ratio' => 24,
            ],
            [
                'name' => 'Month',
                'code' => 'month',
                'type' => 3,
                'ratio' => 720,
            ],
            [
                'name' => 'Year',
                'code' => 'year',
                'type' => 3,
                'ratio' => 8640,
            ],

        ];

        $unitCategories['Distance'] = [

            [
                'name' => 'millimeter',
                'code' => 'mm',
                'type' => 2,
                'ratio' => 1000,
            ],
            [
                'name' => 'centimeter',
                'code' => 'cm',
                'type' => 2,
                'ratio' => 100,
            ],
            [
                'name' => 'Inch',
                'code' => 'in',
                'type' => 2,
                'ratio' => 39.3701,
            ],
            [
                'name' => 'Foot',
                'code' => 'ft',
                'type' => 2,
                'ratio' => 3.28084,
            ],
            [
                'name' => 'Yard',
                'code' => 'yd',
                'type' => 2,
                'ratio' => 1.09361,
            ],
            [
                'name' => 'Meter',
                'code' => 'm',
                'type' => 1,
                'ratio' => 1,
            ],
            [
                'name' => 'kilometer',
                'code' => 'km',
                'type' => 3,
                'ratio' => 1000,
            ],
            [
                'name' => 'mile',
                'code' => 'mi',
                'type' => 3,
                'ratio' => 1609.34,
            ],

        ];

        $unitCategories['Surface'] = [

            [
                'name' => 'foot square',
                'code' => 'ft²',
                'type' => 2,
                'ratio' => 10.76391,
            ],
            [
                'name' => 'meter square',
                'code' => 'm²',
                'type' => 1,
                'ratio' => 1,
            ],

        ];

        $unitCategories['Volume'] = [
            [
                'name' => 'inch',
                'code' => 'in³',
                'type' => 2,
                'ratio' => 61.02370,
            ],
            [
                'name' => 'Liter',
                'code' => 'L',
                'type' => 1,
                'ratio' => 1,
            ],
            [
                'name' => 'Gallon',
                'code' => 'gal',
                'type' => 3,
                'ratio' => 3.78541,
            ],
            [
                'name' => 'meter',
                'code' => 'm³',
                'type' => 3,
                'ratio' => 1000,
            ],

        ];


        foreach ($unitCategories as $name => $units) {
            $cat = UnitCategory::create(['name' => $name]);
            foreach ($units as $unit) {
                Unit::create([...$unit, 'category_id' => $cat->id]);
            }
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
                'type' => OtherPartyType::IN
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
