<?php

namespace App\Models\Inventory;

use App\Models\MainModelSoft;

class UnitCategory extends MainModelSoft
{
    protected $fillable = ['name','description','active'];

    public function units()
    {
        return $this->hasMany(Unit::class,'category_id');
    }

}
