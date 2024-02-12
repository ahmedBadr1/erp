<?php

namespace App\Services\Inventory;

use App\Exports\Inventory\ProductsExport;
use App\Exports\UsersExport;
use App\Models\Inventory\Product;
use App\Models\Inventory\Stock;
use App\Models\Inventory\Warehouse;
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


    public function search($search,$query=null)
    {
        $search = trim($search);
        if (!$query){
            $query = Product::query() ;
        }
        return empty($search) ? $query
            : $query->where('name', 'like', '%' . $search . '%')
//                ->orWhere('short_name', 'like', '%' . $search . '%')
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

    public function stocks(array $data = null)
    {
        $columns = $data['columns'];

        $dataset = [];

        $query = Product::query();

        if (!empty($data['warehouses'])) {
            $query->with(['stocks' =>fn($q)=>$q->whereIn('warehouse_id',$data['warehouses'])]);
        }else{
            $query->with('stocks' );
        }

        $query->withSum('stocks as global_balance','balance' );

        if (isset($data['has_balance']) && $data['has_balance'] == 1){
            $query->whereHas('stocks',fn($q)=>$q->where('balance','>',0));
        }

        if (!empty($data['products'])) {
            $query->where(fn($q) => $q->whereIn('id', $data['products']));
            $products = Product::whereIn('id',$data['products'])->pluck('name');
            foreach ($products as $product) {
                $dataset[] = 'Stock Item ' . $product;
            }
        } else {
            $dataset[] = 'All Stock Items';
        }

        if (!empty($data['warehouses'])) {
//            $query->whereHas('stocks',fn($q)=>$q->whereIn('warehouse_id',$data['warehouses']));
            $warehouses = Warehouse::whereIn('id',$data['warehouses'])->pluck('name');
            foreach ($warehouses as $warehouse) {
                $dataset[] = 'Warehouse ' . $warehouse ;
            }
        } else {
            $dataset[] = 'All Warehouses';
        }




        $dataset[] = 'To Date ' . Carbon::parse($data['date'])->format('Y/m/d');
//        $query->when($data['start_date'], function ($query) use ($data) {
//            $query->where('due', '>=', $data['start_date']);
//        })
//            $query->when($data['date'], function ($query) use ($data) {
//            $query->where('due', '<=', $data['date']);
//        });
        $select = [];
        $select[]=  'id';
        $select[]=  'name';

        if (isset($columns['avg_cost']) && $columns['avg_cost']) {
            $select[]=  'avg_cost';
        }

        if (isset($columns['s_price']) && $columns['s_price']) {
            $select[]=  's_price';
        }

//        $query->select(...$select);

        $query->orderBy('id');

        return ['rows' => $query->get(), 'dataset' => $dataset];
    }


    public function cost(array $data = null)
    {
        $columns = $data['columns'];

        $dataset = [];

        $query = Product::query();

        if (!empty($data['warehouses'])) {
            $query->with(['stocks' =>fn($q)=>$q->whereIn('warehouse_id',$data['warehouses'])]);
        }else{
            $query->with('stocks','category' );
        }

        $query->withSum('stocks as global_balance','balance' );

        if (isset($data['has_balance']) && $data['has_balance'] == 1){
            $query->whereHas('stocks',fn($q)=>$q->where('balance','>',0));
        }

        if (!empty($data['products'])) {
            $query->where(fn($q) => $q->whereIn('id', $data['products']));
            $products = Product::whereIn('id',$data['products'])->pluck('name');
            foreach ($products as $product) {
                $dataset[] = 'Stock Item ' . $product;
            }
        } else {
            $dataset[] = 'All Stock Items';
        }

        if (!empty($data['warehouses'])) {
//            $query->whereHas('stocks',fn($q)=>$q->whereIn('warehouse_id',$data['warehouses']));
            $warehouses = Warehouse::whereIn('id',$data['warehouses'])->pluck('name');
            foreach ($warehouses as $warehouse) {
                $dataset[] = 'Warehouse ' . $warehouse ;
            }
        } else {
            $dataset[] = 'All Warehouses';
        }




        $dataset[] = 'To Date ' . Carbon::parse($data['date'])->format('Y/m/d');
//        $query->when($data['start_date'], function ($query) use ($data) {
//            $query->where('due', '>=', $data['start_date']);
//        })
//            $query->when($data['date'], function ($query) use ($data) {
//            $query->where('due', '<=', $data['date']);
//        });
        $select = [];
        $select[]=  'id';
        $select[]=  'name';

        if (isset($columns['avg_cost']) && $columns['avg_cost']) {
            $select[]=  'avg_cost';
        }

        if (isset($columns['s_price']) && $columns['s_price']) {
            $select[]=  's_price';
        }

//        $query->select(...$select);

        $query->orderBy('id');

        return [$query->get(), $dataset];
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
