<?php

namespace App\Services\Purchases;

use App\Exports\Inventory\ProductsExport;
use App\Exports\UsersExport;
use App\Models\Accounting\Account;
use App\Models\Accounting\Currency;
use App\Models\Inventory\Product;
use App\Models\Purchases\Bill;
use App\Models\System\Status;
use App\Services\Accounting\LedgerService;
use App\Services\ClientsExport;
use App\Services\Inventory\InvTransactionService;
use App\Services\MainService;
use App\Services\System\ModelGroupService;
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
                ->orWhere('paper_ref', 'like', '%' . $search . '%');
//                ->orWhereHas('category', fn($q) => $q->where('name', 'like', '%' . $search . '%'));
    }

    /**
     * @throws Exception
     */
    public function store(array $data, $id = null)
    {
        if (!$id) {
            $type = $data['type'];
            $group = (new ModelGroupService)->store();
            $warehouseAcc = Account::whereHas('warehouse', fn($q) => $q->whereId($data['warehouse_id']))->first();
            $secondPartyAcc = match ($data['second_party_type']) {
                'supplier' => Account::whereHas('supplier', fn($q) => $q->whereId($data['second_party_id']))->first(),
                'client' => Account::whereHas('client', fn($q) => $q->whereId($data['second_party_id']))->first(),
                default => throw new \RuntimeException('No Type Matches'),
            };
            $paidStatus = Status::where('name', 'paid')->value('id');
            $currency = Currency::find($data['currency_id']);

            $bill = Bill::create([
                'type' => $type,
                'date' => $data['date'],
                'deliver_at' => $data['deliver_at'],
                'paper_ref' => $data['paper_ref'],
                'treasury_id' => $data['treasury_id'],
                'warehouse_id' => $data['warehouse_id'],
                'second_party_id' => $data['second_party_id'],
                'second_party_type' => $data['second_party_type'],
                'responsible_id' => $data['responsible_id'] ?? auth('api')->id(),
                'currency_id' => $data['currency_id'],
                'ex_rate' => $data['ex_rate'] ?? $currency->ex_rate ?? 1,
                'currency_total' => $data['currency_total'] ?? ((float)$data['total'] * $currency->ex_rate),
                'gross_total' => (float)$data['gross_total'],
                'discount' => (float)$data['discount'],
                'sub_total' => (float)$data['sub_total'],
                'tax_total' => (float)$data['tax_total'],
                'total' => $data['total'],
                'note' => $data['note'],
                'created_by' => auth()->id(),
                'group_id' => $group->id,
                'status_id' => $paidStatus,
                'tax_exclusive' => 0,
                'tax_inclusive' => 0,
            ]);
            if ($type === 'SO') {
                $items = collect($data['items']);
                $total_cost = $items->sum(function ($item) {
                        return $item['quantity'];
                    }) * $items->avg(function ($item) {
                        return $item['avg_cost'] ?? Product::whereId($item['product_id'])->value('avg_cost') ?? throw new \RuntimeException('Avg Cost Zero');
                    });
            }


            $transType = match ($type) {
                'PO' => 'RS',
                'SO' => 'IO',
                default => throw new \RuntimeException('No Type Matches'),
            };
            $transaction = (new InvTransactionService())->createType(type: $transType, groupId: $group->id, items: $data['items'], amount: $bill->gross_total, warehouse_id: $data['warehouse_id'], second_party_id: $data['second_party_id'], bill_id: $bill->id, second_party_type: $data['second_party_type'], due: $data['date'], note: $data['note'], user_id: $data['responsible_id'] ?? null, paper_ref: $data['paper_ref'], discount_rate: $data['discount_rate'], system: 1);

            foreach ($data['items'] as $item) {
                $cost = $item['price'] - ($item['price'] * $data['discount_rate'] / 100);
                (new BillItemService())->store(billId: $bill->id, productId: $item['product_id'], quantity: $item['quantity'], price: $item['price'], cost: $cost, tax_value: $item['tax_value'] ?? 0, comment: $item['comment'], unitId: $item['unit_id'] ?? null, expireAt: $item['expire_at'] ?? null, invTransactionId: $transaction->id);
            }
        } else {
            throw new \RuntimeException('No Edit Yet');
        }


        if ($type === 'PO') {

            (new LedgerService())->cashout($group->id, $data['treasury_id'], $secondPartyAcc, $bill->total, $data['currency_id'], $data['date'] ?? now(), $data['note'] ?? null, $data['paper_ref'] ?? null, $data['responsible_id'] ?? null);
            (new LedgerService())->PI(type: 'PI', groupId: $group->id, warehouseAcc: $warehouseAcc, supplierAcc: $secondPartyAcc, currencyId: $data['currency_id'], date: $data['date'], total: $bill->total, subTotal: $bill->sub_total, grossTotal: $bill->gross_total, tax: $bill->tax_total, discount: $bill->discount, note: $data['note'] ?? null, paperRef: $data['paper_ref'] ?? null, responsible: $data['responsible_id'] ?? null);

        } elseif ($type === 'SO') {

            (new LedgerService())->cashin($group->id, $data['treasury_id'], $secondPartyAcc, $bill->total, $data['currency_id'], $data['date'] ?? now(), $data['note'] ?? null, $data['paper_ref'] ?? null, $data['responsible_id'] ?? null);
            (new LedgerService())->SI(type: 'PI', groupId: $group->id, warehouseAcc: $warehouseAcc, clientAcc: $secondPartyAcc, currencyId: $data['currency_id'], date: $data['date'], total: $bill->total, subTotal: $bill->sub_total, grossTotal: $bill->gross_total, tax: $bill->tax_total, discount: $bill->discount, note: $data['note'] ?? null, paperRef: $data['paper_ref'] ?? null, responsible: $data['responsible_id'] ?? null);
            (new LedgerService())->COGS(groupId: $group->id, warehouseAcc: $warehouseAcc, amount: $total_cost, currencyId: $data['currency_id'], date: $data['date'], note: $data['note'] ?? null, paperRef: $data['paper_ref'] ?? null, responsible: $data['responsible_id'] ?? null);

        } else {
            throw new \RuntimeException('No Type Matches');

        }

        (new InvTransactionService())->accept($transaction->code);
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

    public
    function destroy($Supplier)
    {
        if ($Supplier->items_count > 0) {
            return 0;
        } else {
            $Supplier->delete();
        }
    }

    public
    function export($collection = null)
    {
        return Excel::download(new ProductsExport($collection), 'products_' . date('d-m-Y') . '.xlsx');
    }
}
