<?php

namespace App\Services\Inventory;

use App\Exports\Inventory\ProductsExport;
use App\Exports\UsersExport;
use App\Models\Inventory\Item;
use App\Models\Inventory\Product;
use App\Models\Inventory\Stock;
use App\Models\Inventory\Warehouse;
use App\Services\ClientsExport;
use App\Services\MainService;
use Exception;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class StockService extends MainService
{

    public function all($fields = null,$active=1)
    {
        $data = $fields ?? (new Stock())->getFillable();

        return Stock::get($data);
    }

    public function store(int $warehouseId,int $productId ,int $balance)
    {
            return Stock::create([
                'warehouse_id' => $warehouseId,
                'product_id'=> $productId,
                'balance' => $balance,
            ]);
    }


    public function warehouses(array $data = null)
    {
        $columns = $data['columns'];

        $dataset = [];

        $query = Stock::query();

        $query->with('product','warehouse');

        if (isset($data['has_balance']) && $data['has_balance']){
            $query->where('balance','>',0);
        }

        if (!empty($data['products'])) {
            $query->where(fn($q) => $q->whereIn('product_id', $data['products']));
            $products = Product::whereIn('id',$data['products'])->pluck('name');
            foreach ($products as $product) {
                $dataset[] = 'Stock Item ' . $product;
            }
        } else {
            $dataset[] = 'All Stock Items';
        }

        if (!empty($data['warehouses'])) {
            $query->where(fn($q) => $q->whereIn('warehouse_id', $data['warehouses']));
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
        $query->orderBy('product_id');

        return ['rows' => $query->get(), 'dataset' => $dataset];
    }

    public function export($collection =null)
    {
        return Excel::download(new ProductsExport($collection), 'products_'.date('d-m-Y').'.xlsx');
    }
}
