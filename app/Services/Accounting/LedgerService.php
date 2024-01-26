<?php

namespace App\Services\Accounting;

use App\Exports\UsersExport;
use App\Models\Accounting\Account;
use App\Models\Accounting\CostCenter;
use App\Models\Accounting\Entry;
use App\Models\Accounting\Ledger;
use App\Models\Accounting\Node;
use App\Models\Accounting\Transaction;
use App\Services\ClientsExport;
use App\Services\MainService;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
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
                    $related = Account::where('group_account_id', $group_account_id)->whereNotIn('id', $accounts)->pluck('id')->toArray();
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
            $query->where('currency_id', $data['currency']);
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

    public function posting(array $data = null)
    {
        $columns = $data['columns'];

        $dataset = [];

        $query = Ledger::query();
        $query->locked(0);
        $query->posted($data['posted']);
        $dataset[] = $data['posted'] ?  'Posted' : 'Un Posted';


        $query->with('firstTransaction','firstWhTransaction');

//        if (isset($columns['responsible']) && $columns['responsible']) {
        $query->with('responsible');
//        }

        if (isset($columns['edited_by']) && $columns['edited_by']) {
            $query->with('editor');
        }
        if (isset($columns['first_party']) && $columns['first_party']) {
            $query->with('transactions.firstParty');
        }

        if (isset($columns['second_party']) && $columns['second_party']) {
            $query->with('transactions.secondParty');
        }

        if (!empty($data['codes'])) {
////            $query->whereIn('code',$data['codes']);
//            $query->whereHas("transactions" , fn($qu) => $qu->whereIn('transactions.code', $data['codes']));
            $query->where(fn($q) => $q->whereIn('id', $data['codes'])
                ->orWhere(fn($q) => $q->orWhereHas("transactions",fn($qu) => $qu->whereIn('transactions.code', $data['codes'])
                )));
            foreach ($data['codes'] as $code){
                $dataset[] = 'Code ' . $code ;
            }
        }

        if (!empty($data['transactionTypes'])) {
            $query->whereHas('transactions', fn($q) => $q->whereIn('type', $data['transactionTypes']));
            foreach ($data['transactionTypes'] as $type){
                $dataset[] = 'Type ' . $type ;
            }
        }else{
            $dataset[] = 'All Types';
        }

        if (!empty($data['sellers'])) {
            $query->whereIn('responsible_id', $data['sellers']);
        }


        if (!empty($data['accounts'])) {
            $query->whereHas('transactions',
                fn($q) => $q->where(fn($q) => $q->whereIn('first_party_id', $data['accounts'])
                    ->orWhereIn('second_party_id', $data['accounts']))
            );
        }

        if (!isset($data['credit'])) {
            $query->whereHas('entries', fn($q) => $q->where(fn($q) => $q->whereHas('account', fn($q) => $q->where('credit', true)))
            );
        }

        if (!isset($data['debit'])) {
            $query->whereHas('entries', fn($q) => $q->where(fn($q) => $q->whereHas('account', fn($q) => $q->where('credit', false)))
            );
        }

//        if (!isset($data['debit'])) {
//            $query->where(fn($q) => $q->whereHas('firstParty', fn($q) => $q->where('credit', false))
//                ->orWhereHas('secondParty', fn($q) => $q->where('credit', false))
//            );
//        }

        if (isset($data['sellers']) && $data['sellers']) {
            $query->whereIn('responsible_id', $data['sellers']);
        }

        $dataset[] = 'start date ' . Carbon::parse($data['start_date'])->format('Y/m/d');
        $dataset[] = 'end date ' . Carbon::parse($data['end_date'])->format('Y/m/d');
        $query->when($data['start_date'], function ($query) use ($data) {
            $query->where('due', '>=', $data['start_date']);
        })->when($data['end_date'], function ($query) use ($data) {
            $query->where('due', '<=', $data['end_date']);
        });
        $query->orderBy('due');
        return ['rows'=> $query->get(),'dataset'=> $dataset];
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

    public function store(int $amount, int $currency_id, $due = null, $description = null, $user_id = null, $system = 1)
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

    public function post(array $ids , $post = true)
    {
        try {
            Ledger::whereIn('id',$ids)->update(['posted'=>$post]);
            Entry::whereIn('ledger_id',$ids)->update(['posted'=>$post]);
            Transaction::whereIn('ledger_id',$ids)->update(['posted'=>$post]);
            return  true ;
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
            $ledger = $this->store($data['amount'], $data['currency_id`'], $data['due'], $data['description'], $data['responsible']);
            $EntryService->createDebitEntry($data['amount'], $treasury->id, $ledger->id, $treasury->cost_center_id);
            $AccountService->updateBalance($treasury->id);
            foreach ($data['accounts'] as $account) {
                $account_id = Account::where('code', $account['code'])->value('id');
                $cost_center_id = isset($account['costCenter']) ? CostCenter::where('code', $account['costCenter']['code'])->value('id') : null;
                $EntryService->createCreditEntry($account['amount'], $account_id, $ledger->id, $cost_center_id, $account['comment'] ?? null);
                $TransactionService->createCI($data['amount'], $ledger->id, $treasury->id, $account_id, $data['due'], $data['description'], $data['responsible'], $data['je_code'], $data['document_no']);
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
            $ledger = $this->store($data['amount'], $data['currency_id'], $data['due'], $data['description'], $data['responsible']);
            foreach ($data['accounts'] as $account) {
                $account_id = Account::where('code', $account['code'])->value('id');
                $cost_center_id = isset($account['costCenter']) ? CostCenter::where('code', $account['costCenter']['code'])->value('id') : null;
                $EntryService->createDebitEntry($account['amount'], $account_id, $ledger->id, $cost_center_id, $account['comment'] ?? null);
                $TransactionService->createCO($data['amount'], $ledger->id, $treasury->id, $account_id, $data['due'], $data['description'], $data['responsible'], $data['je_code'], $data['document_no']);
                $AccountService->updateBalance($account_id);
            }
            $EntryService->createCreditEntry($data['amount'], $treasury->id, $ledger->id, $treasury->cost_center_id);
            $AccountService->updateBalance($treasury->id);
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
            $ledger = $this->store($data['amount'], $data['currency_id'], $data['due'], $data['description'], $data['responsible']);
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
