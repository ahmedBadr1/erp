<?php

namespace App\Models\Accounting;

use App\Models\Inventory\Warehouse;
use App\Models\MainModelSoft;
use App\Models\Purchases\Supplier;
use App\Models\Sales\Client;
use App\Models\System\Status;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\LogOptions;

class Account extends MainModelSoft
{
//    use LogsActivity;

    protected $fillable = ['code', 'name', 'type_code', 'credit', 'description', 'account_type_id',
        'select_cost_center', 'cost_center_id', 'c_opening', 'd_opening', 'credit_limit', 'debit_limit',
        'opening_date','usable', 'system', 'active', 'node_id', 'currency_id','account_group_id', 'status_id'];
    protected array $TYPES = ['credit', 'debit'];
    protected $casts = ['opening_date' => 'date'];


    protected $fields = [ 'name','description', 'c_opening', 'd_opening', 'credit_limit', 'debit_limit'];

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
        return $this->belongsTo(AccountGroup::class, 'group_account_id');
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

    public function warehouse()
    {
        return $this->hasOne(Warehouse::class);
    }

    public function supplier()
    {
        return $this->hasOne(Supplier::class);
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
            $model->code = $node->code . str_pad(((int)$node->children_count + $node->accounts_count + 1), 4, '0', STR_PAD_LEFT);
            $model->credit = $node->credit;
            if (!$model->select_cost_center) {
                $model->select_cost_center = $node->select_cost_center ;
            }
            if (!$model->account_type_id) {
                if (isset($node->account_type_id)){
                    $model->account_type_id = $node->account_type_id ;

                }
                $type = AccountType::withCount('accounts')->whereId($model->account_type_id)->first();
                if ($type){
                    if (isset($type->code) && $type->code == 'TR'){
                        $model->type_code = $type->code . ((int)$type->accounts_count + 1);
                    }else{
                        $model->type_code = ((int)$type->accounts_count + 1) ;
                    }
                }
            }

        });

        static::created(function ($model) {
            switch ($model->type?->code){
//                case 'I' :
//                    Warehouse::create([
//                            'name' => $model->name,
//                        'account_id' => $model->id
//                        ]);
//                    break;
                case 'AP' :
                    Supplier::create([
                        'name' => $model->name,
                        'account_id' => $model->id
                    ]);
                    break;
                case "AR" :
                    Client::create([
                        'name' => $model->name,
                        'account_id' => $model->id
                    ]);
                    break;
                default:
            }
        });

    }
}
