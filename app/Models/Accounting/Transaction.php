<?php

namespace App\Models\Accounting;

use App\Models\MainModelSoft;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Transaction extends MainModelSoft
{
//    use  LogsActivity;

    protected $fillable = ['code','amount', 'type', 'description','due','je_code','document_no','ledger_id','account_id','user_id','posted','locked','system'];

    protected $casts = ['due' => 'datetime'];

    public static array $TYPES = ['SO','SR','PO','PR','EX','CI',"CO"];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ledger()
    {
        return $this->belongsTo(Ledger::class);
    }

    public function partner()
    {
        return $this->belongsTo(Account::class);
    }


    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->setDescriptionForEvent(fn(string $eventName) => "This Transaction has been {$eventName}")
            ->logOnly(['user_id','amount', 'type', 'description','due'])
            ->logOnlyDirty()
            ->useLogName('system');
    }

    public static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if(! $model->code){
                $transactionCount = Transaction::where('type',$model->type)->count();
                if (!$transactionCount) {
                    $model->code = $model->type .'-'. 1 ;
                    return ;
                }
                $model->code = $model->type .'-'. $transactionCount +1 ;//str_pad(((int)$transactionCount+ 1), 5, '0', STR_PAD_LEFT);
            }

        });
    }
}
