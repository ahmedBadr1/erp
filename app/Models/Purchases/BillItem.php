<?php

namespace App\Models\Purchases;

use App\Models\Accounting\Tax;
use App\Models\Element;
use App\Models\Inventory\InvTransaction;
use App\Models\Inventory\Product;
use App\Models\MainModelSoft;
use App\Models\ProductionOrder;
use App\Models\Purchases\Bill;
use App\Models\User;
use Illuminate\Database\Eloquent\Prunable;

class BillItem extends MainModelSoft
{
    use Prunable;

    protected $fillable = ['bill_id', 'product_id', 'quantity', 'price','cost','tax_value','sub_total','total','tax_id',
        'expire_at','comment', 'inv_transaction_id'];

    protected $casts = [
        'expire_at' => 'date'
    ];

//    public function prunable()
//    {
//        return static::where('deleted_at', '<=', now()->subMonth('6'));
//    }


    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function tax()
    {
        return $this->belongsTo(Tax::class);
    }

    public function invTransaction()
    {
        return $this->belongsTo(InvTransaction::class);
    }


}
