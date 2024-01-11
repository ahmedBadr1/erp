<?php

namespace App\Models\Accounting;

use App\Models\MainModelSoft;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Entry extends MainModelSoft
{
//    use  LogsActivity ;

    protected $fillable = ['amount','credit','account_id','ledger_id','cost_center_id','comment'];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function costCenter()
    {
        return $this->belongsTo(CostCenter::class);
    }

    public function ledger()
    {
        return $this->belongsTo(Ledger::class);
    }
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->setDescriptionForEvent(fn(string $eventName) => "This Entry has been {$eventName}")
            ->logOnly(['credit','account_id'])
            ->logOnlyDirty()
            ->useLogName('system');
    }

}
