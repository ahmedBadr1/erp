<?php

namespace App\Services\Accounting;

use App\Enums\TransactionTypeGroups;
use App\Exports\UsersExport;
use App\Models\Accounting\Transaction;
use App\Services\ClientsExport;
use App\Services\MainService;
use Exception;
use Maatwebsite\Excel\Facades\Excel;

class TransactionService extends MainService
{

    public function all($fields = null, $type = null)
    {
        $data = $fields ?? (new Transaction())->getFillable();

        $query = Transaction::query();
        if ($type) {
            $query->where('type', $type);
        }

        return $query->get($data);
    }

    public function types()
    {
        $types = [
            'CI' => 'Cash In',
            'CO' => 'Cash Out',
            'JE' => 'Journal Entry',
            'PI' => 'Purchase Invoice',
            'SI' => 'Sales Invoice',
            'NP' => 'Credit Note',
            'NR' => 'Debit Note',
        ];
        return $types ?? Transaction::$TYPES;
    }

    public function cash(array $data = null)
    {
        $columns = $data['columns'];

        $accounts = $data['accounts'] ?? [];

        $query = Transaction::query();
        $query->locked(0);
        if ($data['posting'] === 'posted') {
            $query->posted(1);
        } elseif ($data['posting'] === 'unposted') {
            $query->posted(0);
        }

        if (isset($data['cashin']) && $data['cashin'] && $data['cashout']) {
            $query->whereIn('type', ['CI', "CO"]);
        } else if (isset($data['cashin']) && $data['cashin']) {
            $query->where('type', 'CI');
        } else if (isset($data['cashout']) && $data['cashout']) {
            $query->where('type', 'CO');
        }

        if (isset($data['code']) && $data['code']) {
            $query->where('code', $data['code']);
        }

        if (isset($columns['responsible']) && $columns['responsible']) {
            $query->with('responsible');
        }

        if (isset($data['treasuries']) && $data['treasuries']) {
            $query->whereIn('first_party_id', $data['treasuries']);
        }

        if (isset($data['partners']) && $data['partners']) {
            $query->whereIn('second_party_id', $data['partners']);
        }

        if (isset($data['partners']) && $data['partners']) {
            $query->whereIn('second_party_id', $data['partners']);
        }


//        if (isset($data['treasuries']) && $data['treasuries']) {
        $query->with('firstParty');
//        }


        if (isset($data['sellers']) && $data['sellers']) {
            $query->whereIn('responsible_id', $data['sellers']);
        }

        if (isset($columns['edited_by']) && $columns['edited_by']) {
            $query->with('editor');
        }

        if (isset($columns['second_party']) && $columns['second_party']) {
            $query->with('secondParty');
        }


//        if (isset($data['costCenters'])) {
//            $query->whereHas('entries', fn($q) => $q->whereIn('cost_center_id', $data['costCenters']));
//        }

        $query->when($data['start_date'], function ($query) use ($data) {
            $query->where('due', '>=', $data['start_date']);
        })->when($data['end_date'], function ($query) use ($data) {
            $query->where('due', '<=', $data['end_date']);
        });

//            ->orderBy($data->get('OrderBy') ?? $this->orderBy, $data->get('OrderBy') ?? $this->orderDesc ? 'desc' : 'asc');
        $query->orderBy('due');
        return $query->get();
    }

    public function search($search)
    {
        $search = trim($search);
        return empty($search) ? Transaction::query()
            : Transaction::query()->where('amount', 'like', '%' . $search . '%')
                ->orWhere('note', 'like', '%' . $search . '%')
                ->orWhere('type', 'like', '%' . $search . '%')
//                ->orWhereHas('entries', fn($q) => $q->where('description', 'like', '%' . $search . '%'))
//                ->orWhereHas('transaction', fn($q) => $q->where('type', 'like', '%' . $search . '%'))
                ->orWhereHas('entries.account', fn($q) => $q->where('name', 'like', '%' . $search . '%'));
    }

    public function createTransaction(string $type, int $groupId, float $amount,
                                      int $ledger_id = null, int $first_party_id, int $second_party_id, $due = null, string $note = null, $user_id = null, $paper_ref = null,
                                      int $currency_id = null ,float $ex_rate  = null ,float $currency_total  = null, $system = 1)
    {

        return Transaction::create([
            'group_id' => $groupId,
            'type' => $type,
            'amount' => $amount,
            'ledger_id' => $ledger_id,
            'first_party_id' => $first_party_id,
            'second_party_id' => $second_party_id,
            'note' => $note ?? null,
            'due' => $due ?? null,
            'paper_ref' => $paper_ref ?? null,
            'currency_id' => $currency_id ?? 1,
            'ex_rate' => $ex_rate ?? 1.00,
            'currency_total' => $currency_total ??$amount ,
            'responsible' => $user_id ?? auth()->id(),
            'created_by' => auth()->id(),
            'system' => $system,
        ]);
    }

    public function createCI(int $groupId, float $amount, int $ledger_id, int $first_party_id, int $second_party_id, $due = null, string $note = null, $user_id = null, $paper_ref = null, $system = 1)
    {
        return $this->createTransaction( 'CI', $groupId, $amount, $ledger_id, $first_party_id, $second_party_id, $due, $note, $user_id, $paper_ref, $system);
    }

    public function createCO(int $groupId, float $amount, int $ledger_id, int $first_party_id, int $second_party_id, $due = null, string $note = null, $user_id = null, $paper_ref = null, $system = 1)
    {
        return $this->createTransaction( 'CO', $groupId, $amount, $ledger_id, $first_party_id, $second_party_id, $due, $note, $user_id, $paper_ref, $system);
    }

    public function createSO(int $groupId, float $amount, int $ledger_id, int $first_party_id, int $second_party_id, $due = null, string $note = null, $user_id = null, $paper_ref = null, $system = 1)
    {
        return $this->createTransaction('SO', $groupId, $amount, $ledger_id, $first_party_id, $second_party_id, $due, $note, $user_id, $paper_ref, $system);
    }

    public function createPI(int $groupId, float $amount, int $ledger_id, int $first_party_id, int $second_party_id, $due = null, string $note = null, $user_id = null, $paper_ref = null, $system = 1)
    {
        return $this->createTransaction( 'PI', $groupId, $amount, $ledger_id, $first_party_id, $second_party_id, $due, $note, $user_id, $paper_ref, $system);
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
