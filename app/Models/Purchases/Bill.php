<?php

namespace App\Models\Purchases;

use App\Models\Accounting\Account;
use App\Models\Accounting\Currency;
use App\Models\Accounting\Tax;
use App\Models\Inventory\Branch;
use App\Models\Inventory\Warehouse;
use App\Models\MainModelSoft;
use App\Models\System\ModelGroup;
use App\Models\System\Status;
use App\Models\User;

class Bill extends MainModelSoft
{

    protected $fillable = ['code','type','treasury_id','warehouse_id','second_party_id','second_party_type', 'status_id', 'deliver_at', 'date','paper_ref','group_id'
        ,'tax_id','gross_total','tax_total','discount','sub_total','total', 'note','tax_id','tax_exclusive','tax_inclusive' ,'canceled',
        'currency_id','ex_rate','currency_total','responsible_id','created_by','edited_by'];

    protected $casts = [
        'billed_at' => 'date',
        'due_at' => 'date',
    ];

    public static array $TYPES =[
        'PO' => 'Purchases Order',
        'PR' => 'Purchases Return',
        'SO' => 'Sales Order',
        'SR' => 'Sales Return',
    ];

    public function group()
    {
        return $this->belongsTo(ModelGroup::class, 'group_id');
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
    public function secondParty()
    {
        return $this->morphTo('second_party');
    }
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class,'warehouse_id');
    }

    public function treasury()
    {
        return $this->belongsTo(Account::class,'treasury_id');
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class,'currency_id');
    }

    public function tax()
    {
        return $this->belongsTo(Tax::class);
    }
    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function items()
    {
        return $this->hasMany(BillItem::class);
    }
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
    public function getBilledAttribute()
    {
        return $this->billed_at?->format('d M Y');
    }
    public function getDueAttribute()
    {
        return $this->due_at?->format('d M Y');
    }

    public static function search($search)
    {
        return empty($search) ? static::query()
            : static::query()->where('total', 'like', '%' . $search . '%')
                ->orWhere('number', 'like', '%' . $search . '%')
                ->orWhere('notes', 'like', '%' . $search . '%')
                ->orWhereHas('vendor', fn($q) => $q->where('name','like', '%'.$search.'%'))
                ->orWhereHas('status', fn($q) => $q->where('name','like', '%'.$search.'%'));
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->code) {
                $billCode = Bill::where('type',$model->type)->latest()->value('code')  ;
//                $warehouse_code = Account::whereHas('warehouse',fn($q)=>$q->whereid($model->warehouse_id))->value('type_code');
                if (!$billCode) {
                    $model->code = $model->type . '-'. $model->warehouse_id . '-' . 1;
                    return;
                }else{
                    if (preg_match('/^[A-Z]*-[1-9]*-(\d+)/', $billCode, $matches)) {
                        $count = $matches[1] + 1;
                        $model->code = $model->type . '-'.  $model->warehouse_id . '-'  . $count ;
                    } else{
                        throw new \Exception('Wrong Bill Code');
                    }
                }
            }
        });
    }
}
