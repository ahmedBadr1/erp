<?php

namespace App\Models\Inventory;

use App\Models\Employee\Employee;
use App\Models\MainModelSoft;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends MainModelSoft
{

    protected $fillable = ['name' ,'manager_id'];

    public function manager()
    {
        $this->belongsTo(Employee::class,'manager_id');
    }
}
