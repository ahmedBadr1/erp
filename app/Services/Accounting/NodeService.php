<?php

namespace App\Services\Accounting;

use App\Exports\UsersExport;
use App\Models\Accounting\Account;
use App\Models\Accounting\Entry;
use App\Models\Accounting\Node;
use App\Services\ClientsExport;
use App\Services\MainService;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Number;
use Maatwebsite\Excel\Facades\Excel;

class NodeService extends MainService
{

    public function all($fields = null,$relations = [], $countRelations = [])
    {
        $data = $fields ?? (new Node)->getFillable();
        $query =Node::active();
        if (!empty($relations )) {
            $query->with(...$relations);
        }
        if (!empty($countRelations )) {
            $query->withCount(...$countRelations);
        }
        return $query->get($data);
    }

    public function tree($fields = null, $relations = [], $countRelations = [])
    {
        $data = $fields ?? (new Node)->getFillable();
        $query = Node::tree();
        if (!empty($relations )) {
            $query->with(...$relations);
        }
        if (!empty($countRelations )) {
            $query->withCount(...$countRelations);
        }
        return $query->get()->toTree();
    }

    public function root($level = null)
    {
       return Node::whereNull('parent_id')->get(['id','name']);
    }

    public function levels()
    {
        return array();
    }


    public function search($search)
    {
        $search = trim($search);
        return empty($search) ? Account::query()
            : Account::query()->where('name', 'like', '%' . $search . '%')
                ->orWhere('type_code', 'like', '%' . $search . '%')
                ->orWhere('code', 'like', '%' . $search . '%')
//                ->orWhere('description', 'like', '%' . $search . '%')
//                ->orWhereHas('currency', fn($q) => $q->where('name', 'like', '%' . $search . '%'))
                ->orWhereHas('costCenter', fn($q) => $q->where('name', 'like', '%' . $search . '%'))
                ->orWhereHas('node', fn($q) => $q->where('name', 'like', '%' . $search . '%'));
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

    public function updateBalance($account_id)
    {
        try {
            $account = Account::with(['entries' => fn($q) => $q->locked(0)])->whereId($account_id)->first();
            $totalCredit = (int)$account->entries->where('credit', 1)->sum('amount') + $account->c_opening;
            $totalDebit = (int)$account->entries->where('credit', 0)->sum('amount') + $account->d_opening;
            $account->balance = $account->credit ? $totalCredit - $totalDebit : $totalDebit - $totalCredit;
            $account->save();
        } catch (Exception $e) {
            return $e->getMessage();
        }
        return true;
    }


    public function gl(array $data = null)
    {
        $columns = $data['columns'];
        $dataset = [];
        $dataset[] = 'All Levels';

        $nodeLevel = $data['accounts'] ?? [];

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

//        if ($data['related_accounts']) {
//            foreach ($data['accounts'] as $account) {
//                $group_account_id = Account::whereId($account)->value('account_group_id');
//                if ($group_account_id) {
//                    $related = Account::where('account_group_id', $group_account_id)->whereNotIn('id', $accounts)->pluck('id')->toArray();
//                    array_push($accounts, ...$related);
//                }
//            }
//        }

//        $rootNode = Node::tree()->with(['children'=> fn($q) =>
//        $q->withSum('accounts as net_credit','c_balance')
//            ->withSum('accounts as net_debit' , 'd_balance')
//        ])->get();


        $query = Node::tree()
            ->with(['accounts'=> fn($q) => $q->select('id','code','d_balance','c_balance','d_opening','c_opening','node_id')])
            ->withSum('accounts as net_credit','c_balance')
            ->withSum('accounts as net_debit' , 'd_balance');
        $coll = $query->get();

        $total = collect(['name'=>'Total']);
        $max = $coll->max('depth') ;
        for ($i = 0; $i < ($max +1); $i++) {
            $coll->where('depth',$max - $i)->map(function($node) use ($coll) {
                $node->net_credit = round( $node->net_credit +  $coll->where('parent_id',$node->id)?->sum('net_credit')  , 4);
                $node->net_debit = round( $node->net_debit +  $coll->where('parent_id',$node->id)?->sum('net_debit')  , 4);
            });

        }

        $total['net_credit'] = $coll->whereNull('parent_id')->sum('net_credit');
        $total['net_debit'] = $coll->whereNull('parent_id')->sum('net_debit');

        return [$coll->toTree(), $dataset,$total];

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

        $query->whereIn('account_id', $accounts);


        $accounts = Account::whereIn('id', $accounts)
            ->withSum(['entries as credit_sum' => function ($query) {
                $query->select(DB::raw('SUM(amount)'))
                    ->where('credit', true);
            }], 'amount')
            ->withSum(['entries as debit_sum' => function ($query) {
                $query->select(DB::raw('SUM(amount)'))
                    ->where('credit', false);
            }], 'amount')
            ->withSum(['entries as period_credit_sum' => function ($query) use($data) {
                $query->select(DB::raw('SUM(amount)'))
                    ->where('credit', true)
                    ->where('created_at', '>=', $data['start_date'])
                    ->where('created_at', '<=', $data['end_date']);
            }], 'amount')
            ->withSum(['entries as period_debit_sum' => function ($query)  use($data) {
                $query->select(DB::raw('SUM(amount)'))
                    ->where('credit', false)
                    ->where('created_at', '>=', $data['start_date'])
                    ->where('created_at', '<=', $data['end_date']);
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
        return Excel::download(new AccountsExport, 'accounts_' . date('d-m-Y') . '.xlsx');
    }
}
