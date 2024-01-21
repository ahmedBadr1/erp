<?php

namespace App\Models\System;

use App\Models\MainModelSoft;
use App\Models\User;

class Contact extends MainModelSoft
{
    protected $fillable = ['name','phone1','phone2', 'whatsapp', 'mobile',  'fax','email', 'contactable_id', 'contactable_type', 'status_id','user_id'];

 public function owner()
 {
     return $this->morphTo('contactable');
 }

    public function creator()
    {
        return $this->belongsTo(User::class);
    }
}

