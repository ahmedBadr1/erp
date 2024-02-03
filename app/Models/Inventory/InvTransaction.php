<?php

namespace App\Models\Inventory;

use App\Models\Accounting\TransactionGroup;
use App\Models\MainModelSoft;
use App\Models\Purchases\Bill;
use App\Models\Purchases\Supplier;
use App\Models\Sales\Client;
use App\Models\Sales\Invoice;
use App\Models\User;
use Spatie\Activitylog\LogOptions;

class InvTransaction extends MainModelSoft
{

    protected $fillable = ['code', 'amount', 'type', 'note', 'due', 'accepted_at', 'paper_ref',
        'from_id', 'to_id', 'group_id', 'supplier_id', 'responsible_id', 'created_by', 'edited_by', 'pending', 'system'];

    protected $casts = ['due' => 'datetime','accepted_at'=>'datetime'];

    public static array $TYPES = [
        'IO', // Issue Offering to decrease items Form warehouse
        'RS', // Receive Supply to increase items To warehouse
        'IR',  // Issue Returns from warehouse to another
        'RR',  // Receive Returns
        'IT',  // Issue Transfer from warehouse to another
        'RT',  // Receive Transfer from another warehouse
    ];

    public function group()
    {
        return $this->belongsTo(TransactionGroup::class, 'group_id');
    }


    public function from()
    {
        return $this->belongsTo(Warehouse::class, 'from_id');
    }

    public function to()
    {
        return $this->belongsTo(Warehouse::class, 'to_id');
    }

    public function items()
    {
        return $this->hasMany(Item::class,);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function editor()
    {
        return $this->belongsTo(User::class, 'edited_by');
    }

    public function responsible()
    {
        return $this->belongsTo(User::class, 'responsible_id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_inv_transaction')->withPivot('quantity', 'price','cost');
    }


    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->setDescriptionForEvent(fn(string $eventName) => "This Transaction has been {$eventName}")
            ->logOnly(['user_id', 'amount', 'type', 'description', 'due'])
            ->logOnlyDirty()
            ->useLogName('system');
    }

    public static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (!$model->code) {
                $transactionCount = InvTransaction::where('type', $model->type)->count();
                $model->code = $model->type . '-' . $model->from_id . '-' . ((int) $transactionCount + 1);//str_pad(((int)$transactionCount+ 1), 5, '0', STR_PAD_LEFT);
            }

        });
    }
}
