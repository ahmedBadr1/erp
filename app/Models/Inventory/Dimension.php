<?php

namespace App\Models\Inventory;

use App\Models\MainModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dimension extends MainModel
{
    protected $fillable = ['weight', 'width', 'length', 'height', 'measurable'];

    public function measurable()
    {
        return $this->morphTo('measurable');
    }
}
