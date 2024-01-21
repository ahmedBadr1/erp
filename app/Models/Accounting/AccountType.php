<?php

namespace App\Models\Accounting;

use App\Models\MainModelSoft;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountType extends MainModelSoft
{
    protected $fillable = ['name',"description",'active'];

    public static array $types = [
        'TR', // Treasury Account
        'CO', // Cash In Account
        'CI', // Cash Out Account
        'WH', // WareHouse Account
        'SP', // Supplier Account
        'CL', // Client Account
        'TAX', // Tax Account
    ];

    public function nodes()
    {
        return $this->hasMany(Node::class);
    }

    public function accounts()
    {
        return $this->hasMany(Account::class);
    }
}
