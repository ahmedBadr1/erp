<?php

namespace App\Models\Accounting;

use App\Models\MainModel;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Currency extends MainModel
{
    use  LogsActivity ;

    protected $fillable = ['name','code','symbol', 'ex_rate','last_rate','sub_unit','gain_account','loss_account', 'active'];

    protected $casts = ['last_rate'=>'date'];

    public function gainAccount()
    {
        return $this->belongsTo(Account::class,'gain_account');
    }

    public function lossAccount()
    {
        return $this->belongsTo(Account::class,'loss_account');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->setDescriptionForEvent(fn(string $eventName) => "This Currency has been {$eventName}")
            ->logOnly(['name', 'code', 'ex_rate',  'active'])
            ->logOnlyDirty()
            ->useLogName('system');
    }

    public static function search($search)
    {
        return empty($search) ? static::query()
            : static::query()->where('id', 'like', '%'.$search.'%')
                ->orWhere('name', 'like', '%'.$search.'%')
                ->orWhere('code', 'like', '%'.$search.'%');
    }
}
