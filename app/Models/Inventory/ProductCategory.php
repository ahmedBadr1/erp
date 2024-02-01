<?php

namespace App\Models\Inventory;



use App\Models\MainModelSoft;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Staudenmeir\LaravelAdjacencyList\Eloquent\HasRecursiveRelationships;

class ProductCategory extends MainModelSoft
{
    use  HasRecursiveRelationships;

    protected $fillable = ['name','type','color','parent_id','active'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }


}
