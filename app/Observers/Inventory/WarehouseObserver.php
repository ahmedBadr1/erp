<?php

namespace App\Observers\Inventory;

use App\Models\Inventory\Warehouse;
use App\Services\Inventory\WarehouseService;
use Illuminate\Support\Facades\Log;

class WarehouseObserver
{
    /**
     * Handle the Warehouse "created" event.
     */
    public function created(Warehouse $warehouse): void
    {
        (new WarehouseService())->check($warehouse);
    }

    /**
     * Handle the Warehouse "updated" event.
     */
    public function updated(Warehouse $warehouse): void
    {
        (new WarehouseService())->check($warehouse);
    }

    /**
     * Handle the Warehouse "deleted" event.
     */
    public function deleted(Warehouse $warehouse): void
    {
        //
    }

    /**
     * Handle the Warehouse "restored" event.
     */
    public function restored(Warehouse $warehouse): void
    {
        //
    }

    /**
     * Handle the Warehouse "force deleted" event.
     */
    public function forceDeleted(Warehouse $warehouse): void
    {
        //
    }

    private function check(Warehouse $warehouse)
    {
        if ($warehouse->active){
            if (empty($warehouse->account_id) || empty($warehouse->cog_account_id) || empty($warehouse->s_account_id)) {
                $warehouse->updateQuietly(['active' => false]);
            }
        }else if (isset($warehouse->account_id, $warehouse->cog_account_id, $warehouse->s_account_id)) {
            $warehouse->updateQuietly(['active' => true]);
        }
    }
}
