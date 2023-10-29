<?php

namespace App\Models\Purchases;

use App\Models\Accounting\Account;
use App\Models\Employee\Employee;
use App\Models\MainModelSoft;
use App\Models\System\Country;
use App\Models\System\State;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Znck\Eloquent\Traits\BelongsToThrough;
use App\Traits\Taggable;
use App\Traits\HasContacts;
use App\Traits\HasLocation;

class Supplier extends MainModelSoft
{
    protected $fillable = ['business_name', 'name', 'code', 'responsible_id', 'credit_limit', 'warranty','tax_number',
        'registration_number', 'payment_method', 'phone', 'telephone', 'email', 'active'];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }
    public function employee()
    {
        return $this->belongsTo(Employee::class,'responsible_id');
    }
    public function state()
    {
        return $this->belongsTo(State::class);
    }
    public function country()
    {
        return $this->belongsToThrough(Country::class,State::class);
    }
    public function bills()
    {
        return $this->hasMany(Bill::class);
    }
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public static function search($search)
    {
        return empty($search) ? static::query()
            : static::query()->where('business_name', 'like', '%' . $search . '%')
                ->orWhere('name', 'like', '%' . $search . '%')
                ->orWhere('last_name', 'like', '%' . $search . '%')
                ->orWhere('phone', 'like', '%' . $search . '%')
                ->orWhere('code', 'like', '%' . $search . '%')
                ->orWhere('email', 'like', '%' . $search . '%')
                ->orWhere('address', 'like', '%' . $search . '%');
    }
}
