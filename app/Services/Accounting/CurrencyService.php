<?php

namespace App\Services\Accounting;

use App\Exports\UsersExport;
use App\Models\Accounting\Account;
use App\Models\Accounting\Currency;
use App\Models\Accounting\Entry;
use App\Models\Crm\Client;
use App\Models\User;
use App\Services\ClientsExport;
use App\Services\MainService;
use Exception;
use Maatwebsite\Excel\Facades\Excel;

class CurrencyService extends MainService
{

    public function fetchAll()
    {
        return Currency::get();
    }


    public function search($search)
    {
        $search = trim($search);
        return empty($search) ? Currency::query()
            : Currency::query()->where('name', 'like', '%' . $search . '%')
                ->orWhere('symbol', 'like', '%' . $search . '%')
                ->orWhere('code', 'like', '%' . $search . '%')
                ->orWhere('ex_rate', 'like', '%' . $search . '%')
                ->orWhereHas('gainAccount', fn($q) => $q->where('name', 'like', '%' . $search . '%'))
                ->orWhereHas('lossAccount', fn($q) => $q->where('name', 'like', '%' . $search . '%'));
    }

    public function store(array $data)
    {
        try {
            $currency = Currency::create($data);
            return $currency;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function update($currency, array $data)
    {
        try {
            $currency->update($data);
            return $currency;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function destroy($account)
    {
        if ($currency->projects_count > 0) {
            return 0;
        } else {
            $currency->delete();
        }
    }

    public function export()
    {
        return Excel::download(new CurrenciesExport, 'currencies_' . date('d-m-Y') . '.xlsx');
    }
}
