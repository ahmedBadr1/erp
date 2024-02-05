<?php

namespace App\Console\Commands;

use App\Models\Accounting\Account;
use App\Models\Accounting\Entry;
use App\Models\Accounting\Ledger;
use App\Models\Accounting\Transaction;
use App\Models\Inventory\Warehouse;
use App\Models\Purchases\Supplier;
use App\Models\Sales\Client;
use Database\Seeders\AccountingSeeder;
use Database\Seeders\PurchasesSeeder;
use Database\Seeders\SalesSeeder;
use Illuminate\Console\Command;
use Illuminate\Process\Pool;
use Illuminate\Support\Facades\Process;

class Seed extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:seed {--processes=0}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'hyper Seed Data';

    protected $aliases = [
        'seed:data'
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $treasuries = Account::with('costCenter')->whereHas('type',fn($q)=>$q->where('code','TR'))->get();

        $warehouses =  Warehouse::with('account')->get();
        $clients = Client::with('account')->get();
        $suppliers = Supplier::with('account')->get();

        $processes = (int)$this->option('processes');
        if ($processes) {
            return $this->spawn($processes);
        }
        for ($i = 0; $i < 1000; $i++) {
            if ($i % 100 === 0) {
                echo ".";
            }
//            try {
            $this->insert($i ,$treasuries->random(),$warehouses->random(),$suppliers->random(),$clients->random());
//            } catch (\Throwable $e){
//                // nothing
//            }

        }
        echo "\nDone Seeding ;) \n";
    }

    protected function insert($i,$treasury , $warehouse,$supplier,$client)
    {
        AccountingSeeder::seedType('CI',null,$treasury,$client->account);
        AccountingSeeder::seedType('CO',null,$treasury,$supplier->account);
        PurchasesSeeder::seedType(type: 'PO', warehouse: $warehouse,supplier: $supplier,treasury:  $treasury);
        if($i > 100){
            SalesSeeder::seedType(type: 'SO', warehouse: $warehouse,client: $client,treasury:  $treasury);
        }

    }

    public function spawn($processes)
    {
        Process::pool(function (Pool $pool) use ($processes) {
            for ($i = 0; $i < $processes; $i++) {
                $pool->command('php artisan app:seed')->timeout(60 * 5);
            }
        })->start()->wait();
    }
}
