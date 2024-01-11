<?php

namespace App\Services\Accounting;

use App\Exports\UsersExport;
use App\Models\Accounting\Account;
use App\Models\Accounting\CostCenter;
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
                'description' => $description ,
                'user_id' => $userId ?? auth()->id(),
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
            $ledger = $this->store($data['amount'], $data['due'], $data['description'], $data['responsible']);
            $EntryService->createDebitEntry($data['amount'], $treasury->id, $ledger->id,$treasury->cost_center_id);
            $AccountService->updateBalance($treasury->id);
            foreach ($data['accounts'] as $account){
                $account_id = Account::where('code', $account['code'])->value('id');
                $cost_center_id =  isset($account['costCenter'])  ? CostCenter::where('code',$account['costCenter']['code'])->value('id')  : null ;
                $EntryService->createCreditEntry($account['amount'], $account_id, $ledger->id,$cost_center_id, $account['comment'] ?? null);
                $TransactionService->createCI($data['amount'], $ledger->id, $account_id,  $data['due'], $data['description'], $data['responsible'],$data['je_code'],$data['document_no']);
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
            $ledger = $this->store($data['amount'], $data['due'], $data['description'], $data['responsible']);
            $EntryService->createCreditEntry($data['amount'], $treasury->id, $ledger->id,$treasury->cost_center_id);
            $AccountService->updateBalance($treasury->id);
            foreach ($data['accounts'] as $account){
                $account_id = Account::where('code', $account['code'])->value('id');
                $cost_center_id =  isset($account['costCenter'])  ? CostCenter::where('code',$account['costCenter']['code'])->value('id')  : null ;
                $EntryService->createDebitEntry($account['amount'], $account_id, $ledger->id,$cost_center_id, $account['comment'] ?? null);
                $TransactionService->createCO($data['amount'], $ledger->id, $account_id,  $data['due'], $data['description'], $data['responsible'],$data['je_code'],$data['document_no']);
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
            $ledger = $this->store($data['amount'], $data['due'], $data['description'], $data['responsible']);
            foreach ($data['accounts'] as $account){
                $account_id = Account::where('code', $account['code'])->value('id');
                $cost_center_id =  isset($account['costCenter'])  ? CostCenter::where('code',$account['costCenter']['code'])->value('id')  : null ;
                if($account['c_amount']){
                    $EntryService->createCreditEntry($account['c_amount'], $account_id, $ledger->id,$cost_center_id, $account['comment'] ?? null);
                }else{
                    $EntryService->createDebitEntry($account['d_amount'], $account_id, $ledger->id,$cost_center_id, $account['comment'] ?? null);
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
