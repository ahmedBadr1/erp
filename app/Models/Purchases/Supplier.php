<?php

namespace App\Models\Purchases;

use App\Enums\OrderStatus;
use App\Models\Accounting\Account;
use App\Models\Employee\Employee;
use App\Models\MainModelSoft;
use App\Models\System\Country;
use App\Models\System\State;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends MainModelSoft
{
    protected $fillable = ['name', 'code', 'responsible_id', 'warranty','tax_number','debit_limit',
        'registration_number', 'payment_method', 'phone', 'telephone', 'email', 'active','account_id'];

    protected $casts = [
//        'status' => OrderStatus::class
    ];
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

    protected function status(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => OrderStatus::tryFrom($value)
        );
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
