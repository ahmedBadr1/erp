<?php

namespace App\Models\Sales;

use App\Models\MainModelSoft;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PriceListItem extends MainModelSoft
{
    protected $fillable = ['price_list_id','product_id','price'];
}
