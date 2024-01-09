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

    protected $fillable = ['amount', 'description','due','user_id'];

    protected $casts = ['due' => 'datetime'];

    protected $appends = ['code'];

    public function user()
    {
        return $this->belongsTo(User::class);
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

    public function getCodeAttribute()
    {
        return  'JE-' . $this->id ;
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->setDescriptionForEvent(fn(string $eventName) => "This Transaction has been {$eventName}")
            ->logOnly(['user_id','amount', 'type', 'description','due'])
            ->logOnlyDirty()
            ->useLogName('system');
    }
}
