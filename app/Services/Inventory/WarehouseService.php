<?php

namespace App\Services\Inventory;

use App\Exports\UsersExport;
use App\Models\Inventory\Warehouse;
use App\Services\ClientsExport;
use App\Services\MainService;
use App\Services\System\AddressService;
use App\Services\System\ContactService;
use App\Services\System\ContractService;
use App\Services\System\TagService;
use Maatwebsite\Excel\Facades\Excel;

class WarehouseService extends MainService
{

    public function all($fields = null, $active = 1)
    {
        $data = $fields ?? (new Warehouse())->getFillable();
        $query = Warehouse::query();
        if (is_int($active)) {
            $query->active($active);
        }
        return $query->get($data);
    }

    public function query()
    {
        return Warehouse::query();
    }

    public function search($search)
    {
        $search = trim($search);
        return empty($search) ? $this->query()
            : $this->query()->where('name', 'like', '%' . $search . '%')
                ->orWhere('type', 'like', '%' . $search . '%')
                ->orWhere('address', 'like', '%' . $search . '%');
//                ->orWhereHas('currency', fn($q) => $q->where('name', 'like', '%' . $search . '%'))
//                ->orWhereHas('category', fn($q) => $q->where('name', 'like', '%' . $search . '%'));
    }

    public function store(array $data)
    {
        $nodeId = 19; // المخزون
//        $account = (new AccountService())->store([
//            'name' => $data['name'],
//            'description' => $data['description'],
//            'node_id' => $nodeId,
//        ]);
//        $data['account_id'] = $account->id ;
        //        $warehouse = Warehouse::where('account_id', $account->id)->first();
//        $warehouse->update($data);
        $warehouse = Warehouse::create($data); // already Warehouse Created IN Account


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

        return true;
    }

    public function update($warehouseId, array $data)
    {
        $warehouse = Warehouse::find($warehouseId);
        return $warehouse->update($data);
    }

    public function destroy($warehouse)
    {
        if ($warehouse->items_count > 0) {
            return 0;
        } else {
            $warehouse->delete();
        }
    }

    public function check(Warehouse $warehouse)
    {
        if ($warehouse->active) {
            if (empty($warehouse->account_id) || empty($warehouse->cog_account_id) || empty($warehouse->s_account_id)) {
                $warehouse->updateQuietly(['active' => false]);
            }
        } else if (isset($warehouse->account_id, $warehouse->cog_account_id, $warehouse->s_account_id)) {
            $warehouse->updateQuietly(['active' => true]);
        }
    }

    public function export()
    {
        return Excel::download(new WarehouseExport, 'warehouse_' . date('d-m-Y') . '.xlsx');
    }
}
