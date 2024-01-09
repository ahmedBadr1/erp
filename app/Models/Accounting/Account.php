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
//    use LogsActivity;

    protected $fillable = ['code', 'name', 'credit', 'description','account_type_id', 'c_opening', 'd_opening', 'credit_limit','debit_limit', 'opening_date', 'system', 'active', 'node_id', 'currency_id', 'status_id'];

    protected array $TYPES = ['credit', 'debit'];
    protected $casts = ['opening_date' => 'date'];

    public function type()
    {
        return $this->belongsTo(AccountType::class,'account_type_id');
    }
    public function node()
    {
        return $this->belongsTo(Node::class,'node_id');
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

    public function transactions()
    {
        return $this->belongsToThrough(Transaction::class,Entry::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->setDescriptionForEvent(fn(string $eventName) => "This Account has been {$eventName}")
            ->logOnly(['code', 'name', 'type', 'description', 'active', 'node_id', 'currency_id', 'status_id',])
            ->logOnlyDirty()
            ->useLogName('system');
    }

    public static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $node = Node::withCount('children', 'accounts', 'ancestors')->find($model->node_id);
            if ($node->children_count > 0) {
                return false;
            }
            $model->code = $node->code . str_pad(((int)$node->children_count + $node->accounts_count + 1), 4, '0', STR_PAD_LEFT);
            $model->credit = $node->credit;
        });
    }
}
