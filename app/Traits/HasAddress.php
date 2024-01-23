<?php

namespace App\Traits;

use App\Models\System\Address;

trait HasAddress
{
    public function addresses()
    {
        return $this->morphMany(Address::class,'addressable');
    }

    public function lastAddresses()
    {
        return $this->morphMany(Address::class,'addressable')->latest();
    }

    public function lastAddress()
    {
        return $this->morphOne(Address::class,'addressable')->latestOfMany();
    }

    public function motherAddress()
    {
        return $this->morphOne(Address::class,'addressable');
    }
}
