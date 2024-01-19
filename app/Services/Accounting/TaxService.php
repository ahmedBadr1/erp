<?php

namespace App\Services\Accounting;

use App\Exports\UsersExport;
use App\Models\Accounting\Account;
use App\Models\Accounting\Entry;
use App\Models\Accounting\Tax;
use App\Models\Crm\Client;
use App\Models\User;
use App\Services\ClientsExport;
use App\Services\MainService;
use Exception;
use Maatwebsite\Excel\Facades\Excel;

class TaxService extends MainService
{

    public function all($fields = null)
    {
        $data = $fields ?? (new Tax())->getFillable();

        return Tax::active()->get($data);
    }


    public function search($search)
    {
        $search = trim($search);
        return empty($search) ? Tax::query()
            : Tax::query()->where('name', 'like', '%' . $search . '%')
                ->orWhere('rate', 'like', '%' . $search . '%')
                ->orWhere('code', 'like', '%' . $search . '%')
//                ->orWhereJsonContains('code',  $search )
                ->orWhereHas('account', fn($q) => $q->where('name', 'like', '%' . $search . '%'));
    }

    public function store(array $data)
    {
        try {
            $Tax = Tax::create($data);
            return $Tax;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function update($Tax, array $data)
    {
        try {
            $Tax->update($data);
            return $Tax;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function destroy($Tax)
    {
        if ($Tax->projects_count > 0) {
            return 0;
        } else {
            $Tax->delete();
        }
    }

    public function export()
    {
        return Excel::download(new TaxesExport, 'taxes_' . date('d-m-Y') . '.xlsx');
    }
}
