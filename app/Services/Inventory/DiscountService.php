<?php

namespace App\Services\Inventory;

use App\Exports\Inventory\ProductsExport;
use App\Exports\UsersExport;
use App\Models\Inventory\Brand;
use App\Models\Inventory\Discount;
use App\Services\ClientsExport;
use App\Services\MainService;
use Exception;
use Maatwebsite\Excel\Facades\Excel;

class DiscountService extends MainService
{

    public function all($fields = null,$active=1)
    {
        $data = $fields ?? (new Discount())->getFillable();

        return Discount::active($active)->get($data);
    }


    public function search($search)
    {
        $search = trim($search);
        return empty($search) ? Discount::query()
            : Discount::query()->where('name', 'like', '%' . $search . '%');

    }

    public function store($discountable_type,$discountable_id,$type ,$amount,$is_value,$limited,$from,$to)
    {
//        ['type','amount', 'is_value', 'from', 'to', 'limited','discountable'];

        return Discount::create([
                'type' => $type ?? null,
            'amount' => $amount,
            'is_value' => $is_value,
            'limited' => $limited,
            'from' => $from ?? null,
            'to' => $to ?? null,
            'discountable_type' =>  $discountable_type,
            'discountable_id' =>  $discountable_id,
        ]);

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
