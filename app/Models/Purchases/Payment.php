<?php

namespace App\Models\Purchases;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = ['paid_at','amount','bill_id','vendor_id'];

    protected $casts = ['paid_at'=> 'date'];

    public static array $METHODS = ['cash','bank'];
    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function getPaidAttribute()
    {
        return $this->paid_at?->format('d M Y');
    }

    public static function search($search)
    {
        return empty($search) ? static::query()
            : static::query()->where('paid_at', 'like', '%' . $search . '%')
                ->orWhere('amount', 'like', '%' . $search . '%')
                ->orWhereHas('vendor', fn($q) => $q->where('name','like', '%'.$search.'%'))
                ->orWhereHas('bill', fn($q) => $q->where('total','like', '%'.$search.'%'));
    }

}
