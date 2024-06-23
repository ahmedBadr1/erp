<?php

namespace App\Services\Inventory;

use App\Exports\Inventory\ProductsExport;
use App\Exports\UsersExport;
use App\Models\Inventory\ProductCategory;
use App\Models\Inventory\Unit;
use App\Models\Inventory\UnitCategory;
use App\Services\ClientsExport;
use App\Services\MainService;
use Exception;
use Maatwebsite\Excel\Facades\Excel;

class UnitService extends MainService
{

    public function all($fields = null, $single = true)
    {
        $query = Unit::query();
        $data = $fields ?? (new Unit())->getFillable();
        if ($single) {
            $query->whereNull('unit_group_id');
        } else {
            $query->whereNotNull('unit_group_id');
        }
        return $query->get($data);
    }

    public function categories($fields = null,bool $active = null,$relations = [],$countRelations = [])
    {
        $data = $fields ?? (new UnitCategory())->getFillable();
        $query = UnitCategory::query();

//        if ($active){
//            $query->active($active);
//        }
        if (!empty($relations)) {
            $query->with(...$relations);
        }
        if (!empty($countRelations)) {
            $query->withCount(...$countRelations);
        }
        return $query->get($data);
    }


    public function search($search)
    {
        $search = trim($search);
        return empty($search) ? Unit::query()
            : Unit::query()->where('name', 'like', '%' . $search . '%')
                ->orWhereHas('category', fn($q) => $q->where('like', '%' . $search . '%'));
    }

    public function store(array $data,$code = null)
    {
        $category = UnitCategory::create([
           'name' =>  $data['name'] ,
           'description' => $data['description']
        ]);
        foreach ($data['units'] as $unit){
            Unit::create([...$unit,'category_id' => $category->id]);
        }
        return true;
    }

    public function update($product, array $data)
    {
        return $product->update($data);
    }

    public function destroy($unit)
    {
        if ($unit->products_count > 0) {
            return 0;
        } else {
            $unit->delete();
        }
    }

    public function export($collection = null)
    {
        return Excel::download(new ProductsExport($collection), 'products_' . date('d-m-Y') . '.xlsx');
    }
}
