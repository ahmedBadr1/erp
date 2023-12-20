<?php

namespace App\Services\Accounting;

use App\Exports\UsersExport;
use App\Models\Accounting\Entry;
use App\Models\Accounting\Transaction;
use App\Models\Crm\Client;
use App\Models\User;
use App\Services\ClientsExport;
use App\Services\MainService;
use Exception;
use Maatwebsite\Excel\Facades\Excel;

class TransactionService extends MainService
{

    public function fetchAll()
    {
        return Transaction::get();
    }


    public function search($search)
    {
        $search = trim($search);
        return empty($search) ? Transaction::query()
            : Transaction::query()->where('amount', 'like', '%' . $search . '%')
                ->orWhere('description', 'like', '%' . $search . '%')
                ->orWhere('type', 'like', '%' . $search . '%')
//                ->orWhereHas('entries', fn($q) => $q->where('description', 'like', '%' . $search . '%'))
//                ->orWhereHas('transaction', fn($q) => $q->where('type', 'like', '%' . $search . '%'))
                ->orWhereHas('entries.account', fn($q) => $q->where('name', 'like', '%' . $search . '%'));
    }

    public function store(array $data)
    {
        try {
            $client = Transaction::create($data);
            return $client;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function update($client, array $data)
    {
        try {
            $client->update($data);
            return $client;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function destroy($client)
    {
        if ($client->projects_count > 0) {
            return 0;
        } else {
            $client->delete();
        }
    }

    public function export()
    {
        return Excel::download(new ClientsExport, 'clients_'.date('d-m-Y').'.xlsx');
    }
}