<?php

namespace App\Models\Accounting;

use App\Models\MainModelSoft;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Staudenmeir\LaravelAdjacencyList\Eloquent\HasRecursiveRelationships;

class CostCenterNode extends MainModelSoft
{
    use  HasRecursiveRelationships;

    protected $fillable = ['name', 'parent_id','active'];

    public function costCenters()
    {
        return $this->hasMany(CostCenter::class)->withTrashed();
    }

    public function lastCostCenter()
    {
        return $this->hasOne(CostCenter::class)->latestOfMany();
    }
}
