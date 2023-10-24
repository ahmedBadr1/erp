<?php

namespace App\Models\Accounting;

use App\Models\System\Status;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Account extends Model
{
    use HasFactory , LogsActivity , SoftDeletes;

    protected $fillable = ['code', 'name', 'type', 'description','opening_balance','opening_balance_date', 'system', 'active', 'category_id', 'currency_id', 'status_id'];

    protected array $TYPES= ['credit','debit'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }
    public function status()
    {
        return $this->belongsTo(Status::class);
    }
    public function creator()
    {
        return $this->belongsTo(User::class,'created_by');
    }
    public function entries()
    {
        return $this->hasMany(Entry::class);
    }
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->setDescriptionForEvent(fn(string $eventName) => "This Account has been {$eventName}")
            ->logOnly(['code', 'name', 'type', 'description', 'active', 'category_id', 'currency_id', 'status_id',])
            ->logOnlyDirty()
            ->useLogName('system');
    }
}
