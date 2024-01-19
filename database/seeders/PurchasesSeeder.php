<?php

namespace Database\Seeders;

use App\Models\Inventory\Item;
use App\Models\Purchases\Bill;
use App\Models\Purchases\Supplier;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PurchasesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Supplier::factory(3)->create();
        Bill::factory(10)->create()->each(function ($bill){
            $bill->items()->saveMany(Item::factory(rand(2,5))->create());
        });
    }
}
