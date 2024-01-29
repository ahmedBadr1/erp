<?php

namespace App\Models\Accounting;

use App\Models\MainModelSoft;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Installment extends MainModelSoft
{
    protected $fillable = ['credit','amount','due_at','note','paper_ref','comment','note_type', 'account_id', 'bank_id', 'installmentable'];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function bank()
    {
        return $this->belongsTo(Account::class,'bank_id');
    }

    public function installmentable()
    {
        return $this->morphTo('installmentable');
    }
}
