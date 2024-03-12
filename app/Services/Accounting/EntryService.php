<?php

namespace App\Services\Accounting;

use App\Exports\UsersExport;
use App\Models\Accounting\Account;
use App\Models\Accounting\Entry;
use App\Services\ClientsExport;
use App\Services\MainService;
use Exception;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class EntryService extends MainService
{

    public function fetchAll()
    {
        return Entry::get();
    }


    public function search($search)
    {
        $search = trim($search);
        return empty($search) ? Entry::query()
            : Entry::query()->where('amount', 'like', '%' . $search . '%')
                ->orWhereHas('transaction', fn($q) => $q->where('description', 'like', '%' . $search . '%'))
                ->orWhereHas('transaction', fn($q) => $q->where('type', 'like', '%' . $search . '%'))
                ->orWhereHas('account', fn($q) => $q->where('name', 'like', '%' . $search . '%'));
    }

    public function createEntry(int $credit, float $amount, int $account_id, int $ledger_id, int $cost_center_id = null, string $comment = null)
    {
        return Entry::create([
            'credit' => $credit,
            'amount' => $amount,
            'account_id' => $account_id,
            'ledger_id' => $ledger_id,
            'cost_center_id' => $cost_center_id,
            'comment' => $comment ?? null,

        ]);
    }

    public function createCreditEntry(float $amount, int $account_id, int $ledger_id, int $cost_center_id = null, string $comment = null)
    {
        return $this->createEntry(1, $amount, $account_id, $ledger_id, $cost_center_id, $comment);
    }

    public function createDebitEntry(float $amount, $account_id, int $ledger_id, $cost_center_id = null, string $comment = null)
    {
        return $this->createEntry(0, $amount, $account_id, $ledger_id, $cost_center_id, $comment);
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

    public function accounts(array $data = null)
    {
        $columns = $data['columns'];
        $dataset = [];


        $accounts = $data['accounts'] ?? [];

//        if (isset($data['node'])) {
//            $node = Node::active()->with(['descendants' => fn($q) => $q->with('accounts'), 'accounts'])->where('id', $data['node'])->first();
//            $nodeAccounts = $node->account?->pluck('id');
//            $nodeDescendants = $node->descendants?->pluck('id');
//            if ($nodeAccounts) {
//                array_push($accounts, ...$nodeAccounts);
//            }
//            if ($nodeDescendants) {
//                array_push($accounts, ...$nodeDescendants);
//            }
//        }
        if ($data['related_accounts']) {
            foreach ($data['accounts'] as $account) {
                $group_account_id = Account::whereId($account)->value('account_group_id');
                if ($group_account_id) {
                    $related = Account::where('account_group_id', $group_account_id)->whereNotIn('id', $accounts)->pluck('id')->toArray();
                    array_push($accounts, ...$related);
                }
            }
        }

        $query = Entry::query();
        $query->locked(0);
        if ($data['posting'] === 'posted') {
            $query->posted(1);
        } elseif ($data['posting'] === 'unposted') {
            $query->posted(0);
        }
        $query->with('account', 'costCenter');


        if (isset($data['with_transactions']) && $data['with_transactions']) {
            $query->with('ledger.transaction');
        }

        if (isset($columns['second_party']) && $columns['second_party']) {
            $query->with('ledger.transaction.secondParty');
        }

        if (isset($data['detailed']) && $data['detailed']) {
            $query->with('ledger.entries');
        }

        if (isset($columns['created_by']) && $columns['created_by']) {
            $query->with('ledger.creator');
        }
        if (isset($columns['responsible']) && $columns['responsible']) {
            $query->with('ledger.responsible');
        }
        if (isset($columns['edited_by']) && $columns['edited_by']) {
            $query->with('ledger.editor');
        }

        if (isset($columns['document_currency']) && $columns['document_currency']) {
//            $query->with('ledger.transactions');
        }

        if (isset($columns['document_currency']) && $columns['document_currency']) {
//            $query->with('ledger.firstTransaction.currency');
        }

        $query->whereIntegerInRaw('account_id', $accounts);


        $accounts = Account::whereIn('id', $accounts)
//            ->withSum(['entries as credit_sum' => function ($query) {
//            $query->select(DB::raw('SUM(amount)'))
//                ->where('credit', true);
//        }], 'amount')
//            ->withSum(['entries as debit_sum' => function ($query) {
//                $query->select(DB::raw('SUM(amount)'))
//                    ->where('credit', false);
//            }], 'amount')
            ->withSum(['entries as period_credit_sum' => function ($query) use($data) {
                $query->select(DB::raw('SUM(amount)'))
                    ->where('created_at', '>=', $data['start_date'])
                    ->where('created_at', '<=', $data['end_date'])
                    ->where('credit', true);
            }], 'amount')
            ->withSum(['entries as period_debit_sum' => function ($query)  use($data) {
                $query->select(DB::raw('SUM(amount)'))
                    ->where('created_at', '>=', $data['start_date'])
                    ->where('created_at', '<=', $data['end_date'])
                    ->where('credit', false)
                ;
            }], 'amount')
            ->get();

        foreach ($accounts as $acc) {
            $dataset[] = "Account " . $acc->name . ' (' . $acc->code . ')';
        }


        if (isset($data['costCenters'])) {
//            $query->whereIn('cost_center_id', $data['costCenters']);
        }

        if (isset($data['currency']) && $data['currency']) {
//            $query->where('ledger.firstTransaction.currency_id', $data['currency']);
        }

        $query->when(isset($data['start_date']), function ($query) use ($data) {
            $query->where('created_at', '>=', $data['start_date']);
        })
            ->when(isset($data['end_date']), function ($query) use ($data) {
                $query->where('created_at', '<=', $data['end_date']);
            });
//        $query->withSum(['entries as credit_sum' => function ($query) {
//                $query->where('credit', true);
//            }], 'amount')
//            ->withSum(['entries as debit_sum' => function ($query) {
//                $query->where('credit', false);
//            }], 'amount')
//            ->orderBy($data->get('OrderBy') ?? $this->orderBy, $data->get('OrderBy') ?? $this->orderDesc ? 'desc' : 'asc');
//        $query->select(DB::raw('SUM(CASE WHEN credit THEN amount ELSE -amount END)'))
        $query->select('entries.*')
            ->selectSub(function ($query) {
                $query->select(DB::raw("SUM(CASE WHEN sub_entries.credit = (SELECT credit FROM accounts WHERE accounts.id = entries.account_id) THEN sub_entries.amount ELSE -sub_entries.amount END)"))
                    ->from('entries as sub_entries','accounts')
                    ->whereColumn('sub_entries.account_id', '=', 'entries.account_id')
                    ->where('sub_entries.created_at', '<=', DB::raw('entries.created_at'))
                    ->orderBy('sub_entries.created_at');
            }, 'balance')
            ->selectSub(function ($query) use ($data) {
                $query->select(DB::raw("SUM(CASE WHEN per_entries.credit = (SELECT credit FROM accounts WHERE accounts.id = entries.account_id) THEN per_entries.amount ELSE -per_entries.amount END)"))
                    ->from('entries as per_entries')
                    ->whereColumn('per_entries.account_id', '=', 'entries.account_id')
                    ->where('per_entries.created_at', '>=', $data['start_date'])
                    ->where('per_entries.created_at', '<=', DB::raw('entries.created_at'))
                    ->where('per_entries.created_at', '<=', $data['end_date'])
                    ->orderBy('per_entries.created_at');
            }, 'period_balance')


        ;

        $query->orderBy('created_at');
//        $query->orderBy('account_id');
        return [$query->get(), $dataset, $accounts];
    }

    public function export()
    {
        return Excel::download(new ClientsExport, 'clients_' . date('d-m-Y') . '.xlsx');
    }
}
