<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class SalesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
//        \App\Models\Sales\Client::factory(3)->create(); // ACCOUNT WILL CREATE IT
        \App\Models\Crm\Action::factory(10)->create();
    }
}
