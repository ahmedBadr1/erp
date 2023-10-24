<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Entry extends Model
{
    use HasFactory , LogsActivity , SoftDeletes;

    protected $fillable = ['debit','credit','description','account_id','transaction_id'];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->setDescriptionForEvent(fn(string $eventName) => "This Entry has been {$eventName}")
            ->logOnly(['debit','credit','account_id'])
            ->logOnlyDirty()
            ->useLogName('system');
    }

}
