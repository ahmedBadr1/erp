<?php

namespace App\Models\Accounting;

use App\Models\Inventory\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Staudenmeir\LaravelAdjacencyList\Eloquent\HasRecursiveRelationships;
use Staudenmeir\LaravelAdjacencyList\Eloquent\Traits\HasAdjacencyList;


class Category extends Model
{
    use HasFactory  , HasRecursiveRelationships;

    public static $types = ['account','product'];

    protected $fillable = ['name','slug','type','parent_id'];

    public function accounts()
    {
        return $this->hasMany(Account::class);
    }

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

}
