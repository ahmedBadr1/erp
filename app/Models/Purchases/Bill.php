<?php

namespace App\Models\Purchases;

use App\Models\Inventory\Branch;
use App\Models\Inventory\Item;
use App\Models\Inventory\Warehouse;
use App\Models\System\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    use HasFactory;

    protected $fillable = ['code','vendor_id', 'status_id', 'billed_at', 'due_at','number','tax_id','tax_total','discount','sub_total','total', 'notes','parent_id'];

    protected $casts = [
        'billed_at' => 'date',
        'due_at' => 'date',
    ];

    public function vendor()
    {
        return $this->belongsTo(Supplier::class);
    }
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function items()
    {
        return $this->hasMany(Item::class);
    }
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
    public function getBilledAttribute()
    {
        return $this->billed_at?->format('d M Y');
    }
    public function getDueAttribute()
    {
        return $this->due_at?->format('d M Y');
    }

    public static function search($search)
    {
        return empty($search) ? static::query()
            : static::query()->where('total', 'like', '%' . $search . '%')
                ->orWhere('number', 'like', '%' . $search . '%')
                ->orWhere('notes', 'like', '%' . $search . '%')
                ->orWhereHas('vendor', fn($q) => $q->where('name','like', '%'.$search.'%'))
                ->orWhereHas('status', fn($q) => $q->where('name','like', '%'.$search.'%'));
    }
}
