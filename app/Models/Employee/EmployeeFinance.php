<?php

namespace App\Models\Employee;

use App\Models\Accounting\Currency;
use App\Models\MainModelSoft;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmployeeFinance extends MainModelSoft
{
    use HasFactory;

    protected $fillable = ["employee_id","currency_id","salary_circle","salary","work_days_in_week","work_hours","allowances","car_allownce","total","hourly_value","minute_value"];


    public function employee() {
        return $this->belongsTo(Employee::class);
    }

    public function currency() {
        return $this->hasOne(Currency::class, 'id' , 'currency_id');
    }
}
