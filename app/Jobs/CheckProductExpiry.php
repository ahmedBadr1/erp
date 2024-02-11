<?php

namespace App\Jobs;

use App\Models\Inventory\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CheckProductExpiry implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $expiredProducts = Product::where('expiry_date', '<', now())->get();

        foreach ($expiredProducts as $product) {
            // Perform actions for expired products
            // For example, you can send notifications, update the status, etc.
        }
    }
}
