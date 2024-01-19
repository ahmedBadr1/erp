<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Purchases\Supplier;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat\Wizard\Accounting;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $this->call(ConstantSeeder::class);
        Artisan::call('passport:install');
        $this->call(AccountingSeeder::class);
        $this->call(InventorySeeder::class);
        $this->call(PurchasesSeeder::class);
        $this->call(SalesSeeder::class);



    }
}
