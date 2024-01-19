<?php

namespace App\Models\Accounting;

use App\Models\Crm\Client;
use App\Models\MainModelSoft;
use App\Models\Purchases\Supplier;
use App\Models\System\Status;
use App\Models\User;
use Spatie\Activitylog\LogOptions;

class Account extends MainModelSoft
{
//    use LogsActivity;

    protected $fillable = ['code', 'name', 'type_code', 'credit', 'description', 'account_type_id', 'cost_center_id', 'c_opening', 'd_opening', 'credit_limit', 'debit_limit', 'opening_date', 'system', 'active', 'node_id', 'currency_id','group_account_id', 'status_id'];

    protected array $TYPES = ['credit', 'debit'];
    protected $casts = ['opening_date' => 'date'];

    public function type()
    {
        return $this->belongsTo(AccountType::class, 'account_type_id');
    }

    public function costCenter()
    {
        return $this->belongsTo(CostCenter::class);
    }

    public function node()
    {
        return $this->belongsTo(Node::class, 'node_id');
    }

    public function group()
    {
        return $this->belongsTo(GroupAccount::class, 'group_account_id');
    }

    public function relatedAccounts()
    {
        return $this->hasManyThrough(Account::class,'group_accounts', 'group_account_id');
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

    public function client()
    {
        return $this->hasone(Client::class);
    }

    public function supplier()
    {
        return $this->hasone(Supplier::class);
    }

    public function taxes()
    {
        return $this->hasMany(Tax::class);
    }

    public function currencies()
    {
        return $this->hasMany(Currency::class);
    }

    public function transactions()
    {
        return $this->belongsToThrough(Transaction::class, Entry::class);
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
            if (!$model->account_type_id) {
                $model->account_type_id = $node->account_type_id;
            }
            $type = AccountType::withCount('accounts')->whereId($model->account_type_id)->first();
            $model->type_code = $type->code . ((int)$type->accounts_count + 1);
        });
    }
}
