<?php

namespace App\Services\Accounting;

use App\Exports\Inventory\ProductsExport;
use App\Exports\UsersExport;
use App\Models\Accounting\TransactionGroup;
use App\Models\Hr\Branch;
use App\Services\ClientsExport;
use App\Services\MainService;
use Exception;
use Maatwebsite\Excel\Facades\Excel;

class TransactionGroupService extends MainService
{

    public function all($fields = null,$active=1)
    {
        $data = $fields ?? (new TransactionGroup())->getFillable();

        return TransactionGroup::active($active)->get($data);
    }


    public function search($search)
    {
        $search = trim($search);
        return empty($search) ? TransactionGroup::query()
            : TransactionGroup::query()->where('name', 'like', '%' . $search . '%')
                ->orWhere('business_name', 'like', '%' . $search . '%')
                ->orWhere('code', 'like', '%' . $search . '%');
//                ->orWhereHas('category', fn($q) => $q->where('name', 'like', '%' . $search . '%'));
    }

    public function store(array $data = [] )
    {
        try {
            $group = TransactionGroup::create([...$data,'created_by'=>auth()->id()]);
            return $group;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function update($Branch, array $data)
    {
        try {
            $Branch->update($data);
            return $Branch;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function destroy($Branch)
    {
        if ($Branch->items_count > 0) {
            return 0;
        } else {
            $Branch->delete();
        }
    }

    public function export($collection =null)
    {
        return Excel::download(new ProductsExport($collection), 'products_'.date('d-m-Y').'.xlsx');
    }
}
