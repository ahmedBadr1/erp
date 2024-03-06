<?php

namespace App\Models\Accounting;

use App\Models\MainModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountGroup extends MainModel
{
    protected $fillable = ['created_by'];

        public $timestamps = false ;
    public function accounts()
    {
        return $this->hasMany(Account::class);
    }
}
