<?php

namespace App\Models\Sales;

use App\Models\Inventory\Item;
use App\Models\System\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use HasFactory ,softDeletes;

    protected $fillable = ['client_id','status_id','invoiced_at','due_at','number','tax_id','discount','tax_total','sub_total','total','parent_id'];
    protected $casts = ['invoiced_at'=>'date','due_at'=>'date'];


    public function status():\Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    public function client():\Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Client::class);
    }


    public function items()
    {
        return $this->hasMany(Item::class);
    }
    public function revenues()
    {
        return $this->hasMany(Revenue::class);
    }



    public function getInvoicedAttribute()
    {
        return $this->invoiced_at?->format('d M Y');
    }
    public function getDueAttribute()
    {
        return $this->due_at?->format('d M Y');
    }

    public static function search($search)
    {
        return empty($search) ? static::query()
            : static::query()->where('total', 'like', '%' . $search . '%')
                ->orWhere('invoice_number', 'like', '%' . $search . '%')
                ->orWhere('notes', 'like', '%' . $search . '%')
                ->orWhereHas('client', fn($q) => $q->where('name','like', '%'.$search.'%'))
                ->orWhereHas('status', fn($q) => $q->where('name','like', '%'.$search.'%'));
    }
}
