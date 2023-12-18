<?php

namespace App\Models\Sales;

use App\Models\MainModel;
use App\Models\MainModelSoft;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends MainModelSoft
{
  protected $fillable = ['item_id','invoice_id','quantity'];
}
