<?php

namespace App\Services\System;

use App\Exports\Inventory\ProductsExport;
use App\Exports\UsersExport;
use App\Models\Accounting\Entry;
use App\Models\Crm\Client;
use App\Models\Inventory\Product;
use App\Models\System\Access;
use App\Models\System\Address;
use App\Models\User;
use App\Services\ClientsExport;
use App\Services\MainService;
use Exception;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class AccessService extends MainService
{

    public function all($fields = null)
    {
        $data = $fields ?? (new Address())->getFillable();

        return Access::active()->get($data);
    }


    public function search($search)
    {
        $search = trim($search);
        return empty($search) ? Access::query()
            : Access::query()->where('name', 'like', '%' . $search . '%');
//                ->orWhereHas('account', fn($q) => $q->where('name', 'like', '%' . $search . '%'));
    }

    public function sync(array $authModels, $authType, $model, $model_type)
    {
        try {
            $model->accesses()->where('auth_type', $authType)->whereNotIn('auth_id', $authModels)->delete();
            foreach ($authModels as $auth) {
                $model->accesses()->firstOrCreate([
                    'auth_id' => $auth,
                    'auth_type' => $authType
                ]);
            }
            return true;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function update($Address, array $data)
    {
        try {
            $Address->update($data);
            return $Address;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function destroy($Address)
    {
        if ($Address->items_count > 0) {
            return 0;
        } else {
            $Address->delete();
        }
    }

    public function export($collection = null)
    {
        return Excel::download(new ProductsExport($collection), 'products_' . date('d-m-Y') . '.xlsx');
    }
}
