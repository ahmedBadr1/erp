<?php

namespace App\Services\Accounting;

use App\Exports\UsersExport;
use App\Models\Accounting\Account;
use App\Models\Accounting\Entry;
use App\Models\Crm\Client;
use App\Models\User;
use App\Services\ClientsExport;
use App\Services\MainService;
use Exception;
use Maatwebsite\Excel\Facades\Excel;

class AccountService extends MainService
{

    public function fetchAll()
    {
        return Account::get();
    }


    public function search($search)
    {
        $search = trim($search);
        return empty($search) ? Account::query()
            : Account::query()->where('name', 'like', '%' . $search . '%')
                ->orWhere('code', 'like', '%' . $search . '%')
                ->orWhere('description', 'like', '%' . $search . '%')
                ->orWhereHas('currency', fn($q) => $q->where('name', 'like', '%' . $search . '%'))
                ->orWhereHas('category', fn($q) => $q->where('name', 'like', '%' . $search . '%'));
    }

    public function store(array $data)
    {
        try {
            $account = Account::create($data);
            return $account;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function update($account, array $data)
    {
        try {
            $account->update($data);
            return $account;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function destroy($account)
    {
        if ($account->projects_count > 0) {
            return 0;
        } else {
            $account->delete();
        }
    }

    public function export()
    {
        return Excel::download(new AccountsExport, 'accounts_'.date('d-m-Y').'.xlsx');
    }
}
