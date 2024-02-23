<?php

namespace App\Models\Inventory;


use App\Models\Accounting\Account;
use App\Models\Accounting\CostCenter;
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

    protected $fillable = ['name', 'description','type', 'space','height','is_rma','is_rented','has_security',
        'account_id','cog_account_id','s_account_id','p_account_id','sr_account_id','pr_account_id','sd_account_id','pd_account_id',
        'cost_center_id', 'ss_account_id','or_account_id',
        'client_id','price_list_id','manager_id','active',
    ];

    protected $fields = ['name', 'description','type', 'space','height','active',];

    public function account()
    {
        return $this->belongsTo(Account::class,);
    }

    public function costCenter()
    {
        return $this->belongsTo(CostCenter::class,);
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

    public function pAccount()
    {
        return $this->belongsTo(Account::class,'p_account_id');
    }

    public function prAccount()
    {
        return $this->belongsTo(Account::class,'pr_account_id');
    }

    public function pdAccount()
    {
        return $this->belongsTo(Account::class,'pd_account_id');
    }

    public function sAccount()
    {
        return $this->belongsTo(Account::class,'s_account_id');
    }

    public function srAccount()
    {
        return $this->belongsTo(Account::class,'sr_account_id');
    }

    public function sdAccount()
    {
        return $this->belongsTo(Account::class,'sd_account_id');
    }

    public function cogAccount()
    {
        return $this->belongsTo(Account::class,'cog_account_id');
    }

    public function orAccount()
    {
        return $this->belongsTo(Account::class,'or_account_id');
    }


    public function ssAccount()
    {
        return $this->belongsTo(Account::class,'ss_account_id');
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
