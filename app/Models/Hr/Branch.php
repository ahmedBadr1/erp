<?php

namespace App\Models\Hr;

use App\Models\MainModelSoft;
use App\Models\System\City;
use App\Models\System\Country;
use App\Models\System\State;
use App\Models\User;
use App\Models\WorkAt;
use App\Traits\WorkAtTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Branch extends MainModelSoft
{
    use HasFactory, WorkAtTrait ;

    protected $table = 'branches';
    protected $fillable = [
        'user_id', 'name', 'content', 'image', 'icon','attachment', 'active','phone','manger_name',
        'address', 'order_id', 'latitude', 'longitude','polygon','email','type','country_id','state_id','city_id',
        'parent_id', 'manager_id','shift_id', 'is_clients', 'is_mangers','is_services','is_papers','is_projects','is_shifts',
        'share_client','share_service','share_paper','share_shift','share_manger'
    ];
    protected $casts = [
        // 'name'    => 'array',
        // 'content' => 'array',
        // 'address' => 'array',
    ];

    public static $types = ['central','main','sub'];
    public function user() {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function manager() {
        return $this->belongsTo(User::class,'manager_id')->withTrashed();
    }

    public function country() {
        return $this->belongsTo(Country::class);
    }

    public function state() {
        return $this->belongsTo(State::class);
    }

    public function city() {
        return $this->belongsTo(City::class);
    }

    public function shift() {
        return $this->hasOne(Shift::class, 'id', 'shift_id');
    }

    public function groups() {
        return $this->belongsToMany(Group::class);
    }

    public function branchPapers() {
        return $this->hasMany(BranchPaper::class);
    }

    public function managements() {
        return $this->hasMany(Management::class);
    }

    public function departments() {
        return $this->hasManyThrough(Department::class,Management::class);
    }

    public function childern() {
        return $this->hasMany(Branch::class, 'parent_id');
    }

    public function parent() {
        return $this->belongsTo(Branch::class, 'parent_id');
    }

    public function scopeNumberOfEmps() {

       $managements = Management::where('branch_id',$this->id)->pluck('id')->toArray();
       $departments = Department::whereIn('management_id', $managements)->pluck('id')->toArray();

       $emp_of_all_mgmts = WorkAt::whereIn('workable_id', $managements)->where('workable_type', 'managements')->count();
       $emp_of_all_deps = WorkAt::whereIn('workable_id', $departments)->where('workable_type', 'departments')->count();

       return count($this->workers) + $emp_of_all_deps + $emp_of_all_mgmts;
    }

    public function scopeEmps() {

        $managements = Management::where('branch_id',$this->id)->pluck('id')->toArray();
        $departments = Department::whereIn('management_id', $managements)->pluck('id')->toArray();

        $emp_of_all_mgmts = WorkAt::whereIn('workable_id', $managements)->where('workable_type', 'managements')->pluck('id')->toArray();
        $emp_of_all_deps = WorkAt::whereIn('workable_id', $departments)->where('workable_type', 'departments')->pluck('id')->toArray();

        return array_merge($emp_of_all_deps,$emp_of_all_mgmts,$this->workers->pluck('id')->toArray()) ;
    }
}
