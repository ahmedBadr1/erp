<?php

namespace App\Services\Inventory;

use App\Exports\Inventory\ProductsExport;
use App\Exports\UsersExport;
use App\Models\Inventory\Brand;
use App\Models\Inventory\Product;
use App\Services\ClientsExport;
use App\Services\MainService;
use Exception;
use Maatwebsite\Excel\Facades\Excel;

class BrandService extends MainService
{

    public function all($fields = null,$active=1)
    {
        $data = $fields ?? (new Brand())->getFillable();

        return Brand::active($active)->get($data);
    }


    public function search($search)
    {
        $search = trim($search);
        return empty($search) ? Brand::query()
            : Brand::query()->where('name', 'like', '%' . $search . '%');

    }

    public function store(array $data)
    {
        try {
            $Brand = Brand::create($data);
            return $Brand;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function update($product, array $data)
    {
        try {
            $product->update($data);
            return $product;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function destroy($product)
    {
        if ($product->items_count > 0) {
            return 0;
        } else {
            $product->delete();
        }
    }

    public function export($collection =null)
    {
        return Excel::download(new ProductsExport($collection), 'products_'.date('d-m-Y').'.xlsx');
    }
}
