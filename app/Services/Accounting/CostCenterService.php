<?php

namespace App\Services\Accounting;

use App\Exports\UsersExport;
use App\Models\Accounting\Account;
use App\Models\Accounting\CostCenter;
use App\Models\Accounting\Entry;
use App\Models\Crm\Client;
use App\Models\User;
use App\Services\ClientsExport;
use App\Services\MainService;
use Exception;
use Maatwebsite\Excel\Facades\Excel;

class CostCenterService extends MainService
{

    public function all($fields = null)
    {
        $data = $fields ?? (new CostCenter())->getFillable();

        return CostCenter::active()->get($data);
    }

    public function search($search)
    {
        $search = trim($search);
        return empty($search) ? CostCenter::query()
            : CostCenter::query()->where('name', 'like', '%' . $search . '%')
                ->orWhere('code', 'like', '%' . $search . '%')
                ->orWhereHas('parent', fn($q) => $q->where('name', 'like', '%' . $search . '%'));
    }

    public function store(array $data)
    {
        try {
            $costCenter = CostCenter::create($data);
            return $costCenter;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function update($costCenter, array $data)
    {
        try {
            $costCenter->update($data);
            return $costCenter;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function destroy($costCenter)
    {
        if ($costCenter->transaction_count > 0) {
            return 0;
        } else {
            $costCenter->delete();
        }
    }

    public function export()
    {
        return Excel::download(new CostCenterExport, 'cost_centers_'.date('d-m-Y').'.xlsx');
    }
}
