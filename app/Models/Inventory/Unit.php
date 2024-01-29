<?php

namespace App\Models\Inventory;

use App\Models\MainModel;

class Unit extends MainModel
{

    protected $fillable = ['name', 'type', 'group', 'conversion_factor'];

    public static array $GROUPS= ['weight', 'length', 'liquid', 'packing', 'time', 'volume'];

    public function group()
    {
        return $this->belongsTo(UnitGroup::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
    public function greater($value)
    {
        return $value * $this->conversion_factor;
    }

    public function smalller($value)
    {
        return $value / $this->conversion_factor;
    }
}
