<?php

namespace App\Models\System;

use App\Models\MainModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Currency extends MainModel
{
    use  LogsActivity ;

    protected $fillable = ['name','code', 'ratio', 'active'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->setDescriptionForEvent(fn(string $eventName) => "This Currency has been {$eventName}")
            ->logOnly(['name', 'code', 'ratio',  'active'])
            ->logOnlyDirty()
            ->useLogName('system');
    }
}
