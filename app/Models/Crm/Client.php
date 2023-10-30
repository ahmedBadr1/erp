<?php

namespace App\Models\Crm;

use App\Models\MainModelSoft;
use App\Models\Sales\Invoice;
use App\Models\Sales\Revenue;
use App\Models\System\Country;
use App\Models\System\State;
use App\Models\System\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends MainModelSoft
{

    protected $fillable = [
        'name',
        'phone',
        'code',
        'email',
        'status_id',
        'address',
        'type',
        'credit_limit',
        'image',
    ];

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
