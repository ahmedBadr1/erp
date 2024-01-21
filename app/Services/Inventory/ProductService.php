<?php

namespace App\Services\Inventory;

use App\Exports\Inventory\ProductsExport;
use App\Exports\UsersExport;
use App\Models\Accounting\Entry;
use App\Models\Crm\Client;
use App\Models\Inventory\Product;
use App\Models\User;
use App\Services\ClientsExport;
use App\Services\MainService;
use Exception;
use Maatwebsite\Excel\Facades\Excel;

class ProductService extends MainService
{

    public function all($fields = null)
    {
        $data = $fields ?? (new z())->getFillable();

        return Product::active()->get($data);
    }


    public function search($search)
    {
        $search = trim($search);
        return empty($search) ? Product::query()
            : Product::query()->where('name', 'like', '%' . $search . '%')
                ->orWhere('short_name', 'like', '%' . $search . '%')
                ->orWhere('code', 'like', '%' . $search . '%')
                ->orWhere('origin_number', 'like', '%' . $search . '%')
                ->orWhere('barcode', 'like', '%' . $search . '%')
                ->orWhere('hs_code', 'like', '%' . $search . '%')
                ->orWhere('batch_number', 'like', '%' . $search . '%');
//                ->orWhereHas('account', fn($q) => $q->where('name', 'like', '%' . $search . '%'));
    }

    public function store(array $data)
    {
        try {
            $product = Product::create($data);
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
