<?php

namespace App\Models\System;

use App\Models\MainModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bookmark extends MainModel
{
  protected $fillable = ['link','title','user_id'];

  public function user()
  {
      return $this->belongsTo(User::class);
  }
}
