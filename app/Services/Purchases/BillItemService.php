<?php

namespace App\Services\Purchases;

use App\Exports\Inventory\ProductsExport;
use App\Exports\UsersExport;
use App\Models\Inventory\ItemHistory;
use App\Models\Purchases\BillItem;
use App\Models\Purchases\Supplier;
use App\Services\ClientsExport;
use App\Services\MainService;
use Exception;
use Maatwebsite\Excel\Facades\Excel;

class BillItemService extends MainService
{

    public function all($fields = null,$active=1)
    {
        $data = $fields ?? (new ItemHistory())->getFillable();

        return BillItem::active($active)->get($data);
    }


    public function search($search)
    {
        $search = trim($search);
        return empty($search) ? BillItem::query()
            : BillItem::query()->where('name', 'like', '%' . $search . '%')
                ->orWhere('sku', 'like', '%' . $search . '%')
                ->orWhereHas('product', fn($q) => $q->where('code', 'like', '%' . $search . '%'));
    }

    public function
    store($billId ,int $productId ,int $quantity,float $price,float $cost,float $tax_value,string $comment = null,int $userId = null,int $unitId = null,$expireAt = null,$invTransactionId = null)
    {
            return BillItem::create([
                'bill_id'=> $billId ,
                'product_id'=> $productId,
                'quantity'=> $quantity,
                'price'=> $price,
                'cost'=> $cost,
                'tax_value'=> $tax_value,
                'sub_total'=> $quantity * $price,
                'total'=> $tax_value + ($quantity * $price),
                'comment' => $comment,
                'unit_id' => $unitId,
                'expire_at' => $expireAt,
                'inv_transaction_id'=> $invTransactionId,
                'user_id' => $userId ?? auth()->id()
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
