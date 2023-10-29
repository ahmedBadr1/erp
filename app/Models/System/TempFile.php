<?php

namespace App\Models\System;

use App\Models\MainModelSoft;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TempFile extends MainModelSoft
{

    protected $fillable = ['folder','file'];

 public function scopeFolder($query ,$folder)
 {
     return $query->where('folder',$folder);
 }


}
