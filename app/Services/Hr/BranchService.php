<?php

namespace App\Services\Hr;

use App\Exports\Inventory\ProductsExport;
use App\Exports\UsersExport;
use App\Models\Hr\Branch;
use App\Models\Purchases\Supplier;
use App\Services\ClientsExport;
use App\Services\MainService;
use Exception;
use Maatwebsite\Excel\Facades\Excel;

class BranchService extends MainService
{

    public function all($fields = null,$active=1)
    {
        $data = $fields ?? (new Branch())->getFillable();

        return Branch::active($active)->get($data);
    }


    public function search($search)
    {
        $search = trim($search);
        return empty($search) ? Branch::query()
            : Branch::query()->where('name', 'like', '%' . $search . '%')
                ->orWhere('business_name', 'like', '%' . $search . '%')
                ->orWhere('code', 'like', '%' . $search . '%');
//                ->orWhereHas('category', fn($q) => $q->where('name', 'like', '%' . $search . '%'));
    }

    public function store(array $data)
    {
        try {
            $Branch = Branch::create($data);
            return $Branch;
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
