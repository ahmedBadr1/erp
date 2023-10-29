<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Purchases\Supplier;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(ConstantSeeder::class);
         \App\Models\Crm\Client::factory(10)->create();
        \App\Models\Crm\Action::factory(10)->create();
        Supplier::factory(10)->create();
    }
}
