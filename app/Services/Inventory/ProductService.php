<?php

namespace App\Services\Inventory;

use App\Exports\Inventory\ProductsExport;
use App\Exports\UsersExport;
use App\Models\Inventory\Product;
use App\Services\ClientsExport;
use App\Services\MainService;
use App\Services\System\TagService;
use Exception;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class ProductService extends MainService
{

    public function all($fields = null, $active = 1)
    {
        $data = $fields ?? (new Product())->getFillable();

        return Product::active($active)->get($data);
    }


    public function search($search)
    {
        $search = trim($search);
        return empty($search) ? Product::query()
            : Product::query()->where('name', 'like', '%' . $search . '%')
                ->orWhere('short_name', 'like', '%' . $search . '%')
                ->orWhere('part_number', 'like', '%' . $search . '%')
                ->orWhere('origin_number', 'like', '%' . $search . '%')
                ->orWhere('barcode', 'like', '%' . $search . '%')
                ->orWhere('hs_code', 'like', '%' . $search . '%')
                ->orWhere('batch_number', 'like', '%' . $search . '%');
//                ->orWhereHas('category', fn($q) => $q->where('name', 'like', '%' . $search . '%'));
    }

    public function store(array $data)
    {
        try {
            $product = Product::create($data);

            if (isset($data['tags'])) {
                (new TagService())->sync($data['tags'], $product, 'product');
            }
            return true;
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




    public function updateBalance($productId)
    {

        $product = Product::with('stocks')->withSum('stocks as total_balance', 'balance')->find($productId);

        $product->balance = (int) $product->total_balance ;

//        $product = Product::whereHas('items', fn($q) => $q->where('quantity', '>', 0))
//            ->withSum('items as items_quantity', 'quantity')
//            ->withSum('items as items_price', 'price')->find($productId);

//        $product->balance = $product->items_quantity;
//        $product->avg_cost = $product->items_price / $product->items_quantity;
        $product->save();
        return true;
    }

    public function export($collection = null)
    {
        return Excel::download(new ProductsExport($collection), 'products_' . date('d-m-Y') . '.xlsx');
    }
}
