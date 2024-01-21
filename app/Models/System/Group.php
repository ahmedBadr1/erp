<?php

namespace App\Models\System;

use App\Models\MainModelSoft;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends MainModelSoft
{
    protected $fillable = ['name','active'];

    public function users()
    {
        return $this->belongsToMany(User::class,'group_user');
    }
}
