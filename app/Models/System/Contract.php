<?php

namespace App\Models\System;

use App\Models\MainModelSoft;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contract extends MainModelSoft
{
   protected $fillable = ['start_date','end_date','has_contract_type','has_contract_id'];

}
