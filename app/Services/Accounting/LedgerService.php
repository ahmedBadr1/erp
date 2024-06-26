<?php

namespace App\Services\Accounting;

use App\Exports\UsersExport;
use App\Models\Accounting\Account;
use App\Models\Accounting\Entry;
use App\Models\Accounting\Ledger;
use App\Models\Accounting\Node;
use App\Models\Accounting\Transaction;
use App\Models\System\ModelGroup;
use App\Services\ClientsExport;
use App\Services\MainService;
use App\Services\System\ModelGroupService;
use Exception;
use Illuminate\Support\Carbon;
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
                $group_account_id = Account::whereId($account)->value('account_group_id');
                if ($group_account_id) {
                    $related = Account::where('account_group_id', $group_account_id)->whereNotIn('id', $accounts)->pluck('id')->toArray();
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


        $query->whereHas('entries', fn($q) => $q->whereIntegerInRaw('account_id', $accounts));
        if (isset($data['costCenters'])) {
            $query->whereHas('entries', fn($q) => $q->whereIntegerInRaw('cost_center_id', $data['costCenters']));
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
        $dataset[] = $data['posted'] ? 'Posted' : 'Un Posted';


        $query->with(['firstTransaction', 'group.firstInvTransaction']);

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
            $ids = [];
            foreach ($data['codes'] as $code) {
                if (preg_match('/^JE-(\d+)/', $code, $matches)) {
                    $ids[] = $matches[1];
                }
            }
            $query->where(fn($q) => $q->whereIn('id', $ids)
                ->orWhere(fn($q) => $q->orWhereHas("transactions", fn($qu) => $qu->whereIn('transactions.code', $data['codes'])
                )));
            foreach ($data['codes'] as $code) {
                $dataset[] = 'Code ' . $code;
            }
        }

        if (!empty($data['transactionTypes'])) {
            $query->whereHas('transactions', fn($q) => $q->whereIn('type', $data['transactionTypes']));
            foreach ($data['transactionTypes'] as $type) {
                $dataset[] = 'Type ' . $type;
            }
        } else {
            $dataset[] = 'All Types';
        }

        if (!empty($data['sellers'])) {
            $query->whereIntegerInRaw('responsible_id', $data['sellers']);
        }


        if (!empty($data['accounts'])) {
            $query->whereHas('transactions',
                fn($q) => $q->where(fn($q) => $q->whereIntegerInRaw('first_party_id', $data['accounts'])
                    ->orWhereIntegerInRaw('second_party_id', $data['accounts']))
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
            $query->whereIntegerInRaw('responsible_id', $data['sellers']);
        }

        $dataset[] = 'start date ' . Carbon::parse($data['start_date'])->format('Y/m/d');
        $dataset[] = 'end date ' . Carbon::parse($data['end_date'])->format('Y/m/d');
        $query->when($data['start_date'], function ($query) use ($data) {
            $query->where('due', '>=', $data['start_date']);
        })->when($data['end_date'], function ($query) use ($data) {
            $query->where('due', '<=', $data['end_date']);
        });
        $query->orderBy('due');
        return ['rows' => $query->get(), 'dataset' => $dataset];
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

    public function store(int $groupId, float $amount, $currency_id, $due = null, $note = null, $user_id = null, $system = 1)
    {
        return Ledger::create([
            'group_id' => $groupId,
            'amount' => $amount,
            'due' => $due ?? now(),
            'note' => $note ?? null,
            'responsible_id' => $user_id ?? auth()->id(),
            'created_by' => auth()->id(),
//                'posted' => 0,
            'system' => $system
        ]);
    }

    public function storeType(array $data, $code = null)
    {
        if ($code) {
            if ($data['type'] == 'JE') {
                if (preg_match('/^JE-(\d+)/', $code, $matches)) {
                    $id = $matches[1];
                    $transaction = Ledger::with('entries')->find($id);
                } else {
                    throw new  \RuntimeException('Code Not Valid');
                }
            } else {
                $transaction = Transaction::where('code', $code)->firstOrFail();
            }
            $groupId = $transaction->group_id;
            match ($data['type']) {
                'CI' => $this->cashin(groupId: $groupId, treasuryId: $data['treasury'], accounts: $data['accounts'], amount: $data['amount'], currencyId: $data['currency_id'], date: $data['due'] ?? null, note: $data['note'] ?? null, paperRef: $data['paper_ref'] ?? null, responsible: $data['responsible'] ?? auth('api')->id(), system: 0),
                'CO' => $this->cashout(groupId: $groupId, treasuryId: $data['treasury'], accounts: $data['accounts'], amount: $data['amount'], currencyId: $data['currency_id'], date: $data['due'] ?? null, note: $data['note'] ?? null, paperRef: $data['paper_ref'] ?? null, responsible: $data['responsible'] ?? auth('api')->id(), system: 0),
                'JE' => $this->jouranlEntry(data: $data, groupId: $groupId, system: 0 ,ledger: $transaction),
                default => throw  new \RuntimeException('Un Valid Type'),
            };

        } else {
            $groupId = (new ModelGroupService)->store()->id;
            match ($data['type']) {
                'CI' => $this->cashin(groupId: $groupId, treasuryId: $data['treasury'], accounts: $data['accounts'], amount: $data['amount'], currencyId: $data['currency_id'], date: $data['due'] ?? null, note:  $data['note'] ?? null, paperRef: $data['paper_ref'] ?? null, responsible: $data['responsible'] ?? auth('api')->id(), system: 0),
                'CO' => $this->cashout(groupId: $groupId, treasuryId: $data['treasury'], accounts: $data['accounts'], amount: $data['amount'], currencyId: $data['currency_id'], date: $data['due'] ?? null, note: $data['note'] ?? null, paperRef: $data['paper_ref'] ?? null, responsible: $data['responsible'] ?? auth('api')->id(), system: 0),
                'JE' => $this->jouranlEntry(data: $data, groupId: $groupId, system: 0),
                default => throw  new \RuntimeException('Un Valid Type'),
            };
        }



        return true ;
    }

    public function post(array $ids, $post = true)
    {
        Ledger::whereIn('id', $ids)->update(['posted' => $post]);
        Entry::whereIn('ledger_id', $ids)->update(['posted' => $post]);
        Transaction::whereIn('ledger_id', $ids)->update(['posted' => $post]);
        return true;
    }

    public function cashin($groupId, $treasuryId, $accounts, float $amount, $currencyId, $date, $note = null, $paperRef = null, $responsible = null, $system = 1)
    {
        if (!$groupId) {
            $groupId = ModelGroup::create()->id;
        }
        $EntryService = new EntryService();
        $TransactionService = new TransactionService();
        $AccountService = new AccountService();
        $treasury = Account::find($treasuryId);


        $ledger = $this->store(groupId: $groupId, amount: $amount, currency_id: $currencyId, due: $date, note: $note, user_id: $responsible, system: 1);
        $EntryService->createDebitEntry(amount: $amount, account_id: $treasuryId, ledger_id: $ledger->id, cost_center_id: $treasury->cost_center_id);
        $AccountService->updateBalance(account_id: $treasury->id);

        if (is_array($accounts)) {
            foreach ($accounts as $account) {
                $EntryService->createCreditEntry(amount: $account['amount'], account_id: $account['id'], ledger_id: $ledger->id, cost_center_id: $account['cost_center_id'] ?? null, comment: $account['comment'] ?? null);
                $AccountService->updateBalance(account_id: $account['id']);
            }
        } else { // Account Model
            $EntryService->createCreditEntry(amount: $amount, account_id: $accounts->id, ledger_id: $ledger->id, cost_center_id: $accounts->cost_center_id);
            $AccountService->updateBalance(account_id: $accounts->id);
            $account['id'] = $accounts->id;
        }

        $TransactionService->createCI(groupId: $groupId, amount: $amount, ledger_id: $ledger->id, first_party_id: $treasuryId, second_party_id: $account['id'], due: $date, note: $note, user_id: $responsible, paper_ref: $paperRef, system: $system);

        return false;
    }


    public function cashout($groupId, $treasuryId, $accounts, float $amount, $currencyId, $date, $note = null, $paperRef = null, $responsible = null, $system = 1)
    {
        if (!$groupId) {
            $groupId = ModelGroup::create()->id;
        }
        $EntryService = new EntryService();
        $TransactionService = new TransactionService();
        $AccountService = new AccountService();
        $treasury = Account::find($treasuryId);

        $ledger = $this->store(groupId: $groupId, amount: $amount, currency_id: $currencyId, due: $date, note: $note, user_id: $responsible, system: 1);

        if (is_array($accounts)) {
            foreach ($accounts as $account) {
                $EntryService->createDebitEntry(amount: $account['amount'], account_id: $account['id'], ledger_id: $ledger->id, cost_center_id: $account['cost_center_id'] ?? null, comment: $account['comment'] ?? null);
                $AccountService->updateBalance(account_id: $account['id']);
            }
        } else { // Account Model
            $EntryService->createDebitEntry(amount: $amount, account_id: $accounts->id, ledger_id: $ledger->id, cost_center_id: $accounts->cost_center_id);
            $AccountService->updateBalance(account_id: $accounts->id);
            $account['id'] = $accounts->id;
        }
//        throw  new \RuntimeException($treasuryId);

        $EntryService->createCreditEntry(amount: $amount, account_id: $treasuryId, ledger_id: $ledger->id, cost_center_id: $treasury->cost_center_id);
        $AccountService->updateBalance(account_id: $treasuryId);
        $TransactionService->createCO(groupId: $groupId, amount: $amount, ledger_id: $ledger->id, first_party_id: $treasuryId, second_party_id: $account['id'], due: $date, note: $note, user_id: $responsible, paper_ref: $paperRef, system: $system);

        return false;
    }

    public function jouranlEntry(array $data, $groupId = null, $system = 1 ,Ledger $ledger = null)
    {
        if (!isset($groupId)) {
            $groupId = (new ModelGroupService)->store()->id;
        }
        $EntryService = new EntryService();
        $AccountService = new AccountService();
        $accounts = [] ;
        if(!$ledger){
            $ledger = $this->store(groupId: $groupId, amount: $data['amount'], currency_id: $data['currency_id'], due: $data['due'], note: $data['note'] ?? null, user_id: $data['responsible'], system: $system);
        }else{
            $ledger->update([
                    'amount' => $data['amount'],
                    'due' => $data['due']  ?? null,
                    'note' => $data['note'] ?? null,
                    'responsible_id' => $data['responsible_id'] ?? auth()->id(),
                    'edited_by' => auth()->id(),
                ]) ;

            $accounts = array_merge($accounts,$ledger->entries->pluck('account_id')->toArray());
            $ledger->entries->each->delete();
        }


        foreach ($data['accounts'] as $account) {
            if ($account['c_amount']) {
                $EntryService->createCreditEntry(amount: $account['c_amount'], account_id: $account['id'], ledger_id: $ledger->id, cost_center_id: $account['cost_center_id'], comment: $account['comment'] ?? null);
            } else {
                $EntryService->createDebitEntry(amount: $account['d_amount'], account_id: $account['id'], ledger_id: $ledger->id, cost_center_id: $account['cost_center_id'], comment: $account['comment'] ?? null);
            }
            array_push($accounts,$account['id']);
        }
        $AccountService->updateBalance(account_id: array_unique($accounts));
        return false;
    }


    public function PI($type, $groupId, $warehouseAcc, $supplierAcc, $currencyId, $date, $total, $subTotal, $grossTotal, $tax = 0, $taxAccountId = null, $discount = null, $note = null, $paperRef = null, $responsible = null, $system = 1)
    {
        if (!$type) {
            $type = 'PI';
        }
        $EntryService = new EntryService();
        $TransactionService = new TransactionService();
        $AccountService = new AccountService();

        $ledger = $this->store(groupId: $groupId, amount: $grossTotal + $tax, currency_id: $currencyId, due: $date, note: $note, user_id: $responsible, system: 1);
//            dd($ledger);
        $EntryService->createDebitEntry(amount: $grossTotal, account_id: $warehouseAcc->id, ledger_id: $ledger->id, cost_center_id: $warehouseAcc->cost_centet_id);
        $AccountService->updateBalance(account_id: $warehouseAcc->id);
        if ($tax) {
            if (!$taxAccountId) {
                $taxAccountId = Account::whereHas('type', fn($q) => $q->where('code', 'T'))->value('id');
            }
            $EntryService->createDebitEntry(amount: $tax, account_id: $taxAccountId, ledger_id: $ledger->id);
            $AccountService->updateBalance(account_id: $taxAccountId);
        }
        if ($discount) {
            $discountAccountId = Account::whereHas('type', fn($q) => $q->where('code', 'PD'))->value('id');
            $EntryService->createCreditEntry(amount: $discount, account_id: $discountAccountId, ledger_id: $ledger->id);
            $AccountService->updateBalance(account_id: $discountAccountId);
        }

        $EntryService->createCreditEntry(amount: $total, account_id: $supplierAcc->id, ledger_id: $ledger->id, cost_center_id: $supplierAcc->cost_center_id);
        $AccountService->updateBalance(account_id: $supplierAcc->id);
        $TransactionService->createPI(groupId: $groupId, amount: $ledger->amount, ledger_id: $ledger->id, first_party_id: $warehouseAcc->id, second_party_id: $supplierAcc->id, due: $date, note: $note, user_id: $responsible, paper_ref: $paperRef, system: $system);

        return false;
    }


    public function SI($type, $groupId, $warehouseAcc, $clientAcc, $currencyId, $date, $total, $subTotal, $grossTotal, $tax = 0, $taxAccountId = null, $discount = null, $note = null, $paperRef = null, $responsible = null, $system = 1)
    {
        if (!$type) {
            $type = 'SI';
        }
        $EntryService = new EntryService();
        $TransactionService = new TransactionService();
        $AccountService = new AccountService();

        $ledger = $this->store(groupId: $groupId, amount: $total + $discount, currency_id: $currencyId, due: $date, note: $note, user_id: $responsible, system: $system);

        $EntryService->createDebitEntry(amount: $total, account_id: $clientAcc->id, ledger_id: $ledger->id, cost_center_id: $clientAcc->cost_centet_id);
        $AccountService->updateBalance(account_id: $clientAcc->id);

        if ($discount) {
            $discountAccountId = Account::whereHas('type', fn($q) => $q->where('code', 'SD'))->value('id');
            $EntryService->createDebitEntry(amount: $discount, account_id: $discountAccountId, ledger_id: $ledger->id);
            $AccountService->updateBalance(account_id: $discountAccountId);
        }

        $salesAccountId = Account::whereHas('type', fn($q) => $q->where('code', 'S'))->value('id');
        $EntryService->createCreditEntry(amount: $grossTotal, account_id: $salesAccountId, ledger_id: $ledger->id, cost_center_id: $warehouseAcc->cost_center_id);
        $AccountService->updateBalance(account_id: $salesAccountId);

        if ($tax) {
            if (!$taxAccountId) {
                $taxAccountId = Account::whereHas('type', fn($q) => $q->where('code', 'T'))->value('id');
            }
            $EntryService->createCreditEntry(amount: $tax, account_id: $taxAccountId, ledger_id: $ledger->id);
            $AccountService->updateBalance(account_id: $taxAccountId);
        }


        $TransactionService->createType(type: 'SI', groupId: $groupId, amount: $ledger->amount, ledger_id: $ledger->id, first_party_id: $warehouseAcc->id, second_party_id: $clientAcc->id, due: $date, note: $note, user_id: $responsible, paper_ref: $paperRef, system: $system);

        return false;
    }

    public function COGS($groupId, $warehouseAcc, $amount, $currencyId, $date, $note = null, $paperRef = null, $responsible = null, $system = 1)
    {

        $EntryService = new EntryService();
        $TransactionService = new TransactionService();
        $AccountService = new AccountService();

        $ledger = $this->store(groupId: $groupId, amount: $amount, currency_id: $currencyId, due: $date, note: $note, user_id: $responsible, system: $system);
        $cogsAccount = Account::whereHas('type', fn($q) => $q->where('code', 'COG'))->first();
        $EntryService->createCreditEntry(amount: $amount, account_id: $cogsAccount->id, ledger_id: $ledger->id, cost_center_id: $cogsAccount->cost_center_id);
        $AccountService->updateBalance(account_id: $cogsAccount->id);

        $EntryService->createDebitEntry(amount: $amount, account_id: $warehouseAcc->id, ledger_id: $ledger->id, cost_center_id: $warehouseAcc->cost_centet_id);
        $AccountService->updateBalance(account_id: $warehouseAcc->id);


        $TransactionService->createType(type: 'COGS', groupId: $groupId, amount: $ledger->amount, ledger_id: $ledger->id, first_party_id: $warehouseAcc->id, second_party_id: $cogsAccount->id, due: $date, note: $note, user_id: $responsible, paper_ref: $paperRef, system: $system);

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
