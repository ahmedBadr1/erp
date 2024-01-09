<?php

namespace App\Models\System;

use App\Models\MainModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Access extends MainModel
{
  protected $fillable = ['user_id','model_id','model_type'];

  public function user()
  {
      return $this->belongsTo(User::class);
  }


}
