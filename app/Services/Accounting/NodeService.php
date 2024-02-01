<?php

namespace App\Services\Accounting;

use App\Exports\UsersExport;
use App\Models\Accounting\Account;
use App\Models\Accounting\Node;
use App\Services\ClientsExport;
use App\Services\MainService;
use Exception;
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
