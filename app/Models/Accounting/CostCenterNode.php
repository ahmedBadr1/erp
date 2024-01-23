<?php

namespace App\Models\Accounting;

use App\Models\MainModelSoft;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Staudenmeir\LaravelAdjacencyList\Eloquent\HasRecursiveRelationships;

class CostCenterNode extends MainModelSoft
{
    use  HasRecursiveRelationships;

    protected $fillable = ['name','slug', 'parent_id','active'];

    public function costCenters()
    {
        return $this->hasMany(CostCenter::class)->withTrashed();
    }

    public function lastCostCenter()
    {
        return $this->hasOne(CostCenter::class)->latestOfMany();
    }


    public static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if ($model->parent_id) {
                $parent = CostCenterNode::withCount('children', 'costCenters', 'ancestors')->find($model->parent_id);
//                if ($parent->accounts_count > 0 ){
//                    return false ;
//                }
                if (!$model->slug) {
                    $model->slug = Str::slug($model->name);
                }
                $model->code = $parent->code . str_pad(((int)($parent->children_count ?? 0) + $parent->cost_centers_count + 1), 2, '0', STR_PAD_LEFT);
                $model->usable = 1;
            }else{
                $siblings = CostCenterNode::whereNull('parent_id')->count();
                $model->code = str_pad(((int) $siblings + 1), 2, '0', STR_PAD_LEFT);
            }
        });
    }
}
