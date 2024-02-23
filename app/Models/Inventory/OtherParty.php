<?php

namespace App\Models\Inventory;

use App\Models\Accounting\Account;
use App\Models\MainModelSoft;

class OtherParty extends MainModelSoft
{
    protected $fillable = ['name','type','description','account_id','active'];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

}
