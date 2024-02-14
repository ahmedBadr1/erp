<?php

namespace App\Models\Sales;

use App\Models\MainModelSoft;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PriceList extends MainModelSoft
{
    protected $fillable = ['name','type'];

    public function items()
    {
        return $this->hasMany(PriceListItem::class);
    }
}
