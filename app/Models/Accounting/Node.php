<?php

namespace App\Models\Accounting;

use App\Models\MainModelSoft;
use Illuminate\Support\Str;
use Staudenmeir\LaravelAdjacencyList\Eloquent\HasRecursiveRelationships;


class Node extends MainModelSoft
{
    use  HasRecursiveRelationships;

    protected $fillable = ['name', 'slug', 'parent_id', "account_type_id",'select_cost_center', 'active', 'usable', 'system'];

//    protected $casts =[ 'select_cost_center' => 'boolean'];

    public function type()
    {
        return $this->belongsTo(AccountType::class, 'account_type_id');
    }

    public function accounts()
    {
        return $this->hasMany(Account::class)->withTrashed();
    }

    public function lastAccount()
    {
        return $this->hasOne(Account::class)->latestOfMany();
    }

//    public function descendantCategories()
//    {
//        return $this->hasManyOfDescendants(__CLASS__,'parent_id');
//    }
//
//    public function childrens()
//    {
//        return $this->hasMany(__CLASS__,'parent_id');
//    }


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
                $parent = Node::withCount('children', 'accounts', 'ancestors')->find($model->parent_id);
//                if ($parent->accounts_count > 0 ){
//                    return false ;
//                }
                if (!$model->slug) {
                    $model->slug = Str::slug($model->name);
                }
                $model->code = $parent->code . str_pad(((int)($parent->children_count ?? 0) + $parent->accounts_count + 1), 2, '0', STR_PAD_LEFT);
                $model->credit = $parent->credit;
                $model->usable = 1;

                if (!$model->select_cost_center) {
                    $model->select_cost_center = $parent->select_cost_center ?? null;
                }
                if (!$model->account_type_id) {
                    $model->account_type_id = $parent->account_type_id ?? null;
                }
            }
        });
    }


}
