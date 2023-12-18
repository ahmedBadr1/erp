<?php

namespace App\Models\Accounting;

use App\Models\MainModelSoft;
use App\Models\System\Currency;
use App\Models\System\Status;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Account extends MainModelSoft
{
    use LogsActivity;

    protected $fillable = ['code', 'name', 'credit', 'description', 'opening_balance', 'opening_balance_date', 'system', 'active', 'acc_category_id', 'currency_id', 'status_id'];

    protected array $TYPES = ['credit', 'debit'];

    public function category()
    {
        return $this->belongsTo(AccCategory::class);
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
        return $this->belongsTo(User::class, 'created_by');
    }

    public function entries()
    {
        return $this->hasMany(Entry::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->setDescriptionForEvent(fn(string $eventName) => "This Account has been {$eventName}")
            ->logOnly(['code', 'name', 'type', 'description', 'active', 'acc_category_id', 'currency_id', 'status_id',])
            ->logOnlyDirty()
            ->useLogName('system');
    }

    public static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $category = AccCategory::withCount('children', 'accounts', 'ancestors')->find($model->acc_category_id);
            if ($category->children_count > 0) {
                return false;
            }
            $model->code = $category->code . str_pad(((int)$category->children_count + $category->accounts_count + 1), 4, '0', STR_PAD_LEFT);
            $model->credit = $category->credit;
        });
    }
}
