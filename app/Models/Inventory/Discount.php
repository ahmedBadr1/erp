<?php

namespace App\Models\Inventory;

use App\Models\MainModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discount extends MainModel
{
    protected $fillable = ['type','value', 'percentage', 'from', 'to', 'limited','discountable'];

    public function discountable()
    {
        return $this->morphTo('discountable');
    }


}
