<?php

namespace App\Models\System;

use App\Models\Employee\Employee;
use App\Models\MainModelSoft;

class City extends MainModelSoft
{
protected $fillable = ['name','state_id','state_code','country_id','country_code',];


    public function state (){
        return $this->belongsTo(State::class);
    }

    public function country (){
        return $this->belongsTo(Country::class);
    }

    public function employees (){
        return $this->hasMany(Employee::class);
    }
}
