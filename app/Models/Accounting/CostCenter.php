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

    protected $fillable = ['name', 'parent_id', 'active',  'system'];

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
            if (!empty($model->parent_id)) {
                $parent = CostCenter::withCount('children', 'ancestors')->find($model->parent_id);
                $model->code = $parent->code . str_pad(((int)($parent->children_count ?? 0)  + 1), 2, '0', STR_PAD_LEFT);
            }else{
                $model->code =str_pad( CostCenter::whereNull('parent_id')->count() +1 ,2, '0', STR_PAD_LEFT) ;
            }
        });
    }

}
