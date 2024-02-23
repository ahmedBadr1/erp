<?php

namespace App\Observers\Inventory;

use App\Models\Inventory\OtherParty;
use App\Services\Inventory\OtherPartyService;

class OtherPartyObserver
{
    /**
     * Handle the OtherParty "created" event.
     */
    public function created(OtherParty $otherParty): void
    {
        (new OtherPartyService())->check($otherParty);
    }

    /**
     * Handle the OtherParty "updated" event.
     */
    public function updated(OtherParty $otherParty): void
    {
        (new OtherPartyService())->check($otherParty);
    }

    /**
     * Handle the OtherParty "deleted" event.
     */
    public function deleted(OtherParty $otherParty): void
    {
        //
    }

    /**
     * Handle the OtherParty "restored" event.
     */
    public function restored(OtherParty $otherParty): void
    {
        //
    }

    /**
     * Handle the OtherParty "force deleted" event.
     */
    public function forceDeleted(OtherParty $otherParty): void
    {
        //
    }
}
