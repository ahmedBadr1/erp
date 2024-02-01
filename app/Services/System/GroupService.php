<?php

namespace App\Services\System;

use App\Exports\UsersExport;
use App\Models\System\Group;
use App\Services\ClientsExport;
use App\Services\MainService;
use Exception;
use Maatwebsite\Excel\Facades\Excel;

class GroupService extends MainService
{

    public function all($fields = null)
    {
        $data = $fields ?? (new Group())->getFillable();

        return Group::active()->get($data);
    }


    public function search($search)
    {
        $search = trim($search);
        return empty($search) ? Group::query()
            : Group::query()->where('name', 'like', '%' . $search . '%');
//                ->orWhereHas('currency', fn($q) => $q->where('name', 'like', '%' . $search . '%'))
//                ->orWhereHas('category', fn($q) => $q->where('name', 'like', '%' . $search . '%'));
    }

    public function store(array $data)
    {
        try {
            $Group = Group::create($data);
            return $Group;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function update($Group, array $data)
    {
        try {
            $Group->update($data);
            return $Group;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function destroy($Group)
    {
        if ($Group->users_count > 0) {
            return 0;
        } else {
            $Group->delete();
        }
    }

    public function export()
    {
        return Excel::download(new WarehouseExport, 'warehouse_'.date('d-m-Y').'.xlsx');
    }
}
