<?php

namespace App\Models\Inventory;

use App\Models\MainModelSoft;
use App\Models\Purchases\Bill;
use App\Models\Purchases\Supplier;
use App\Models\Sales\Client;
use App\Models\Sales\Invoice;
use App\Models\System\ModelGroup;
use App\Models\User;
use Spatie\Activitylog\LogOptions;

class InvTransaction extends MainModelSoft
{

    protected $fillable = ['code','bill_id', 'amount', 'type', 'note', 'due', 'accepted_at', 'paper_ref',
        'warehouse_id','group_id','second_party_type','second_party_id', 'responsible_id', 'created_by', 'edited_by','system'];

    protected $casts = ['due' => 'datetime', 'accepted_at' => 'datetime'];

    public static array $TYPES = [
        'IO', // Issue Offering to decrease items Form warehouse
        'RS', // Receive Supply to increase items To warehouse
        'IR',  // Issue Returns from warehouse to another
        'RR',  // Receive Returns
        'IT',  // Issue Transfer from warehouse to another
        'RT',  // Receive Transfer from another warehouse
    ];

    public static array $inOther = [
            [
                'id' => 1,
                'name' => 'إستبدالات',
            ],
            [
                'id' => 2,
                'name' => 'تسوية زيادة الجرد',
            ],
        ];

    public static array $outOther = [
        [
            'id' => 3,
            'name' => 'أصول ثابتة',
        ],
        [
            'id' => 4,
            'name' => 'تسوية عجز الجرد',
        ],
    ];


    public function group()
    {
        return $this->belongsTo(ModelGroup::class, 'group_id');
    }


    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function secondParty()
    {
        return $this->morphTo('second_party');
    }

    public function items()
    {
        return $this->hasMany(InvTransactionItem::class,);
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
                $model->code = $model->type . '-' . $model->warehouse_id . '-' . ((int)$transactionCount + 1);//str_pad(((int)$transactionCount+ 1), 5, '0', STR_PAD_LEFT);
            }

        });
    }
}
