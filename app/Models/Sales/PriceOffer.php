<?php

namespace App\Models\Sales;

use App\Models\Inventory\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Znck\Eloquent\Traits\BelongsToThrough;

class PriceOffer extends Model
{
    use  BelongsToThrough;

    protected $fillable = ['client_id', 'user_id','offered_at'];

    protected $casts = ['offered_at'=>'datetime'];

    public function products()
    {

        return $this->belongsToMany(Product::class, 'product_price_offer_pivot')->withPivot('price');
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getCreatedAttribute()
    {
        return $this->created_at?->format('d M Y');
    }
    public function getOfferedAttribute()
    {
        return $this->offered_at?->format('d M Y');
    }

    public static function search($search)
    {
        return empty($search) ? static::query()
            : static::query()
                // ->where('amount', 'like', '%'.$search.'%')
                //  ->orWhere('unit', 'like', '%'.$search.'%')
                ->WhereHas('user', fn($q) => $q->where('name', 'like', '%' . $search . '%'))
                ->orWhereHas('client', fn($q) => $q->where('name', 'like', '%' . $search . '%'));

    }
}