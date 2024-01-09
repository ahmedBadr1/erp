<?php

namespace App\Console\Commands;

use App\Models\Accounting\Entry;
use App\Models\Accounting\Ledger;
use App\Models\Accounting\Transaction;
use Database\Seeders\AccountingSeeder;
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
        $processes = (int)$this->option('processes');
        if ($processes) {
            return $this->spawn($processes);
        }
        for ($i = 0; $i < 1000; $i++) {
            if ($i % 100 === 0) {
                echo ".";
            }
//            try {
            $this->insert();
//            } catch (\Throwable $e){
//                // nothing
//            }

        }
        echo 'Done Seeding ;)';
    }

    protected function insert()
    {
        AccountingSeeder::seed();
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
