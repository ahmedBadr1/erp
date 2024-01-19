<?php

namespace App\Models\Accounting;

use App\Models\MainModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupAccount extends MainModel
{
    public function accounts()
    {
        return $this->hasMany(Account::class);
    }
}
