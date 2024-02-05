<?php

namespace App\Services\Inventory;

use App\Exports\Inventory\ProductsExport;
use App\Exports\UsersExport;
use App\Models\Inventory\InvTransactionItem;
use App\Models\Inventory\Item;
use App\Services\ClientsExport;
use App\Services\MainService;
use Exception;
use Maatwebsite\Excel\Facades\Excel;

class InvTransactionItemService extends MainService
{

    public function all($fields = null,$active=1)
    {
        $data = $fields ?? (new InvTransactionItem())->getFillable();

        return InvTransactionItem::active($active)->get($data);
    }


    public function search($search)
    {
        $search = trim($search);
        return empty($search) ? InvTransactionItem::query()
            : InvTransactionItem::query()->where('name', 'like', '%' . $search . '%')
                ->orWhere('sku', 'like', '%' . $search . '%')
                ->orWhereHas('product', fn($q) => $q->where('code', 'like', '%' . $search . '%'));
    }

    public function store(int $warehouseId,int $invTransactionId ,int $productId ,int $quantity,float$price,$accepted = 0,int $unitId = null)
    {
            return InvTransactionItem::create([
                'inv_transaction_id' => $invTransactionId,
                'product_id'=> $productId,
                'quantity'=> $quantity,
                'price'=> $price,
                'unit_id' => $unitId,
                'accepted' => $accepted,
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
