<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Currency extends Model
{
    use HasFactory  , LogsActivity , SoftDeletes;

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
