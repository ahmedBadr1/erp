<?php

namespace App\Services\Accounting;

use App\Exports\UsersExport;
use App\Models\Accounting\Entry;
use App\Models\Accounting\Ledger;
use App\Models\Accounting\Transaction;
use App\Models\Crm\Client;
use App\Models\User;
use App\Services\ClientsExport;
use App\Services\MainService;
use Exception;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class LedgerService extends MainService
{

    public function fetchAll()
    {
        return Ledger::get();
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

    public function store(int $amount, $due = null, $description = null, $userId = null, $system = 1)
    {
        try {
            $ledger = Ledger::create([
                'amount' => $amount,
                'due' => $due ?? now(),
                'description' => $description ?? 'cash in',
                'user_id' => $userId ?? auth()->id(),
                'system' => $system
            ]);
            return $ledger;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function cashin($debit_account, $credit_account, $amount,$cost_center = null, $due = null, $description = null, $userId = null)
    {
        $EntryService = new EntryService();
        $TransactionService = new TransactionService();
        $AccountService = new AccountService();
        DB::beginTransaction();
        try {
            $ledger = $this->store($amount, $due, $description, $userId);
            $EntryService->createDebitEntry($amount, $debit_account, $ledger->id);
            $EntryService->createCreditEntry($amount, $credit_account, $ledger->id,$cost_center);
            $TransactionService->createCI($amount, $ledger->id, $credit_account, $due, $description, $userId);
            $AccountService->updateBalance($credit_account);
            $AccountService->updateBalance($debit_account);

        } catch (Exception $e) {
            DB::rollBack();
            return $e->getMessage();
        }
        DB::commit();
        return true;
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
