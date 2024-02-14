<?php

namespace App\Services\Inventory;

use App\Exports\UsersExport;
use App\Models\Inventory\Warehouse;
use App\Services\Accounting\AccountService;
use App\Services\ClientsExport;
use App\Services\MainService;
use App\Services\System\AddressService;
use App\Services\System\ContactService;
use App\Services\System\ContractService;
use App\Services\System\TagService;
use Exception;
use Maatwebsite\Excel\Facades\Excel;

class WarehouseService extends MainService
{

    public function all($fields = null,$active = 1)
    {
        $data = $fields ?? (new Warehouse())->getFillable();

        return Warehouse::active($active)->get($data);
    }


    public function search($search)
    {
        $search = trim($search);
        return empty($search) ? Warehouse::query()
            : Warehouse::query()->where('name', 'like', '%' . $search . '%')
                ->orWhere('type', 'like', '%' . $search . '%')
                ->orWhere('address', 'like', '%' . $search . '%');
//                ->orWhereHas('currency', fn($q) => $q->where('name', 'like', '%' . $search . '%'))
//                ->orWhereHas('category', fn($q) => $q->where('name', 'like', '%' . $search . '%'));
    }

    public function store(array $data)
    {
        $nodeId = 19 ; // المخزون
        $account = (new AccountService())->store([
            'name' => $data['name'],
            'description' => $data['description'],
            'node_id' => $nodeId,
        ]);
        $data['account_id'] = $account->id ;
        $warehouse = Warehouse::create($data);

        if (isset($data['contact'])) {
            (new ContactService())->store($data['contact'], $warehouse->id, 'warehouse');
        }

        if (isset($data['address'])) {
            (new AddressService())->store($data['address'], $warehouse->id, 'warehouse');
        }
        if (isset($data['tags'])) {
            (new TagService())->sync($data['tags'], $warehouse, 'warehouse');
        }

        if (isset($data['contract'])) {
            (new ContractService())->store($data['contract'], $warehouse->id, 'warehouse');
        }

        return true ;
    }

    public function update($warehouse, array $data)
    {
        try {
            $warehouse->update($data);
            return $warehouse;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function destroy($warehouse)
    {
        if ($warehouse->items_count > 0) {
            return 0;
        } else {
            $warehouse->delete();
        }
    }

    public function export()
    {
        return Excel::download(new WarehouseExport, 'warehouse_'.date('d-m-Y').'.xlsx');
    }
}
