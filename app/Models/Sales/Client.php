<?php

namespace App\Models\Sales;

use App\Models\Accounting\Account;
use App\Models\MainModelSoft;
use App\Models\System\Country;
use App\Models\System\State;
use App\Models\System\Status;

class Client extends MainModelSoft
{

    protected $fillable = [
        'code',
        'name',
        'status_id',
        'type',
        'image',
        'account_id',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }
    public function state()
    {
        return $this->belongsTo(State::class);
    }
    public function country()
    {
        return $this->belongsToThrough(Country::class,State::class);
    }
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
    public function revenues()
    {
        return $this->hasMany(Revenue::class);
    }
    public function status()
    {
        return $this->belongsTo(Status::class);
    }


    public static function search($search)
    {
        return empty($search) ? static::query()
            : static::query()->where('name', 'like', '%' . $search . '%')
                ->orWhere('phone', 'like', '%' . $search . '%')
                ->orWhere('code', 'like', '%' . $search . '%')
                ->orWhereHas('state', fn($q) => $q->where('name','like', '%'.$search.'%'));
        //  ->orWhere('address', 'like', '%' . $search . '%');
    }

}
