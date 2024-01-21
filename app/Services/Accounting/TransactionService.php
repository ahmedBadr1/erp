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

    public function createTransaction(string $type, int $amount, int $ledger_id,int $first_party_id, int $second_party_id, $due = null, string $description = null, $user_id = null,$je_code= null,$document_no = null, $system = 1)
    {

        return Transaction::create([
            'type' => $type,
            'amount' => $amount,
            'ledger_id' => $ledger_id,
            'first_party_id' => $first_party_id,
            'second_party_id' => $second_party_id,
            'description' => $description,
            'due' => $due,
            'je_code' => $je_code,
            'document_no' => $document_no,
            'responsible' => $user_id ?? auth()->id(),
            'created_by' =>  auth()->id(),
            'system' => $system,
        ]);
    }

    public function createCI(int $amount, int $ledger_id, int $first_party_id, int $second_party_id, $due = null, string $description = null, $user_id = null,$je_code= null,$document_no = null, $system = 1)
    {
        return $this->createTransaction('CI', $amount, $ledger_id, $first_party_id,  $second_party_id, $due, $description, $user_id,$je_code,$document_no, $system);
    }

    public function createCO(int $amount, int $ledger_id,int $first_party_id, int $second_party_id, $due = null, string $description = null, $user_id = null,$je_code= null,$document_no = null, $system = 1)
    {
        return $this->createTransaction('CO', $amount, $ledger_id, $first_party_id,  $second_party_id, $due, $description, $user_id, $je_code,$document_no, $system);
    }

    public function createSO(int $amount, int $ledger_id, int $first_party_id, int $second_party_id, $due = null, string $description = null, $user_id = null,$je_code= null,$document_no = null, $system = 1)
    {
        return $this->createTransaction('SO', $amount, $ledger_id,  $first_party_id,  $second_party_id, $due, $description, $user_id, $je_code,$document_no, $system);
    }

    public function update($transaction, array $data)
    {
        try {
            $transaction->update([...$data,
                'edited_by' => auth()->id(),
                ]);
            return $transaction;
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
        return Excel::download(new ClientsExport, 'clients_' . date('d-m-Y') . '.xlsx');
    }
}
