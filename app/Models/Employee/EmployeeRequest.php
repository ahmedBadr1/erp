<?php

namespace App\Models\Employee;

use App\Models\MainModelSoft;
use App\Models\System\Status;

class EmployeeRequest extends MainModelSoft
{
    protected $fillable = ['type', 'reason' , 'project_name','name', 'responsible', 'time_from','time_to', 'latitude', 'longitude','address','from', 'response', 'employee_id', 'status_id', 'time_valid_to' , 'time_valid_in_seconds'];
    // protected $casts = ['time_from'=>'datetime','time_to'=>'datetime'];
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function getFromTimeAttribute()
    {
        return Date('h:i A', strtotime($this->time_from));
    }

    public function getToTimeAttribute()
    {
        return Date('h:i A', strtotime($this->time_to));
    }


}
