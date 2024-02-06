<?php

namespace App\Services\Inventory;

use App\Exports\Inventory\ProductsExport;
use App\Exports\UsersExport;
use App\Models\Inventory\Item;
use App\Models\Inventory\Product;
use App\Models\Inventory\Warehouse;
use App\Services\ClientsExport;
use App\Services\MainService;
use Exception;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class ItemService extends MainService
{

    public function all($fields = null,$active=1)
    {
        $data = $fields ?? (new Item())->getFillable();

        return Item::active($active)->get($data);
    }


    public function search($search)
    {
        $search = trim($search);
        return empty($search) ? Item::query()
            : Item::query()->where('name', 'like', '%' . $search . '%')
                ->orWhere('sku', 'like', '%' . $search . '%')
                ->orWhereHas('product', fn($q) => $q->where('code', 'like', '%' . $search . '%'));
    }

    public function store(int $warehouseId,int $invTransactionId ,int $productId ,int $quantity,float$price,$second_party_id,$second_party_type,$in,$balance=null,string $avg_cost = null,int $userId = null,int $unitId = null, )
    {
            return Item::create([
                'warehouse_id' => $warehouseId,
                'inv_transaction_id' => $invTransactionId,
                'product_id'=> $productId,
                'quantity'=> $quantity,
                'price'=> $price,
                'avg_cost'=> $avg_cost,
                'second_party_id' => $second_party_id,
                'second_party_type' => $second_party_type,
                'balance' => $balance,
                'unit_id' => $unitId,
                'in' => $in,
            ]);
    }

    public function update($Supplier, array $data)
    {
        try {
            $Supplier->update($data);
            return $Supplier;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function destroy($Supplier)
    {
        if ($Supplier->items_count > 0) {
            return 0;
        } else {
            $Supplier->delete();
        }
    }

    public function cards(array $data = null)
    {
        $columns = $data['columns'];

        $dataset = [];

        $query = Item::query();

        $query->with('secondParty','transaction' );


        $query->where(fn($q)=>$q->where('product_id',$data['product'])->where('warehouse_id',$data['warehouse']));

        $dataset[] = __('Stock Item') .' '.  Warehouse::whereId($data['warehouse'])->value('name');
        $dataset[] = __('Warehouse') .' '.  Product::whereId($data['product'])->value('name');


        $dataset[] = __('Start Date'). ' ' . Carbon::parse($data['start_date'])->format('Y/m/d');
        $dataset[] = __('End Date'). ' ' . Carbon::parse($data['end_date'])->format('Y/m/d');

        $query->when($data['start_date'], function ($query) use ($data) {
            $query->where('created_at', '>=', $data['start_date']);
        });

            $query->when($data['end_date'], function ($query) use ($data) {
            $query->where('created_at', '<=', $data['end_date']);
        });

//        $query->select(...$select);

        $query->orderBy('created_at');

        return [$query->get(), $dataset];
    }

    public function export($collection =null)
    {
        return Excel::download(new ProductsExport($collection), 'products_'.date('d-m-Y').'.xlsx');
    }
}
