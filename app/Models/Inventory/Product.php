<?php

namespace App\Models\Inventory;

use App\Models\Accounting\Node;
use App\Models\Employee\Employee;
use App\Models\MainModelSoft;
use App\Models\System\Tax;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Znck\Eloquent\Traits\BelongsToThrough;

class Product extends MainModelSoft
{
    use LogsActivity;

    protected $fillable = [
        'name', 'short_name', 'code', 'warehouse_id', 'origin_number', 'type', 'price', 'd_price', 'sd_price', 'min_price', 'ref_price',
      'avg_cost', 'profit_margin', 'warranty', 'expire_date', 'barcode', 'hs_code', 'batch_number',
        'tax1_id', 'tax2_id', 'unit_id', 'brand_id', 'vendor_id', 'user_id', 'weight', 'width', 'length', 'height', 'max_limit', 'min_limit',
        'require_barcode', 'repeat_barcode', 'negative_stock', 'can_be_sold', 'can_be_purchased', 'returnable', 'active', 'inv_category_id'];

    protected $casts = ['expire_date' => 'date', 'require_barcode' => 'boolean', 'repeat_barcode' => 'boolean', 'negative_stock' => 'boolean',
        'can_be_sold' => 'boolean', 'can_be_purchased' => 'boolean', 'returnable' => 'boolean', 'active' => 'boolean'];

    public function category()
    {
        return $this->belongsTo(InvCategory::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function responsible()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(Item::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function taxes()
    {
        return $this->belongsToMany(Tax::class, 'product_tax');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->setDescriptionForEvent(fn(string $eventName) => "Product has been {$eventName}")
            ->logOnly([
                'name',
                'code',
            ])->logOnlyDirty()
            ->useLogName('system');
        // Chain fluent methods for configuration options
    }

    public static function search($search)
    {
        return empty($search) ? static::query()
            : static::query()->where('code', 'like', '%' . $search . '%')
                ->orWhere('name', 'like', '%' . $search . '%')
                ->orWhereHas('category', fn($q) => $q->where('name', 'like', '%' . $search . '%'));
    }
}
