<?php

namespace App\Models\System;

use App\Models\MainModelSoft;

class Contact extends MainModelSoft
{
    protected $fillable = ['name', 'email', 'phone',  'telephone', 'contactable_id', 'contactable_type', 'status_id'];
}
