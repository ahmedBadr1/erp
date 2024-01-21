<?php

namespace App\Models\System;

use App\Models\MainModelSoft;
use App\Models\User;


class Address extends MainModelSoft
{
    protected $fillable = [
        'city_id',
        'district',
        'street',
        'building',
        'floor',
        'apartment',
        'landmarks',
        'longitude',
        'latitude',
        'addressable_type',
        'addressable_id',
        'user_id'
    ];

    public function addressable()
    {
        return $this->morphTo('addressable');
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
