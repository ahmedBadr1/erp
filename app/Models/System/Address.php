<?php

namespace App\Models\System;

use App\Models\MainModelSoft;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Znck\Eloquent\Traits\BelongsToThrough;

class Address extends MainModelSoft
{
    use HasFactory , BelongsToThrough ;
    protected $fillable = [
        'name',
        'area_id',
        'street',
        'building',
        'floor',
        'apartment',
        'landmarks',
        'longitude',
        'latitude',
        'addressable_type',
        'addressable_id',
        'city_id',
        'address'

    ];

    public function addressable()
    {
        return $this->morphTo();
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

}
