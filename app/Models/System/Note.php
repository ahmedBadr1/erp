<?php

namespace App\Models\System;

use App\Models\MainModelSoft;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Note extends MainModelSoft
{
  protected $fillable = ['content', 'notable_id','notable_type'];

  public function noteable()
  {
      return $this->morphTo();
  }
}
