<?php

namespace App\Models\Inventory;

use App\Models\MainModelSoft;

class UnitGroup extends MainModelSoft
{
    protected $fillable = ['name','description','active'];

    public function units()
    {
        return $this->hasMany(Unit::class);
    }

}
