<?php

namespace App\Models\Inventory;

use App\Models\MainModelSoft;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends MainModelSoft
{
    protected $fillable = ['balance','product_id','warehouse_id'];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }


    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
