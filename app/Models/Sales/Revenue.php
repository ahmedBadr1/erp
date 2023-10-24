<?php

namespace App\Models\Sales;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Revenue extends Model
{
    use HasFactory;

    protected $fillable = ['paid_at','amount','invoice_id','client_id'];

    protected $casts = ['paid_at'=>'date'];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function getPaidAttribute()
    {
        return $this->paid_at?->format('d M Y');
    }

    public static function search($query)
    {
        return empty($query) ? static::query()
            : static::query()->where('paid_at', 'like', '%' . $query . '%')
                ->orWhere('amount', 'like', '%' . $query . '%')
                ->orWhereHas('client', fn($q) => $q->where('name','like', '%'.$query.'%'))
                ->orWhereHas('invoice', fn($q) => $q->where('total','like', '%'.$query.'%'));
    }




}
