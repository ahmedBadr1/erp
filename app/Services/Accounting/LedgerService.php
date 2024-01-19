<?php

namespace App\Services\Accounting;

use App\Exports\UsersExport;
use App\Models\Accounting\Account;
use App\Models\Accounting\CostCenter;
use App\Models\Accounting\Entry;
use App\Models\Accounting\Ledger;
use App\Models\Accounting\Node;
use App\Models\Accounting\Transaction;
use App\Models\Crm\Client;
use App\Models\User;
use App\Services\ClientsExport;
use App\Services\MainService;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class LedgerService extends MainService
{

    public function all($fields = null)
    {
        $data = $fields ?? (new Ledger())->getFillable();
        return Ledger::get($data);
    }

    public function accounts(array $data = null)
    {
        $columns = $data['columns'];

        $accounts = $data['accounts'] ?? [];


        if (isset($data['node'])) {
            $node = Node::active()->with(['descendants' => fn($q) => $q->with('accounts'), 'accounts'])->where('id', $data['node'])->first();
            $nodeAccounts = $node->account?->pluck('id');
            $nodeDescendants = $node->descendants?->pluck('id');
            if ($nodeAccounts) {
                array_push($accounts, ...$nodeAccounts);
            }
            if ($nodeDescendants) {
                array_push($accounts, ...$nodeDescendants);
            }
        }
        if ($data['related_accounts']) {
            foreach ($data['accounts'] as $account) {
                $group_account_id = Account::whereId($account)->value('group_account_id');
                if ($group_account_id) {
                    $related = Account::where('group_account_id',$group_account_id)->whereNotIn('id', $accounts)->pluck('id')->toArray();
                    array_push($accounts, ...$related);
                }
            }
        }
        $query = Ledger::query();
        $query->locked(0);
        if ($data['posting'] === 'posted') {
            $query->posted(1);
        } elseif ($data['posting'] === 'unposted') {
            $query->posted(0);
        }

        if (isset($data['with_transactions']) && $data['with_transactions']) {
            $query->with('transactions');
        }

        if (isset($data['detailed']) && $data['detailed']) {
            $query->with('entries.account');
        }

        if (isset($columns['created_by']) && $columns['created_by']) {
            $query->with('creator');
        }
        if (isset($columns['responsible']) && $columns['responsible']) {
            $query->with('responsible');
        }
        if (isset($columns['edited_by']) && $columns['edited_by']) {
            $query->with('editor');
        }

        if (isset($columns['document_currency']) && $columns['document_currency']) {
            $query->with('currency');
        }



        $query->whereHas('entries', fn($q) => $q->whereIn('account_id', $accounts));
        if (isset($data['costCenters'])) {
            $query->whereHas('entries', fn($q) => $q->whereIn('cost_center_id', $data['costCenters']));
        }

        if (isset($data['currency']) && $data['currency']) {
            $query->where('currency_id',$data['currency'] );
        }

//        $query->when($data->get('start_date'), function ($query) use ($data) {
//            $query->where('created_at', '>=', $data->get('start_date'));
//        })
//            ->when($data->get('end_date'), function ($query) use ($data) {
//                $query->where('created_at', '<=', $data->get('start_date'));
//            });
//        $query->withSum(['entries as credit_sum' => function ($query) {
//                $query->where('credit', true);
//            }], 'amount')
//            ->withSum(['entries as debit_sum' => function ($query) {
//                $query->where('credit', false);
//            }], 'amount')
//            ->orderBy($data->get('OrderBy') ?? $this->orderBy, $data->get('OrderBy') ?? $this->orderDesc ? 'desc' : 'asc');

        return $query->get();
    }


    public function search($search)
    {
        $search = trim($search);
        return empty($search) ? Ledger::query()
            : Ledger::query()->where('amount', 'like', '%' . $search . '%')
                ->orWhere('description', 'like', '%' . $search . '%')
                ->orWhere('type', 'like', '%' . $search . '%')
//                ->orWhereHas('entries', fn($q) => $q->where('description', 'like', '%' . $search . '%'))
//                ->orWhereHas('transaction', fn($q) => $q->where('type', 'like', '%' . $search . '%'))
                ->orWhereHas('entries.account', fn($q) => $q->where('name', 'like', '%' . $search . '%'));
    }

    public function store(int $amount,int $currency_id, $due = null, $description = null, $user_id = null, $system = 1)
    {
        try {
            $ledger = Ledger::create([
                'amount' => $amount,
                'currency_id' => $currency_id,
                'due' => $due ?? now(),
                'description' => $description,
                'responsible_id' => $user_id ?? auth()->id(),
                'created_by' => auth()->id(),
//                'posted' => 0,
                'system' => $system
            ]);
            return $ledger;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function cashin(array $data)
    {
        $EntryService = new EntryService();
        $TransactionService = new TransactionService();
        $AccountService = new AccountService();
        $treasury = Account::where('code', $data['treasury'])->first();

        DB::beginTransaction();
        try {
            $ledger = $this->store($data['amount'],$data['currency_id`'], $data['due'], $data['description'], $data['responsible']);
            $EntryService->createDebitEntry($data['amount'], $treasury->id, $ledger->id, $treasury->cost_center_id);
            $AccountService->updateBalance($treasury->id);
            foreach ($data['accounts'] as $account) {
                $account_id = Account::where('code', $account['code'])->value('id');
                $cost_center_id = isset($account['costCenter']) ? CostCenter::where('code', $account['costCenter']['code'])->value('id') : null;
                $EntryService->createCreditEntry($account['amount'], $account_id, $ledger->id, $cost_center_id, $account['comment'] ?? null);
                $TransactionService->createCI($data['amount'], $ledger->id, $account_id, $data['due'], $data['description'], $data['responsible'], $data['je_code'], $data['document_no']);
                $AccountService->updateBalance($account_id);
            }
        } catch (Exception $e) {
            DB::rollBack();
            return $e;
        }
        DB::commit();
        return false;
    }

    public function cashout(array $data)
    {
        $EntryService = new EntryService();
        $TransactionService = new TransactionService();
        $AccountService = new AccountService();
        $treasury = Account::where('code', $data['treasury'])->first();

        DB::beginTransaction();
        try {
            $ledger = $this->store($data['amount'],$data['currency_id'], $data['due'], $data['description'], $data['responsible']);
            $EntryService->createCreditEntry($data['amount'], $treasury->id, $ledger->id, $treasury->cost_center_id);
            $AccountService->updateBalance($treasury->id);
            foreach ($data['accounts'] as $account) {
                $account_id = Account::where('code', $account['code'])->value('id');
                $cost_center_id = isset($account['costCenter']) ? CostCenter::where('code', $account['costCenter']['code'])->value('id') : null;
                $EntryService->createDebitEntry($account['amount'], $account_id, $ledger->id, $cost_center_id, $account['comment'] ?? null);
                $TransactionService->createCO($data['amount'], $ledger->id, $account_id, $data['due'], $data['description'], $data['responsible'], $data['je_code'], $data['document_no']);
                $AccountService->updateBalance($account_id);
            }
        } catch (Exception $e) {
            DB::rollBack();
            return $e;
        }
        DB::commit();
        return false;
    }

    public function jouranlEntry(array $data)
    {
        $EntryService = new EntryService();
        $AccountService = new AccountService();
        DB::beginTransaction();
        try {
            $ledger = $this->store($data['amount'],$data['currency_id'], $data['due'], $data['description'], $data['responsible']);
            foreach ($data['accounts'] as $account) {
                $account_id = Account::where('code', $account['code'])->value('id');
                $cost_center_id = isset($account['costCenter']) ? CostCenter::where('code', $account['costCenter']['code'])->value('id') : null;
                if ($account['c_amount']) {
                    $EntryService->createCreditEntry($account['c_amount'], $account_id, $ledger->id, $cost_center_id, $account['comment'] ?? null);
                } else {
                    $EntryService->createDebitEntry($account['d_amount'], $account_id, $ledger->id, $cost_center_id, $account['comment'] ?? null);
                }
                $AccountService->updateBalance($account_id);
            }
        } catch (Exception $e) {
            DB::rollBack();
            return $e;
        }
        DB::commit();
        return false;
    }


    public function update($ledger, array $data)
    {
        try {
            $ledger->update($data);
            return $ledger;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function destroy($ledger)
    {
        if ($ledger->projects_count > 0) {
            return 0;
        } else {
            $ledger->delete();
        }
    }

    public function export()
    {
        return Excel::download(new LedgerExport, 'ledger_' . date('d-m-Y') . '.xlsx');
    }
}
