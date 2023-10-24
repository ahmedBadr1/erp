<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Transaction extends Model
{
    use HasFactory , LogsActivity , SoftDeletes;

    protected $fillable = ['user_id','type','date','total','description'];

    protected $casts = ['date' => 'datetime'];

    public static array $TYPES = ['so','si','po'];
    public function entries()
    {
        return $this->hasMany(Entry::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->setDescriptionForEvent(fn(string $eventName) => "This Transaction has been {$eventName}")
            ->logOnly(['user_id','total','description'])
            ->logOnlyDirty()
            ->useLogName('system');
    }
}
