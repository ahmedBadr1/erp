<?php

namespace App\Models\Inventory;

use App\Models\Accounting\Transaction;
use App\Models\Element;
use App\Models\MainModelSoft;
use App\Models\ProductionOrder;
use App\Models\Purchases\Bill;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;

class Item extends MainModelSoft
{
    use Prunable;

    protected $fillable= [
        'name', 'sku', 'quantity', 'description', 'price', 'tax_exclusive', 'tax_inclusive', 'type', 'cost', 'expire_at' ,
        'unit_id','bill_id','invoice_id','product_id','warehouse_id','user_id'  ];

    protected $casts = [
        'expire_at' => 'date'
    ];
    public static $units = ['kg','g','t'];

    public function prunable()
    {
        return static::where('deleted_at', '<=', now()->subMonth('6'));
    }

    public function category()
    {
        return $this->belongsToThrough('App\Models\Category', 'App\Models\Element');
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
    public function creator()
    {
        return $this->belongsTo(User::class,'user_id');
    }
    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }

    public function getCreatedAttribute()
    {
        return $this->created_at?->format('d M Y');
    }

    public static function search($search)
    {
        return empty($search) ? static::query()
            : static::query()->where('id', 'like', '%'.$search.'%')
                ->orWhere('name', 'like', '%'.$search.'%')
                ->orWhere('description', 'like', '%'.$search.'%')
                ->orWhere('quantity', 'like', '%'.$search.'%')
                ->orWhere('price', 'like', '%'.$search.'%')
                ->orWhereHas('product', fn($q) => $q->where('name','like', '%'.$search.'%')->orWhere('code','like', '%'.$search.'%'))
                ->orWhereHas('warehouse', fn($q) => $q->where('categories.name','like', '%'.$search.'%'));
    }
    public static function searchInvoice($search)
    {
        return empty($search) ? static::query()
            : static::query()->where('type','product')
                ->whereHas('warehouse')
                ->where('name', 'like', '%'.$search.'%');

    }
}
