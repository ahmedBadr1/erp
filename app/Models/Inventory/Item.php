<?php

namespace App\Models\Inventory;

use App\Models\Element;
use App\Models\MainModelSoft;
use App\Models\ProductionOrder;
use Illuminate\Database\Eloquent\Prunable;

class Item extends MainModelSoft
{
    use Prunable;

    protected $fillable = ['product_id', 'warehouse_id', 'quantity', 'price','avg_cost','unit_id', 'inv_transaction_id', 'second_party_id','second_party_type', 'balance', 'in',];


    public function prunable()
    {
        return static::where('deleted_at', '<=', now()->subMonth('6'));
    }


    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function transaction()
    {
        return $this->belongsTo(InvTransaction::class);
    }

}
