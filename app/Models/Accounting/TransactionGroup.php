<?php

namespace App\Models\Accounting;

use App\Models\MainModel;
use App\Models\Purchases\Bill;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionGroup extends MainModel
{
    public $timestamps = false ;

    protected $fillable = ['created_by'];

    public function ledgers()
    {
        return $this->hasMany(Ledger::class,'group_id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class,'group_id');
    }

    public function bills()
    {
        return $this->hasMany(Bill::class,'group_id');
    }

    public function firstTransaction()
    {
        return $this->hasOne(Transaction::class,'group_id')->oldestOfMany();
    }

    public function lastTransaction()
    {
        return $this->hasOne(Transaction::class,'group_id')->latestOfMany();
    }


    public function firstWhTransaction()
    {
        return $this->hasOne(Transaction::class,'group_id')->where('type','WH')->oldestOfMany();
    }

    public function ciTransactions()
    {
        return $this->hasMany(Transaction::class,'group_id')->where('type','CI');
    }

    public function coTransactions()
    {
        return $this->hasMany(Transaction::class,'group_id')->where('type','CO');
    }
}
