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

    protected $fillable = ['code','amount', 'type', 'description','due','je_code','document_no','ledger_id','account_id','responsible_id','posted','locked','system'];

    protected $casts = ['due' => 'datetime'];

    public static array $TYPES = [
        'CI', // Cash IN transaction where treasury (TR) account is debit
        'CO', // Cash Out transaction where treasury (TR) account is Credit
       // 'SO', // sales order only View For Invoice
        'SI', // sales invoice Code For invoice
        'SR', // sales return Code to change later
        //'PO', // purchase order only view for Bill
        'PI', // purchase invoice Code for Bill
        'PR', // purchase return Code to change Later
        'EX', // Exchange between warehouses
        'IO', // Issue Offering to decrease items Form warehouse
        'RS', // Receive Supply to increase items To warehouse
        'NR', // Note Receivable
        'NP', // Note Payable
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ledger()
    {
        return $this->belongsTo(Ledger::class);
    }

    public function firstParty()
    {
        return $this->belongsTo(Account::class,'first_party_id');
    }

    public function secondParty()
    {
        return $this->belongsTo(Account::class , 'second_party_id');
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
                $first_party_type_code = Account::whereId($model->first_party_id)->value('type_code');
                if (!$transactionCount) {
                    $model->code = $model->type .'-'. 1 ;
                    return ;
                }
                $model->code = $model->type .'-'. $first_party_type_code .'-'.  $transactionCount +1 ;//str_pad(((int)$transactionCount+ 1), 5, '0', STR_PAD_LEFT);
            }

        });
    }
}
