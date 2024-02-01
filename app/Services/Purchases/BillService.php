<?php

namespace App\Services\Purchases;

use App\Exports\Inventory\ProductsExport;
use App\Exports\UsersExport;
use App\Models\Accounting\Account;
use App\Models\Purchases\Bill;
use App\Models\System\Status;
use App\Services\Accounting\LedgerService;
use App\Services\Accounting\TransactionGroupService;
use App\Services\Accounting\TransactionService;
use App\Services\ClientsExport;
use App\Services\MainService;
use Exception;
use Illuminate\Support\Facades\DB;
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
        DB::beginTransaction();
        try {
            if (!$id) {

                $group = (new TransactionGroupService)->store();
                $warehouseAcc = Account::whereHas('warehouse',  fn($q)=>$q->whereId($data['warehouse_id']))->first();
                $supplierAcc = Account::whereHas('supplier', fn($q)=>$q->whereId($data['supplier_id']))->first();
                $paidStatus = Status::where('name','paid')->value('id');

                $bill = Bill::create([
                    'date' => $data['date'],
                    'deliver_at' => $data['deliver_at'],
                    'paper_ref' => $data['paper_ref'],
                    'warehouse_id' => $data['warehouse_id'],
                    'supplier_id' => $data['supplier_id'],
                    'responsible_id' => $data['responsible_id'],
                    'currency_id' => $data['currency_id'],
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

                $rs = (new TransactionService)->createRS($group->id,$bill->gross_total,$warehouseAcc->id, $supplierAcc->id, $data['date'], $data['note'], $data['responsible_id'], $data['paper_ref'], null, 1);
                foreach ($data['items'] as $item) {
                    (new ItemService())->store($bill->id, $item['product_id'], $item['quantity'], $item['price'], $item['comment'], $item['unit_id'] ?? null, $item['expire_at'] ?? null);
                    $rs->products()->attach($item['product_id'], [
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                    ]);
                }
                $rs->save();

                (new LedgerService())->cashout($group->id,$data['treasury_id'],$supplierAcc,$bill->total,$data['currency_id'],$data['date'] ?? now,$data['note'] ?? null,$data['paper_ref'] ?? null,$data['responsible_id']);
                (new LedgerService())->PI( 'PI',$group->id,$warehouseAcc,$supplierAcc,$data['currency_id'],$data['date'],$bill->total,$bill->sub_total,$bill->tax_total,$data['note'] ?? null,$data['paper_ref'] ?? null,$data['responsible_id']);

            } else {
                // check if has posted docs
                //  'edited_by' => auth()->id(),
                // delete old items
            }

//            $transaction =  Transaction::factory()->create([
//                'type_group' => TransactionTypeGroups::INV,
//                'type' => 'RS',
//                "first_party_id" => $warehouseId,
//                "second_party_id" => $supplierId,
//                'amount' => $bill->total,
//                'group_id' => $group->id,
//            ]);
//
//            $items =  Item::factory(rand(2,5))->create([
//                'bill_id' => $bill->id,
//                'warehouse_id' => $warehouseId ,
//                'transaction_id' =>$transaction->id ,
//            ]);
//
//            AccountingSeeder::seedType('CO',$group->id,$treasuryId,$supplierId,$bill->total);
//            AccountingSeeder::seedPI('PI',$group->id,$warehouseId,$supplierId,$bill->total,$bill->sub_total,$bill->tax_total);

        } catch (Exception $e) {
            DB::rollBack();
            return $e;
        }
        DB::commit();
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
