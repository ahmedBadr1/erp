<?php

namespace App\Models\Accounting;

use App\Models\MainModelSoft;
use App\Models\System\ModelGroup;
use App\Models\User;
use Spatie\Activitylog\LogOptions;

class Ledger extends MainModelSoft
{
//    use  LogsActivity;

    protected $fillable = ['group_id','amount', 'note','due','created_by','edited_by','responsible_id',
        'currency_id','ex_rate','currency_total','posted', 'locked', 'system'];

    protected $casts = ['due' => 'datetime'];

    protected $appends = ['code'];

    public function group()
    {
        return $this->belongsTo(ModelGroup::class, 'group_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class,'created_by');
    }


    public function editor()
    {
        return $this->belongsTo(User::class,'edited_by');
    }

    public function responsible()
    {
        return $this->belongsTo(User::class,'responsible_id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function transaction()
    {
        return $this->hasOne(Transaction::class);
    }

    public function firstTransaction()
    {
        return $this->hasOne(Transaction::class)->oldestOfMany();
    }

    public function lastTransaction()
    {
        return $this->hasOne(Transaction::class)->latestOfMany();
    }
    public function ciTransaction()
    {
        return $this->hasOne(Transaction::class)->where('type','CI');
    }

    public function coTransaction()
    {
        return $this->hasOne(Transaction::class)->where('type','CO');
    }

    public function entries()
    {
        return $this->hasMany(Entry::class);
    }

    public function accounts()
    {
        return $this->hasManyThrough(Account::class,Entry::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }
    public function getCodeAttribute()
    {
        return  'JE-' . $this->id ;
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->setDescriptionForEvent(fn(string $eventName) => "This Ledger has been {$eventName}")
            ->logOnly(['user_id','amount', 'type', 'description','due'])
            ->logOnlyDirty()
            ->useLogName('system');
    }
}
