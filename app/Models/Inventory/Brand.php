<?php

namespace App\Models\Inventory;

use App\Models\MainModelSoft;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends MainModelSoft
{

    protected $fillable = ['name' ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
