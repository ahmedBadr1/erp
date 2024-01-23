<?php

namespace App\Models\Accounting;

use App\Models\Inventory\Product;
use App\Models\MainModelSoft;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Staudenmeir\LaravelAdjacencyList\Eloquent\HasRecursiveRelationships;

class CostCenter extends MainModelSoft
{
    use  HasRecursiveRelationships;

    protected $fillable = ['name','description', 'cost_center_node_id', 'active',  'system'];

    public function node()
    {
        return $this->belongsTo(CostCenterNode::class,'cost_center_node_id');
    }

    public function accounts()
    {
        return $this->hasMany(Account::class);
    }

    public function entries()
    {
        return $this->hasMany(Entry::class);
    }

    public static function search($search)
    {
        return empty($search) ? static::query()
            : static::query()->where('id', 'like', '%' . $search . '%')
                ->orWhere('name', 'like', '%' . $search . '%')
                ->orWhere('type', 'like', '%' . $search . '%')
                ->orWhereHas('parent', fn($q) => $q->where('name', 'like', '%' . $search . '%'))
                ->orWhereHas('elements', fn($q) => $q->where('name', 'like', '%' . $search . '%'));
    }

    public static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $node = CostCenterNode::withCount('children', 'costCenters', 'ancestors')->find($model->cost_center_node_id);
            $model->code = $node->code . str_pad(((int)$node->children_count + $node->cost_centers_count + 1), 4, '0', STR_PAD_LEFT);
        });
    }

}
