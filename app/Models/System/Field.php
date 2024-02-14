<?php

namespace App\Models\System;

use App\Models\MainModelSoft;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Field extends MainModelSoft
{
    protected $fillable = ['label','type','active'];
}
