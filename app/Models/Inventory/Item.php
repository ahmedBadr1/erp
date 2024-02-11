<?php

namespace App\Models\Inventory;

use App\Models\MainModelSoft;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends MainModelSoft
{
   protected $fillable = ['inv_transaction_id','bill_item_id','product_id','serial','related_serial','price','sold'];

   public function transaction ()
   {
       return $this->belongsTo(InvTransaction::class);
   }

   public function product()
   {
       return $this->belongsTo(Product::class);
   }
}
