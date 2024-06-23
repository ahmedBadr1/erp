<?php

namespace App\Models\System;

use App\Models\Accounting\Ledger;
use App\Models\Accounting\Transaction;
use App\Models\Inventory\InvTransaction;
use App\Models\MainModel;
use App\Models\Purchases\Bill;

class ModelGroup extends MainModel
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

    public function invTransactions()
    {
        return $this->hasMany(InvTransaction::class,'group_id');
    }

    public function firstInvTransaction()
    {
        return $this->hasOne(InvTransaction::class,'group_id')->latestOfMany();
    }


    public function po()
    {
        return $this->hasMany(Bill::class,'group_id')->where('type','PO');
    }

    public function so()
    {
        return $this->hasMany(Bill::class,'group_id')->where('type','SO');
    }

    public function firstTransaction()
    {
        return $this->hasOne(Transaction::class,'group_id')->oldestOfMany();
    }

    public function lastTransaction()
    {
        return $this->hasOne(Transaction::class,'group_id')->latestOfMany();
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
