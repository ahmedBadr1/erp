<?php

namespace App\Models\System;

use App\Models\Client;
use App\Models\MainModelSoft;

class Status extends MainModelSoft
{
    protected $fillable = ['name','type','color'];

    public function client(){
        return $this->hasMany(Client::class);
    }

}
