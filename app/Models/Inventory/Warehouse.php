<?php

namespace App\Models\Inventory;


use App\Models\Employee\Employee;
use App\Models\MainModelSoft;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Warehouse extends MainModelSoft
{
    use LogsActivity;

    protected $fillable = [
        'name',
        'type',
        'location',
        'manager_id',
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function items ()
    {
        return $this->hasMany(Item::class);
    }
    public function manager()
    {
        return $this->belongsTo(Employee::class,'manager_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->setDescriptionForEvent(fn(string $eventName) => "This Department has been {$eventName}")
            ->logOnly([
                'name',
                'type',
                'location',
                'manager_id',])
            ->logOnlyDirty()
            ->useLogName('system');
        // Chain fluent methods for configuration options
    }


}
