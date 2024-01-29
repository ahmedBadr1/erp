<?php

namespace App\Models\Purchases;

use App\Models\Accounting\Account;
use App\Models\Accounting\Transaction;
use App\Models\Accounting\TransactionGroup;
use App\Models\Inventory\Branch;
use App\Models\Inventory\Item;
use App\Models\Inventory\Warehouse;
use App\Models\MainModelSoft;
use App\Models\System\Status;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bill extends MainModelSoft
{

    protected $fillable = ['code','warehouse_id','supplier_id', 'status_id', 'billed_at', 'due_at','paper_ref','group_id'
        ,'tax_id','gross_total','tax_total','discount','sub_total','total', 'note' ,'responsible_id','created_by','edited_by'];

    protected $casts = [
        'billed_at' => 'date',
        'due_at' => 'date',
    ];

    public function group()
    {
        return $this->belongsTo(TransactionGroup::class, 'group_id');
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
    public function supplier()
    {
        return $this->belongsTo(Account::class,'supplier_id');
    }
    public function warehouse()
    {
        return $this->belongsTo(Account::class,'warehouse_id');
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function items()
    {
        return $this->hasMany(Item::class);
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
                $billsCount = Bill::count();
                $warehouse_code = Account::whereId($model->warehouse_id)->value('type_code');
                if (!$billsCount) {
                    $model->code = 'PO-'.$warehouse_code . '-' . 1;
                    return;
                }
                $model->code = 'PO-'.$warehouse_code . '-'  . $billsCount + 1 ;
            }
        });
    }
}
