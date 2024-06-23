<?php

namespace App\Models\Inventory;

use App\Models\MainModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Unit extends MainModel
{

    protected $fillable = ['name','code', 'comment', 'order', 'type', 'category_id', 'ratio'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(UnitCategory::class,'category_id');
    }

    public function products() : HasMany
    {
        return $this->hasMany(Product::class);
    }
    public function greater($value)
    {
        return $value * $this->conversion_factor;
    }

    public function smalller($value)
    {
        return $value / $this->conversion_factor;
    }
}
