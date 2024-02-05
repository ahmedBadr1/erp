<?php

namespace App\Services\Inventory;

use App\Exports\Inventory\ProductsExport;
use App\Exports\UsersExport;
use App\Models\Inventory\Item;
use App\Services\ClientsExport;
use App\Services\MainService;
use Exception;
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

    public function export($collection =null)
    {
        return Excel::download(new ProductsExport($collection), 'products_'.date('d-m-Y').'.xlsx');
    }
}
