<?php

namespace App\Models\Accounting;

use App\Models\MainModelSoft;
use App\Models\System\ModelGroup;
use App\Models\User;
use Spatie\Activitylog\LogOptions;

class Transaction extends MainModelSoft
{
//    use  LogsActivity;

    protected $fillable = ['code', 'amount','type_group', 'type', 'note', 'due', 'paper_ref',
        'first_party_id','second_party_id', 'group_id', 'ledger_id','currency_id','ex_rate','currency_total',
        'responsible_id','created_by','edited_by', 'posted', 'locked', 'system'];

    protected $casts = ['due' => 'datetime'];

    public static array $TYPES = [
        //'JE', Journal entry represented as Ledger class

        'CI', // Cash IN transaction where treasury (TR) account is debit
        'CO', // Cash Out transaction where treasury (TR) account is Credit
        'NR', // Note Receivable
        'NP', // Note Payable
        'COGS', // Cost OF Goods Transaction

        //'PO', // purchase order only view for Bill
        'PI', // purchase invoice Code for Bill
        'PR', // purchase return Code to change Later

        // Inventory Transactions
//        'IO', // Issue Offering to decrease items Form warehouse
//        'RS', // Receive Supply to increase items To warehouse
//        'IR',  // Issue Returns from warehouse to another
//        'RR',  // Receive Returns
//        'IT',  // Issue Transfer from warehouse to another
//        'RT',  // Receive Transfer from another warehouse

        // 'SO', // sales order only View For Invoice
        'SI', // sales invoice Code For invoice
        'SR', // sales return Code to change later

    ];

    public function group()
    {
        return $this->belongsTo(ModelGroup::class, 'group_id');
    }
    public function ledger()
    {
        return $this->belongsTo(Ledger::class);
    }


    public function firstParty()
    {
        return $this->belongsTo(Account::class, 'first_party_id');
    }

    public function secondParty()
    {
        return $this->belongsTo(Account::class, 'second_party_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }


    public function editor()
    {
        return $this->belongsTo(User::class, 'edited_by');
    }

    public function responsible()
    {
        return $this->belongsTo(User::class, 'responsible_id');
    }


    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->setDescriptionForEvent(fn(string $eventName) => "This Transaction has been {$eventName}")
            ->logOnly(['user_id', 'amount', 'type', 'description', 'due'])
            ->logOnlyDirty()
            ->useLogName('system');
    }

    public static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (!$model->code) {
                $transactionCount = Transaction::where('type', $model->type)->count();
                $first_party_type_code = Account::whereId($model->first_party_id)->value('type_code');
                if (!$transactionCount) {
                    $model->code = $model->type . '-' . 1;
                    return;
                }
                $model->code = $model->type . '-' . $first_party_type_code . '-' . $transactionCount + 1;//str_pad(((int)$transactionCount+ 1), 5, '0', STR_PAD_LEFT);
            }

        });
    }
}
