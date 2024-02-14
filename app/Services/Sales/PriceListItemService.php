<?php

namespace App\Services\Sales;

use App\Exports\Inventory\ProductsExport;
use App\Exports\UsersExport;
use App\Models\Sales\PriceList;
use App\Models\Sales\PriceListItem;
use App\Services\ClientsExport;
use App\Services\MainService;
use Exception;
use Maatwebsite\Excel\Facades\Excel;

class PriceListItemService extends MainService
{

    public function all($fields = null,$active=1)
    {
        $data = $fields ?? (new PriceListItem())->getFillable();

        return PriceListItem::active($active)->get($data);
    }


    public function search($search)
    {
        $search = trim($search);
        return empty($search) ? PriceListItem::query()
            : PriceListItem::query()->where('name', 'like', '%' . $search . '%');

    }

    public function store($discountable_type,$discountable_id,$type ,$amount,$is_value,$limited,$from,$to)
    {
//        ['type','amount', 'is_value', 'from', 'to', 'limited','discountable'];

        return PriceListItem::create([
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

    public function update($PriceList, array $data)
    {
        try {
            $PriceList->update($data);
            return $PriceList;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function destroy($PriceList)
    {
        if ($PriceList->items_count > 0) {
            return 0;
        } else {
            $PriceList->delete();
        }
    }

    public function export($collection =null)
    {
        return Excel::download(new ProductsExport($collection), 'products_'.date('d-m-Y').'.xlsx');
    }
}
