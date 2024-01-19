<?php

namespace App\Models\Accounting;

use App\Models\MainModelSoft;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Ledger extends MainModelSoft
{
//    use  LogsActivity;

    protected $fillable = ['amount', 'description','due','currency_id','ex_rate','created_by','edited_by','responsible_id'];

    protected $casts = ['due' => 'datetime'];

    protected $appends = ['code'];

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
