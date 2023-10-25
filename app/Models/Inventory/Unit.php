<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'type', 'group', 'conversion_factor',];

    public static array $GROUPS= ['weight', 'length', 'liquid', 'packing', 'time', 'volume'];


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