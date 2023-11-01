<?php

namespace App\Models\Accounting;

use App\Models\MainModelSoft;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Transaction extends MainModelSoft
{
    use  LogsActivity;

    protected $fillable = ['amount', 'type', 'description','due','user_id'];

    public static $expenses = ['cost of goods','rent','salary','Operating','Extraordinary','Accrued','Prepaid','Fixed'];

    protected $casts = ['due' => 'datetime'];

    public static array $TYPES = ['so','si','po','ex','ci',"co"];

    public static array $METHODS = ['cash','bank'];

    public function refrence()
    {
        return $this->morphTo();
    }


    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->setDescriptionForEvent(fn(string $eventName) => "This Transaction has been {$eventName}")
            ->logOnly(['user_id','amount', 'type', 'description','due'])
            ->logOnlyDirty()
            ->useLogName('system');
    }
}
