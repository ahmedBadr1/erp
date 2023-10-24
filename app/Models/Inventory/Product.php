<?php

namespace App\Models\Inventory;

use App\Models\Accounting\Category;
use App\Models\Formula;
use App\Models\Employee\Employee;
use App\Models\System\Tax;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Znck\Eloquent\Traits\BelongsToThrough;

class Product extends Model
{
    use HasFactory ,BelongsToThrough , LogsActivity;

    protected $fillable = [
        'name', 'name_2', 'code', 'warehouse_id', 'origin_number', 'type', 'name_2', 'price', 'd_price', 'sd_price', 'min_price', 'ref_price',
'last_cost', 'avg_cost', 'first_cost', 'profit_margin', 'warranty', 'expire_date', 'barcode', 'hs_code', 'batch_number',
'tax1_id', 'tax2_id', 'unit_id', 'brand_id', 'vendor_id', 'employee_id', 'weight', 'width', 'length', 'height', 'max_limit', 'min_limit',
        'require_barcode', 'repeat_barcode', 'negative_stock', 'can_be_sold', 'can_be_purchased', 'returnable', 'active', 'category_id'];

    protected $casts = [ 'expire_date' => 'date', 'require_barcode' => 'boolean', 'repeat_barcode' => 'boolean', 'negative_stock' => 'boolean',
        'can_be_sold' => 'boolean', 'can_be_purchased' => 'boolean', 'returnable' => 'boolean', 'active' => 'boolean'];
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function tax_1()
    {
        return $this->belongsTo(Tax::class,'tax1_id');
    }
    public function tax_2()
    {
        return $this->belongsTo(Tax::class,'tax2_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->setDescriptionForEvent(fn(string $eventName) => "This Department has been {$eventName}")
            ->logOnly([
                'name',
                'code',
                'avg_price',
                'category_id'
            ])->logOnlyDirty()
            ->useLogName('system');
        // Chain fluent methods for configuration options
    }
    public static function search($search)
    {
        return empty($search) ? static::query()
            : static::query()->where('code', 'like', '%' . $search . '%')
                ->orWhere('name', 'like', '%' . $search . '%')
                ->orWhereHas('category', fn($q) => $q->where('name','like', '%'.$search.'%'));
    }
}
