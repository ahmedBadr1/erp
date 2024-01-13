<?php

namespace App\Models\Accounting;

use App\Models\MainModelSoft;
use App\Models\Purchases\Bill;
use App\Models\Sales\Invoice;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Tax extends MainModelSoft
{

    protected $fillable = ['name','code','rate','scope','account_id','exclusive','active'];
    protected $casts = ['exclusive'=> 'boolean','active'=>'boolean'];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

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

    public function scope() :Attribute
    {
        return Attribute::make(
            get: fn ($value) => json_decode($value),
            set: fn ($value) => json_encode($value),
        );
    }
}
