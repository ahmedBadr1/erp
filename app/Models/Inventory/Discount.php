<?php

namespace App\Models\Inventory;

use App\Models\MainModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discount extends MainModel
{
    protected $fillable = ['type','amount', 'is_value', 'from', 'to', 'limited','discountable_type','discountable_id'];

    public function discountable()
    {
        return $this->morphTo('discountable');
    }


}
