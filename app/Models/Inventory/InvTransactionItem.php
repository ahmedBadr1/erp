<?php

namespace App\Models\Inventory;

use App\Models\MainModelSoft;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvTransactionItem extends MainModelSoft
{
   protected $fillable = ['inv_transaction_id','product_id','quantity','price','accepted'];

   public function transaction()
   {
       return $this->belongsTo(InvTransaction::class);
   }

   public function product()
   {
       return $this->belongsTo(Product::class);
   }
}
