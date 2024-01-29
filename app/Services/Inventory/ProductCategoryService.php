<?php

namespace App\Services\Inventory;

use App\Exports\Inventory\ProductsExport;
use App\Exports\UsersExport;
use App\Models\Inventory\Product;
use App\Models\Inventory\ProductCategory;
use App\Services\ClientsExport;
use App\Services\MainService;
use Exception;
use Maatwebsite\Excel\Facades\Excel;

class ProductCategoryService extends MainService
{

    public function all($fields = null,$active = true)
    {
        $data = $fields ?? (new ProductCategory())->getFillable();

        return ProductCategory::active($active)->get($data);
    }


    public function search($search)
    {
        $search = trim($search);
        return empty($search) ? ProductCategory::query()
            : ProductCategory::query()->where('name', 'like', '%' . $search . '%')
                ->orWhere('type', 'like', '%' . $search . '%');
    }

    public function store(array $data)
    {
        try {
            $product = ProductCategory::create($data);
            return $product;
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
