<?php

namespace App\Services\Purchases;

use App\Exports\Inventory\ProductsExport;
use App\Exports\UsersExport;
use App\Models\Accounting\Account;
use App\Models\Accounting\Currency;
use App\Models\Purchases\Bill;
use App\Models\System\Status;
use App\Services\Accounting\LedgerService;
use App\Services\Accounting\TransactionGroupService;
use App\Services\Accounting\TransactionService;
use App\Services\ClientsExport;
use App\Services\Inventory\InvTransactionService;
use App\Services\MainService;
use Exception;
use Maatwebsite\Excel\Facades\Excel;

class BillService extends MainService
{

    public function all($fields = null, $active = 1)
    {
        $data = $fields ?? (new Bill())->getFillable();

        return Bill::active($active)->get($data);
    }


    public function search($search)
    {
        $search = trim($search);
        return empty($search) ? Bill::query()
            : Bill::query()->where('code', 'like', '%' . $search . '%')
                ->orWhere('number', 'like', '%' . $search . '%');
//                ->orWhereHas('category', fn($q) => $q->where('name', 'like', '%' . $search . '%'));
    }

    public function store(array $data, $id = null)
    {
        if (!$id) {

            $group = (new TransactionGroupService)->store();
            $warehouseAcc = Account::whereHas('warehouse', fn($q) => $q->whereId($data['warehouse_id']))->first();
            $supplierAcc = Account::whereHas('supplier', fn($q) => $q->whereId($data['supplier_id']))->first();
            $paidStatus = Status::where('name', 'paid')->value('id');
            $currency = Currency::find($data['currency_id']);

            $bill = Bill::create([
                'date' => $data['date'],
                'deliver_at' => $data['deliver_at'],
                'paper_ref' => $data['paper_ref'],
                'treasury_id' => $data['treasury_id'],
                'warehouse_id' => $data['warehouse_id'],
                'supplier_id' => $data['supplier_id'],
                'responsible_id' => $data['responsible_id'],
                'currency_id' => $data['currency_id'],
                'ex_rate' => $data['ex_rate'] ?? $currency->ex_rate ?? 1,
                'currency_total' => $data['currency_total'] ?? ((float)$data['total'] * $currency->ex_rate),

                'gross_total' => (float)$data['gross_total'],
                'discount' => (float)$data['discount'],
                'sub_total' => (float)$data['sub_total'],
                'tax_total' => (float)$data['tax_total'] ?? null,
                'total' => $data['total'],
                'note' => $data['note'],
                'created_by' => auth()->id(),
                'group_id' => $group->id,
                'status_id' => $paidStatus,
                'tax_exclusive' => 0,
                'tax_inclusive' => 0,
            ]);

            (new InvTransactionService())->createType(type: 'RS', groupId: $group->id, items: $data['items'], amount: $bill->gross_total, from_id: $data['warehouse_id'], supplier_id: $data['supplier_id'], bill_id: $bill->id, due: $data['date'], note: $data['note'], user_id: $data['responsible_id'], paper_ref: $data['paper_ref'], system: 1);


            (new LedgerService())->cashout($group->id, $data['treasury_id'], $supplierAcc, $bill->total, $data['currency_id'], $data['date'] ?? now, $data['note'] ?? null, $data['paper_ref'] ?? null, $data['responsible_id']);
            (new LedgerService())->PI('PI', $group->id, $warehouseAcc, $supplierAcc, $data['currency_id'], $data['date'], $bill->total, $bill->sub_total, $bill->tax_total, $data['note'] ?? null, $data['paper_ref'] ?? null, $data['responsible_id']);

        } else {
            // check if has posted docs
            //  'edited_by' => auth()->id(),
            // delete old items
        }
        return false;
    }

    public function update($Supplier, array $data)
    {
        try {
            $Supplier->update($data);
            return $Supplier;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function destroy($Supplier)
    {
        if ($Supplier->items_count > 0) {
            return 0;
        } else {
            $Supplier->delete();
        }
    }

    public function export($collection = null)
    {
        return Excel::download(new ProductsExport($collection), 'products_' . date('d-m-Y') . '.xlsx');
    }
}
