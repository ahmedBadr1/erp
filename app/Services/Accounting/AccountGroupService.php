<?php

namespace App\Services\Accounting;

use App\Exports\UsersExport;
use App\Models\Accounting\Account;
use App\Models\Accounting\AccountGroup;
use App\Services\ClientsExport;
use App\Services\MainService;

class AccountGroupService extends MainService
{

    public function all($fields = null)
    {
        $data = $fields ?? (new AccountGroup())->getFillable();

        $query = AccountGroup::query();

        return $query->get($data);
    }

    public function get($code)
    {
        $account = Account::with(['group.accounts' => fn($q) => $q->where('code', '!=',$code)])->where('code', $code)->first();

        return [$account, collect($account->group?->accounts)];
    }

    public function store($data = [])
    {
        return AccountGroup::create([
            'created_by' => auth()->id()
        ]);
    }

    public function update(int $id , array$accounts)
    {
        $account = Account::findOrFail($id);
        if (!$account->account_group_id) {
            $group = $this->store();
            $groupId = $group->id;
            $accounts[] = $id;
        } else {
            $groupId = $account->account_group_id;
            Account::whereIntegerNotInRaw('id',[...$accounts,$id])->where('account_group_id',$groupId)->update(['account_group_id' => null]);
        }
        return Account::whereIntegerInRaw('id', array_values($accounts))->update(['account_group_id' => $groupId]);
    }

}
