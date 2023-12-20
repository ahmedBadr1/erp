<?php

namespace App\Models\System;

use App\Models\MainModelSoft;
use App\Models\Purchases\Bill;
use App\Models\Sales\Invoice;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tax extends MainModelSoft
{

    protected $fillable = ['name','rate','exclusive','active'];
    protected $casts = ['exclusive'=> 'boolean','active'=>'boolean'];

    public function bills()
    {
        return $this->hasMany(Bill::class);
    }
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public static function search($search)
    {
        return empty($search) ? static::query()
            : static::query()->where('name', 'like', '%' . $search . '%')
                ->orWhere('code', 'like', '%' . $search . '%')
                ->orWhere('rate', 'like', '%' . $search . '%');
    }
}
