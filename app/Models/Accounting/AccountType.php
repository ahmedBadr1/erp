<?php

namespace App\Models\Accounting;

use App\Models\MainModelSoft;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountType extends MainModelSoft
{
    protected $fillable = ['name',"description",'active'];

    public function nodes()
    {
        return $this->hasMany(Node::class);
    }

    public function accounts()
    {
        return $this->hasMany(Account::class);
    }
}
