<?php

namespace App\Models\Employee;

use App\Models\Attendance;
use App\Models\Hr\Branch;
use App\Models\Hr\Department;
use App\Models\Hr\Management;
use App\Models\Hr\Relative;
use App\Models\Hr\Shift;
use App\Models\MainModelSoft;
use App\Models\System\Attachment;
use App\Models\System\Status;
use App\Models\WorkAt;
use Carbon\Carbon;

class Employee extends MainModelSoft
{

    protected $fillable = ['email', 'phone', 'first_name', 'last_name', 'second_name', 'country_id', 'city_id', 'user_id', 'address', "shift_id", 'has_overtime'];

    public function country()
    {
        return $this->hasOne('\App\Models\System\Country', 'id', 'country_id');
    }

    public function city()
    {
        return $this->hasOne('\App\Models\System\City', 'id', 'city_id');
    }

    public function user()
    {
        return $this->hasOne('\App\Models\User', 'id', 'user_id');
    }

    public function info()
    {
        return $this->hasOne(EmployeeInfo::class);
    }


    public function todayReport()
    {
        return $this->hasOne(EmployeeReport::class)->whereDate('created_at', Date('Y-m-d'));
    }

    public function reports()
    {
        return $this->hasMany(EmployeeReport::class);
    }

    public function academic()
    {
        return $this->hasMany(EmployeeInfo::class);
    }

    public function experiences()
    {
        return $this->hasMany(EmployeeExperience::class);
    }

    public function cources()
    {
        return $this->hasMany(EmployeeCourse::class);
    }

    public function employmentData()
    {
        return $this->hasOne(EmploymentData::class);
    }

    public function contract()
    {
        return $this->hasOne(EmploymentContract::class);
    }

    public function finance()
    {
        return $this->hasOne(EmployeeFinance::class);
    }

    public function dues()
    {
        return $this->hasMany(EmployeeDue::class);
    }

    public function vacation()
    {
        return $this->hasOne(EmployeeVacation::class);
    }

    public function relative()
    {
        return $this->hasOne(Relative::class);
    }

    public function workAt()
    {
        return $this->hasOne(WorkAt::class, 'employee_id', 'id');
    }

    public function shift()
    {
        return $this->hasOne(Shift::class, 'id', 'shift_id');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function attendance()
    {
        return $this->hasone(Attendance::class)->latest();
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class, 'attachable_id', 'id')->where('attachable_type', 'employees');
    }

    public function scopeAttendance()
    {
        return Attendance::where('employee_id', $this->id)->whereDate('created_at', Carbon::today())->first();
    }

    public function getNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getBranchAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function requests()
    {
        return $this->hasMany(EmployeeRequest::class, 'employee_id', 'id');
    }

    public function scopeBranchName()
    {
        $workAt = $this->workAt;
        if ($workAt == null) {
            return "";
        }

        if ($workAt->workable instanceof Branch) {
            return Branch::whereId($workAt->workable->id)->select('name')->value('name');
        } else if ($workAt->workable instanceof Management) {
            return Branch::whereId($workAt->workable->branch_id)->select('name')->value('name');
        } else {
            return Branch::whereId($workAt->workable->management->branch_id)->select('name')->value('name');
        }
    }

    public function scopeManagementName()
    {
        $workAt = $this->workAt;
        if ($workAt == null) {
            return "";
        }

        if ($workAt->workable instanceof Branch) {
            return "";
        } else if ($workAt->workable instanceof Management) {
            return Management::whereId($workAt->workable->workable_id)->select('name')->value('name');
        } else {
            return Department::whereId($workAt->workable->management->id)->select('name')->value('name');
        }
    }

    public function scopeBranch($query, $id)
    {
        $managementsId = Management::where('branch_id', $id)->pluck('id')->toArray();
        $departmentsId = Department::whereIn('management_id', $managementsId)->pluck('id')->toArray();

        return $query->whereHas('workAt', function ($qr) use ($id, $managementsId, $departmentsId) {
            $qr->where(fn($q) => $q->where('workable_type', 'branches')->where('workable_id', $id))
                ->orWhere(fn($q) => $q->where('workable_type', 'managements')->whereIn('workable_id', $managementsId))
                ->orWhere(fn($q) => $q->where('workable_type', 'departments')->whereIn('workable_id', $departmentsId));
        });

    }


    public function getShift()
    {
        if (!empty($this->shift)) {
            return $this->shift;
        } else {
            if ($this->workAt->workable instanceof Branch) {
                return Shift::whereId(Branch::whereId($this->workAt->workable->id)->value('shift_id'))->first();
            } else if ($this->workAt->workable instanceof Management) {
                return Shift::whereId(Branch::whereId($this->workAt->workable->branch_id)->value('shift_id'))->first();
            } else {
                return Shift::whereId(Branch::whereId($this->workAt->workable->management->branch_id)->value('shift_id'))->first();
            }
        }
    }


    public function getRequestInThisDay($type, $date)
    {
        $ApprovedSuccessId = Status::where('type', 'employee-requests')->where('name', 'accepted')->value('id');
        return EmployeeRequest::where(['employee_id' => $this->id, 'type' => $type])
            ->where('status_id', $ApprovedSuccessId)
            ->whereDate('time_from', $date)->first();
    }


    public function getEmpFinanceReports($date, $timeZone)
    {


        $arrResult = [
            'WorkHours' => 0,
            'WorkHoursCost' => 0,
            'LateHours' => 0,
            'LateHoursCost' => 0,
            'OvertimeHours' => 0,
            'OvertimeHoursCost' => 0,
            'WorkHourFee' => 0,
            'LateHourFee' => 0,
            'OverTimeFee' => 0,
            'totalFee' => 0,
        ];

        // get Shift
        $emp_shift = $this->getShift();
        if (empty($emp_shift)) {
            return $arrResult;
        }

        $day = $emp_shift->days->where('day_name', Carbon::parse($date)->timezone($timeZone)->format("D"))->first();
        // get basic wrok Hours

        if (empty($day)) {
            return $arrResult;
        }


        $NormalWorkHours = $this->getWorkHours($day, $timeZone); // 3add sa3at el 3aml fe el youm

        $NormalHourFee = $this->finance?->hourly_value ?? 0; // se3r el sa3a
        $LateHourFee = $emp_shift->late_cost * $NormalHourFee; // se3r sa3et el ta25er
        $OverTimeHourFee = $emp_shift->overtime_cost * $NormalHourFee; // se3r sa3et el overtime

        // low 3aml check in abl mwa3eed el 3aml 2w 2a3d 2aktr mn el work hours beta3to ba7splo sa3at el 3aml beta3t el day bas
        $ActualWorkHours = $this->getActualWorkHours($date, $timeZone);
        $ActualWorkHours += $this->workHoursInReq($date, $timeZone, $day);

        if ($ActualWorkHours > $NormalWorkHours) {
            $ActualWorkHours = $NormalWorkHours;
        }

        $WorkHoursCost = $ActualWorkHours * $NormalHourFee;

        $LateHours = $this->getLateHours($date, $timeZone, $day->late_start_in);

        $LateHoursCost = $LateHours * $LateHourFee;

        if ($this->has_overtime) {
            $overTimeHours = $this->getOverTimeHours($date, $timeZone, $day);
            $overTimeCost = $overTimeHours * $OverTimeHourFee;
        } else {
            $overTimeHours = 0;
            $overTimeCost = 0;
        }


        return [
            'WorkHours' => $ActualWorkHours,
            'WorkHoursCost' => $WorkHoursCost,
            'LateHours' => $LateHours,
            'LateHoursCost' => $LateHoursCost,
            'OvertimeHours' => $overTimeHours,
            'OvertimeHoursCost' => $overTimeCost,
            'WorkHourFee' => $NormalHourFee,
            'LateHourFee' => $LateHourFee,
            'OverTimeFee' => $OverTimeHourFee,
            'totalFee' => round((($WorkHoursCost + $overTimeCost) - $LateHoursCost), 2)
        ];

    }

    private function getWorkHours($day, $timeZone)
    {
        return $this->calDiffBetweenHours($day->end_in, $day->start_in, $timeZone);
    }

    private function calDiffBetweenHours($date1, $date2, $timeZone)
    {
        return round(abs((strtotime(Carbon::parse($date1)->timezone($timeZone)->format('h:i A')) - strtotime(Carbon::parse($date2)->timezone($timeZone)->format('h:i A'))) / 3600), 2);
    }


    private function isBefore($time1, $time2, $timeZone)
    {
        return strtotime(Carbon::parse($time1)->timezone($timeZone)->format('h:i A')) < strtotime(Carbon::parse($time2)->timezone($timeZone)->format('h:i A'));
    }

    private function getLateHours($date, $timeZone, $start_in)
    {
        $attendance = $this->getTodayAttendance($date);
        if (gettype($attendance) == 'integer') {
            return 0;
        }

        if ($this->isBefore($start_in, $attendance->check_in, $timeZone)) {
            return $this->calDiffBetweenHours($attendance->check_in, $start_in, $timeZone);
        } else {
            return 0;
        }
    }

    private function getActualWorkHours($date, $timeZone)
    {

        $attendance = $this->getTodayAttendance($date);
        if (gettype($attendance) == 'integer') {
            return 0;
        }

        if (empty($attendance->check_out)) {
            return 0;
        }

        return round(abs((strtotime(Carbon::parse($attendance->check_out)->timezone($timeZone)->format('h:i A')) - strtotime(Carbon::parse($attendance->check_in)->timezone($timeZone)->format('h:i A'))) / 3600), 2);
    }

    public function getOverTimeHours($date, $timeZone, $dayWork, $reqId = null)
    {

        if ($reqId != null) {
            $empRequests = EmployeeRequest::whereId($reqId)->get();
        } else {
            $empRequests = $this->getEmpReqToday($date);
        }


        if (count($empRequests) == 0) {
            return 0;
        }


        $overTimeHours = 0;

        // start abl el shift 2w start ba3d shift w 5alst ba3d el shift
        foreach ($empRequests as $req) {

            if (!empty($dayWork)) {
                if ($this->isBefore($dayWork->end_in, $req->time_to, $timeZone)) {
                    $overTimeHours += $this->calDiffBetweenHours($req->time_to, $dayWork->end_in, $timeZone);
                }
                if ($this->isBefore($req->time_from, $dayWork->early_start_in, $timeZone)) {

                    $overTimeHours += $this->calDiffBetweenHours($dayWork->early_start_in, $req->time_from, $timeZone);

                } else if ($this->isBefore($dayWork->end_in, $req->time_from, $timeZone)) {
                    $overTimeHours += $this->calDiffBetweenHours($req->time_to, $req->time_from, $timeZone);
                }
            } else {
                $overTimeHours += $this->calDiffBetweenHours($req->time_to, $req->time_from, $timeZone);
            }


        }

        return $overTimeHours;

    }


    public function workHoursInReq($date, $timeZone, $dayWork, $reqId = null)
    {
        if ($reqId != null) {
            $empRequests = EmployeeRequest::whereId($reqId)->get();
        } else {
            $empRequests = $this->getEmpReqToday($date);
        }
        $workHours = 0;

        $attendance = $this->getTodayAttendance($date);
        if (gettype($attendance) == 'integer') {
            return 0;
        }

        if (empty($dayWork)) {
            return 0;
        }
        if (empty($attendance->check_out)) {
            return 0;
        }
        // low request fe wa2t el 3aml el asasy ye7sbha work hours 3adya
        foreach ($empRequests as $req) {
            if ($this->isBefore($attendance->check_out, $dayWork->end_in, $timeZone) && $this->isBefore($req->time_from, $dayWork->end_in, $timeZone)) {
                $workHours += $this->calDiffBetweenHours($dayWork->end_in, $req->time_from, $timeZone);
            }
        }

        return $workHours;
    }


    private function getEmpReqToday($date)
    {
        $statusId = Status::where('type', 'employee-requests')->where('name', 'accepted')->value('id');

        $empRequests = EmployeeRequest::where([
            'employee_id' => $this->id,
            'type' => 'mission',
            'status_id' => $statusId,
        ])->whereDate('created_at', Date('Y-m-d', strtotime($date)))->get();

        return $empRequests;
    }


    private function getTodayAttendance($date)
    {
        $attendance = Attendance::where('employee_id', $this->id)->whereDate('created_at', date('Y-m-d', strtotime($date)))->first();
        if (empty($attendance)) {
            return 0;
        } else {
            return $attendance;
        }
    }

}
