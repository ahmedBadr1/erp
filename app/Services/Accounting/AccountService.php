<?php

namespace App\Services\Accounting;

use App\Exports\UsersExport;
use App\Models\Accounting\Account;
use App\Models\Accounting\AccountType;
use App\Models\Accounting\Node;
use App\Services\ClientService;
use App\Services\ClientsExport;
use App\Services\Inventory\WarehouseService;
use App\Services\MainService;
use App\Services\Purchases\SupplierService;
use App\Services\System\AccessService;
use App\Services\System\AddressService;
use App\Services\System\ContactService;
use App\Services\System\TagService;
use Exception;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class AccountService extends MainService
{

    public function all($fields = null, $type_id = null, $node_id = null)
    {
        $data = $fields ?? (new Account())->getFillable();

        $query = Account::query();
        if (!empty($node_id)) {
            $node = Node::active()->with('descendants')->whereId($node_id)->first();
            $nodeIds = collect([$node->id, ...$node->descendants->pluck('id')]);
            $query->whereIntegerInRaw('node_id', $nodeIds);
        }

        if ($type_id) {
            if (is_int($type_id)) {
                $query->where('account_type_id', $type_id);
            } else {
                $query->whereHas('type', fn($q) => $q->where('code', $type_id));
            }
        }

        return $query->get($data);
    }

    public function types($fields = null)
    {
        $data = $fields ?? (new AccountType)->getFillable();

        return AccountType::active()->get($data);
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

    public function store(array $data, $code = null)
    {

                $inputs = [
                    'name' => $data['name'],
                    'description' => $data['description'],
                    'credit_limit' => $data['credit_limit'] ?? null,
                    'debit_limit' => $data['debit_limit'] ?? null,
                    'cost_center_id' => $data['cost_center_id'] ?? null,
                    'd_opening' => $data['d_opening'] ?? null,
                    'c_opening' => $data['c_opening'] ?? null,
                    'opening_date' => $data['opening_date'] ?? null,
                ];
                if ($code) {
                    $account = Account::where('code', $code)->firstOrFail();
                    $inputs['active'] = $data['active'] ?? $account->active;
                    $account->update($inputs);
                } else {
                    $node = Node::withCount('accounts')->whereId($data['node_id'])->first();
                    $account = Account::create([
                        ...$inputs,
                        'currency_id' => $data['currency_id'] ?? 1,
                        'node_id' => $node->id,
                        'accept_cost_center' => $node->accept_cost_center,
                        'credit' => $node->credit,
                    ]);
//                    $accountType = AccountType::find($account->account_type_id);
//                    $new = ['name'=>$account->name,'account_id'=>$account->id];
//                    $transType = match ($node->id) {
//                        19 =>  (new WarehouseService())->store($new),
//                        20 =>  (new ClientService())->store($new),
//                        29 =>  (new SupplierService())->store($new),
////                        default => throw new \RuntimeException('No Type Matches'),
//                    };

                }

                if (isset($data['contact'])) {
                    (new ContactService())->store($data['contact'], $account->id, 'account');
                }

                if (isset($data['address'])) {
                    (new AddressService())->store($data['address'], $account->id, 'account');
                }
                if (isset($data['tags'])) {
                    (new TagService())->sync($data['tags'], $account, 'account');
                }
                if (isset($data['groups'])) {
                    (new AccessService())->sync($data['groups'], 'group', $account, 'account',);
                }
                if (isset($data['users'])) {
                    (new AccessService())->sync($data['users'], 'user', $account, 'account',);
                }

            return $account;
    }

    public function update($code, array $data)
    {
        try {
            return true;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function updateBalance($account_id)
    {
        $accountIds = is_array($account_id) ? $account_id : [$account_id];

        foreach ($accountIds as $id) {
            $this->updateAccountBalance($id);
        }

        return true;
    }

    private function updateAccountBalance($account_id)
    {
        $account = Account::with(['entries' => fn($q) => $q->locked(0)])->whereId($account_id)->first();
        $account->total_debit = (float)$account->entries->where('credit', 0)->sum('amount') ;
        $account->total_credit = (float)$account->entries->where('credit', 1)->sum('amount') ;
        $account->balance = $account->credit ? $account->total_credit -  $account->total_debit : $account->total_debit - $account->total_credit ;
        $account->save();

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
