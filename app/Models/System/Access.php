<?php

namespace App\Models\System;

use App\Models\MainModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Access extends MainModel
{
  protected $fillable = ['auth_id','auth_type','model_id','model_type'];

    public function subject()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->morphTo('user');
    }

    public function group()
    {
        return $this->morphTo('group');
    }

//    public function model()
//    {
//        return $this->morphTo('model');
//    }
//
//    public function auth()
//    {
//        return $this->morphTo('auth');
//    }


}
