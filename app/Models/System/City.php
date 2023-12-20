<?php

namespace App\Models\System;

use App\Models\Employee\Employee;
use App\Models\MainModel;

class City extends MainModel
{
protected $fillable = ['name','state_id','state_code','country_id','country_code',];


    public function state (){
        return $this->belongsTo(State::class);
    }

    public function country (){
        return $this->belongsTo(Country::class);
    }

    public static function search($search)
    {
        return empty($search) ? static::query()
            : static::query()->where('id', 'like', '%'.$search.'%')
                ->orWhere('name', 'like', '%'.$search.'%')
                ->orWhere('country_code', 'like', '%'.$search.'%')
                ->orWhere('state_code', 'like', '%'.$search.'%')
                ->orWhereHas('country', fn($q) => $q->where('name','like', '%'.$search.'%'))
                ->orWhereHas('state', fn($q) => $q->where('name','like', '%'.$search.'%'));
    }
}
