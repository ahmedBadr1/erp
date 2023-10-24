<?php

namespace App\Models\Employee;

use App\Models\MainModelSoft;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeReport extends MainModelSoft
{


    protected $fillable = ['employee_id','start_in','end_in','check_in','check_out','work_hours','hourly_value','late_hours','late_hour_value','overtime_hours','overtime_hour_value','total','currency'];

}
