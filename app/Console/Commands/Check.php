<?php

namespace App\Console\Commands;

use App\Mail\CheckMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class Check extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
                $email = new CheckMail();
        Mail::to('ahmedbauomy30@gmail.com')->send($email);

//        Log::notice('done');
    }
}
