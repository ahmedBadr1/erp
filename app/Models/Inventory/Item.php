<?php

namespace App\Models\Inventory;

use App\Models\Accounting\Transaction;
use App\Models\Element;
use App\Models\MainModelSoft;
use App\Models\ProductionOrder;
use App\Models\Purchases\Bill;
use App\Models\User;
use Illuminate\Database\Eloquent\Prunable;

class Item extends MainModelSoft
{
    use Prunable;

    protected $fillable = ['bill_id', 'product_id', 'quantity', 'price', 'comment', 'expire_at', 'unit_id', 'warehouse_id', 'user_id'];

    protected $casts = [
        'expire_at' => 'date'
    ];

    public function prunable()
    {
        return static::where('deleted_at', '<=', now()->subMonth('6'));
    }

    public function category()
    {
        return $this->belongsToThrough('App\Models\Category', 'App\Models\Element');
    }


    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
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
            : static::query()->where('id', 'like', '%' . $search . '%')
                ->orWhere('name', 'like', '%' . $search . '%')
                ->orWhere('description', 'like', '%' . $search . '%')
                ->orWhere('quantity', 'like', '%' . $search . '%')
                ->orWhere('price', 'like', '%' . $search . '%')
                ->orWhereHas('product', fn($q) => $q->where('name', 'like', '%' . $search . '%')->orWhere('code', 'like', '%' . $search . '%'))
                ->orWhereHas('warehouse', fn($q) => $q->where('categories.name', 'like', '%' . $search . '%'));
    }

    public static function searchInvoice($search)
    {
        return empty($search) ? static::query()
            : static::query()->where('type', 'product')
                ->whereHas('warehouse')
                ->where('name', 'like', '%' . $search . '%');

    }
}
