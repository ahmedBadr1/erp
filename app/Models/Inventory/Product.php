<?php

namespace App\Models\Inventory;

use App\Models\Accounting\Tax;
use App\Models\Accounting\Transaction;
use App\Models\MainModelSoft;
use App\Models\User;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Product extends MainModelSoft
{
    use LogsActivity;

    protected $fillable = [
        'name', 'short_name', 'warehouse_id', 'origin_number', 'type','barcode', 'hs_code', 'batch_number',
      'warehouse_shelf_id',  'part_number', 'sku', 'location', 'oe_number','e_code' ,'e_code_type',
        's_price', 'd_price', 'sd_price', 'min_price', 'ref_price', 'avg_cost','last_cost', 'fifo', 'lifo',
        'opening_balance', 'profit_margin', 'warranty', 'valid_to', 'max_limit', 'min_limit','reorder_limit',
       'has_serial', 'require_barcode', 'repeat_barcode', 'negative_stock', 'can_be_sold', 'can_be_purchased', 'returnable', 'active',
         'brand_id', 'vendor_id', 'user_id', 'inv_category_id'];

    protected $casts = ['expire_date' => 'date', 'require_barcode' => 'boolean', 'repeat_barcode' => 'boolean', 'negative_stock' => 'boolean',
        'can_be_sold' => 'boolean', 'can_be_purchased' => 'boolean', 'returnable' => 'boolean', 'active' => 'boolean'];

    public function category()
    {
        return $this->belongsTo(ProductCategory::class);
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

    public function transactions()
    {
        return $this->belongsToMany(Transaction::class, 'product_transaction')->withPivot('quantity','price');
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
