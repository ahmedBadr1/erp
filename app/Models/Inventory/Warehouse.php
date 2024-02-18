<?php

namespace App\Models\Inventory;


use App\Models\Accounting\Account;
use App\Models\Employee\Employee;
use App\Models\MainModelSoft;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Warehouse extends MainModelSoft
{
    use LogsActivity;

    protected $fillable = ['name', 'description','type', 'space','height','is_rma','is_rented','has_security','warehouse_category_id',
        'account_id', 'client_id','price_list_id','manager_id','active',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class,);
    }


    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class,'stocks',);
    }
    public function items()
    {
        return $this->hasMany(ItemHistory::class);
    }

    public function lastItem ()
    {
        return $this->hasOne(ItemHistory::class)->latestOfMany();
    }
    public function manager()
    {
        return $this->belongsTo(User::class,'manager_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->setDescriptionForEvent(fn(string $eventName) => "Warehouse has been {$eventName}")
            ->logOnly([
                'name',
                'type',
                'address',
                'manager_id',])
            ->logOnlyDirty()
            ->useLogName('system');
        // Chain fluent methods for configuration options
    }


}
