<?php

namespace App\Models\Accounting;

use App\Models\Inventory\Product;
use App\Models\MainModelSoft;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Staudenmeir\LaravelAdjacencyList\Eloquent\HasRecursiveRelationships;
use Staudenmeir\LaravelAdjacencyList\Eloquent\Traits\HasAdjacencyList;


class AccCategory extends MainModelSoft
{
    use  HasRecursiveRelationships;

//    protected $table = 'acc_categories';

    public static $types = ['account','product'];

    protected $fillable = ['code','name','slug','credit','parent_id','active','usable','system'];

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

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function scopeAccount($query)
    {
        return $query->where('type','account');
    }
    public function scopeProduct($query)
    {
        return $query->where('type','product');
    }

    public static function search($search)
    {
        return empty($search) ? static::query()
            : static::query()->where('id', 'like', '%'.$search.'%')
                ->orWhere('name', 'like', '%'.$search.'%')
                ->orWhere('type', 'like', '%'.$search.'%')
                ->orWhereHas('parent', fn($q) => $q->where('name','like', '%'.$search.'%'))
                ->orWhereHas('elements', fn($q) => $q->where('name','like', '%'.$search.'%'));
    }

    public static function boot()
    {
        parent::boot();
        static::creating(function($model)
        {
            if (!empty($model->parent_id)){
                $parent = AccCategory::withCount('children','accounts','ancestors')->find($model->parent_id);
                if ($parent->accounts_count > 0 ){
                    return false ;
                }
                $model->code =$parent->code .  str_pad( ((int)( $parent->children_count ?? 0) + $parent->accounts_count + 1 ),2, '0', STR_PAD_LEFT);
                $model->credit = $parent->credit;
                $model->usable = 1;
            }
        });
        static::created(function($model){
            if($model->children()->exists()){
                $model->usable = 0;
                $model->save();
            }
        });
        static::updated(function($model){
            if($model->children()->exists()){
                $model->usable = 0;
                $model->save();
            }
        });
    }



}
