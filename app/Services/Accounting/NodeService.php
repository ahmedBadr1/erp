<?php

namespace App\Services\Accounting;

use App\Exports\UsersExport;
use App\Models\Accounting\Account;
use App\Models\Accounting\Node;
use App\Services\ClientsExport;
use App\Services\MainService;
use Exception;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class NodeService extends MainService
{

    public function all($fields = null, $relations = [], $countRelations = [])
    {
        $data = $fields ?? (new Node)->getFillable();
        $query = Node::active();
        if (!empty($relations)) {
            $query->with(...$relations);
        }
        if (!empty($countRelations)) {
            $query->withCount(...$countRelations);
        }
        return $query->get($data);
    }

    public function tree($fields = null, $relations = [], $countRelations = [])
    {
        $data = $fields ?? (new Node)->getFillable();
        $query = Node::tree();
        if (!empty($relations)) {
            $query->with(...$relations);
        }
        if (!empty($countRelations)) {
            $query->withCount(...$countRelations);
        }
        return $query->get()->toTree();
    }

    public function root($level = null)
    {
        return Node::whereNull('parent_id')->get(['id', 'name']);
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


    public function gl(array $data)
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

        if ($data['dateType'] === 'period') {
            $start_date = $data['from_date'];
            $end_date = $data['to_date'];
        } else {

            $start_date = setting('opening_date');
            $end_date = $data['date'];
        }

        $query = Node::tree()
            ->with(['accounts' => fn($q) => $q->select('id', 'name', 'code', 'total_debit', 'total_debit', 'balance', 'd_opening', 'c_opening', 'node_id')
                ->withSum(['entries as debit_sum' => function ($query) use ($start_date, $end_date) {
                    $query->select(DB::raw('SUM(amount)'))
                        ->where('created_at', '>=', $start_date)
                        ->where('created_at', '<=', $end_date)
                        ->where('locked', false)->where('credit', false);
                }], 'amount')
                ->withSum(['entries as credit_sum' => function ($query) use ($start_date, $end_date) {
                    $query->select(DB::raw('SUM(amount)'))
                        ->where('created_at', '>=', $start_date)
                        ->where('created_at', '<=', $end_date)
                        ->where('locked', false)->where('credit', true);
                }], 'amount')
            ])
            ->withSum('accounts as opening_debit', 'c_opening')
            ->withSum('accounts as opening_credit', 'd_opening')
        ;
        $coll = $query->get()
            ->map(function ($node) {
                if ($node->credit) {
                    $diff = $node->accounts->sum('credit_sum') - $node->accounts->sum('debit_sum');
                    $node->net_credit = ($diff > 0) ? abs($diff) : 0.000;
                    $node->net_debit = ($diff > 0) ? 0.0000 : abs($diff);
                } else {
                    $diff = $node->accounts->sum('debit_sum') - $node->accounts->sum('credit_sum');
                    $node->net_credit = ($diff < 0) ? abs($diff) : 0.000;
                    $node->net_debit = ($diff < 0) ? 0.0000 : abs($diff);
                }

                $node->total_debit = $node->accounts->sum('debit_sum');
                $node->total_credit = $node->accounts->sum('credit_sum');
                return $node;
            });


        $total = collect(['name' => 'Total']);
        $max = $coll->max('depth');
        for ($i = 0; $i < ($max + 1); $i++) {
            $coll->where('depth', $max - $i)->map(function ($node) use ($coll) {
                $net_credit = round($node->net_credit + $coll->where('parent_id', $node->id)?->sum('net_credit'), 4);
                $net_debit = round($node->net_debit + $coll->where('parent_id', $node->id)?->sum('net_debit'), 4);

                if ($node->credit) {
                    $diff =  $net_credit - $net_debit;
                    $node->net_credit = ($diff > 0) ? abs($diff) : 0.000;
                    $node->net_debit = ($diff > 0) ? 0.0000 : abs($diff);
                } else {
                    $diff = $net_debit - $net_credit;
                    $node->net_credit = ($diff < 0) ? abs($diff) : 0.000;
                    $node->net_debit = ($diff < 0) ? 0.0000 : abs($diff);
                }

                $node->opening_debit = round($node->opening_debit + $coll->where('parent_id', $node->id)?->sum('opening_debit'), 4);
                $node->opening_credit = round($node->opening_credit + $coll->where('parent_id', $node->id)?->sum('opening_credit'), 4);

                $node->total_debit = round($node->total_debit + $coll->where('parent_id', $node->id)?->sum('total_debit'), 4);
                $node->total_credit = round($node->total_credit + $coll->where('parent_id', $node->id)?->sum('total_credit'), 4);

            });
        }

        $total['net_debit'] = round($coll->whereNull('parent_id')->sum('net_debit'), 4);
        $total['net_credit'] = round($coll->whereNull('parent_id')->sum('net_credit'), 4);
        $total['opening_debit'] = round($coll->whereNull('parent_id')->sum('opening_debit'), 4);
        $total['opening_credit'] = round($coll->whereNull('parent_id')->sum('opening_credit'), 4);
        $total['total_debit'] = round($coll->whereNull('parent_id')->sum('total_debit'), 4);
        $total['total_credit'] = round($coll->whereNull('parent_id')->sum('total_credit'), 4);

        return [$coll->toTree(), $dataset, $total];


//        if (isset($data['with_transactions']) && $data['with_transactions']) {
//            $query->with('ledger.transaction');
//        }


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
