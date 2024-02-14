<?php

namespace App\Services\Inventory;

use App\Models\Inventory\InvTransaction;
use App\Models\Inventory\Product;
use App\Models\Inventory\Stock;
use App\Services\MainService;
use Exception;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class InvTransactionService extends MainService
{

    public function all($fields = null, $type = null)
    {
        $data = $fields ?? (new InvTransaction())->getFillable();

        $query = InvTransaction::query();
        if ($type) {
            $query->where('type', $type);
        }

        return $query->get($data);
    }

    public function types()
    {
        $types = [
            'IO' => 'Issue Offering',
            'RS' => 'Receive Supply',
            'IR' => 'Issue Returns',
            'RR' => 'Receive Returns',
            'IT' => 'Issue Transfer',
            'RT' => 'Receive Transfer',
        ];
        return $types ?? InvTransaction::$TYPES;
    }

    public function getPending($type)
    {
        return InvTransaction::with('warehouse', 'secondParty', 'responsible')->where('type', $type)->whereNull('accepted_at')->get();
    }

    public function accept($code)
    {
        $transaction = InvTransaction::with('items')->where('code', $code)->firstOrFail();
        $in = in_array($transaction->type, ['RS', 'IR', 'RT']);
        foreach ($transaction->items as $item) {
            $stock = Stock::firstOrCreate([
                'product_id' => $item->product_id,
                'warehouse_id' => $transaction->warehouse_id,
            ]);

            $product = Product::withSum(['stocks as stocks_balance'=>fn($q)=>$q->where('balance','!=',0)], 'balance')->find($item->product_id);
            if ($in) {
                if ($stock->balance) {
                    $balance = $stock->balance + $item->quantity;
                } else {
                    $balance = $item->quantity;
                }

                $avg_cost = ($product->avg_cost * $product->stocks_balance + $item->quantity * $item->price) / ($product->stocks_balance + $item->quantity);

            } else {
                if ($stock->balance <  $item->quantity){
                    throw new Exception('Not Balance Enough');
                }elseif($stock->balance == $item->quantity){
                    $avg_cost = 0 ;
                }
                $balance = $stock->balance - $item->quantity;
            }
            $stock->update([
                'balance' => $balance
            ]);
            $product->update(['avg_cost' => $avg_cost]);


            (new ItemHistoryService())->store(warehouseId: $transaction->warehouse_id, invTransactionId: $transaction->id, productId: $item->product_id,
                quantity: $item->quantity, price: $item->price, second_party_id: $transaction->second_party_id, second_party_type: $transaction->second_party_type, in: $in, balance: $balance, avg_cost: $avg_cost ?? null, userId: auth('api')->id());

        }

        return $transaction->update(['accepted_at' => now()]);
    }

    public function search($search)
    {
        $search = trim($search);
        return empty($search) ? InvTransaction::query()
            : InvTransaction::query()->where('code', 'like', '%' . $search . '%')
                ->orWhere('paper_ref', 'like', '%' . $search . '%');
//                ->orWhereHas('entries', fn($q) => $q->where('description', 'like', '%' . $search . '%'))
//                ->orWhereHas('transaction', fn($q) => $q->where('type', 'like', '%' . $search . '%'))
//                ->orWhereHas('entries.account', fn($q) => $q->where('name', 'like', '%' . $search . '%'));
    }

    public function createInvTransaction(string $type, int $groupId, float $amount, int $warehouse_id, $second_party_id, $second_party_type, int $bill_id = null,

                                                $due = null, $accepted_at = null, string $note = null, $user_id = null, $paper_ref = null, $system = 1)
    {

        return InvTransaction::create([
            'type' => $type,
            'group_id' => $groupId,
            'amount' => $amount,
            'warehouse_id' => $warehouse_id,
            'second_party_id' => $second_party_id,
            'second_party_type' => $second_party_type,
            'bill_id' => $bill_id,
            'note' => $note ?? null,
            'due' => $due ?? now(),
            'accepted_at' => $accepted_at ?? null,
            'paper_ref' => $paper_ref ?? null,
            'responsible' => $user_id ?? auth()->id(),
            'created_by' => auth()->id(),
            'system' => $system,
        ]);
    }


    public function rs(int $groupId, float $amount, int $warehouse_id, int $second_party_id, $second_party_type, int $bill_id = null,
                           $due = null, $accepted_at = null, string $note = null, $user_id = null, $paper_ref = null, $system = 1)
    {
        return $this->createInvTransaction(type: 'RS', groupId: $groupId, amount: $amount, warehouse_id: $warehouse_id, second_party_id: $second_party_id, second_party_type: $second_party_type, bill_id: $bill_id, due: $due, accepted_at: $accepted_at, note: $note, user_id: $user_id, paper_ref: $paper_ref, system: $system);
    }

    public function io(int $groupId, float $amount, int $warehouse_id, int $second_party_id, $second_party_type, int $bill_id = null,
                           $due = null, $accepted_at = null, string $note = null, $user_id = null, $paper_ref = null, $system = 1)
    {
        return $this->createInvTransaction(type: 'IO', groupId: $groupId, amount: $amount, warehouse_id: $warehouse_id, second_party_id: $second_party_id, second_party_type: $second_party_type, bill_id: $bill_id, due: $due, accepted_at: $accepted_at, note: $note, user_id: $user_id, paper_ref: $paper_ref, system: $system);
    }

    public function createIR(int $groupId, float $amount, int $warehouse_id, int $second_party_id, $second_party_type, int $bill_id = null,
                                 $due = null, $accepted_at = null, string $note = null, $user_id = null, $paper_ref = null, $system = 1)
    {
        return $this->createInvTransaction(type: 'IR', groupId: $groupId, amount: $amount, warehouse_id: $warehouse_id, second_party_id: $second_party_id, second_party_type: $second_party_type, bill_id: $bill_id, due: $due, accepted_at: $accepted_at, note: $note, user_id: $user_id, paper_ref: $paper_ref, system: $system);
    }

    public function createRR(int $groupId, float $amount, int $warehouse_id, int $second_party_id, $second_party_type, nt $bill_id = null,
                                 $due = null, $accepted_at = null, string $note = null, $user_id = null, $paper_ref = null, $system = 1)
    {
        return $this->createInvTransaction(type: 'RR', groupId: $groupId, amount: $amount, warehouse_id: $warehouse_id, second_party_id: $second_party_id, second_party_type: $second_party_type, bill_id: $bill_id, due: $due, accepted_at: $accepted_at, note: $note, user_id: $user_id, paper_ref: $paper_ref, system: $system);
    }

    public function createIT(int $groupId, float $amount, int $warehouse_id, int $second_party_id, $due = null, $accepted_at = null, string $note = null, $user_id = null, $paper_ref = null, $system = 1)
    {
        return $this->createInvTransaction(type: 'IT', groupId: $groupId, amount: $amount, warehouse_id: $warehouse_id, second_party_id: $second_party_id, second_party_type: $second_party_type, due: $due, accepted_at: $accepted_at, note: $note, user_id: $user_id, paper_ref: $paper_ref, system: $system);
    }

    public function createRT(int $groupId, float $amount, int $warehouse_id, int $second_party_id, $second_party_type, $due = null, $accepted_at = null, string $note = null, $user_id = null, $paper_ref = null, $system = 1)
    {
        return $this->createInvTransaction(type: 'RT', groupId: $groupId, amount: $amount, warehouse_id: $warehouse_id, second_party_id: $second_party_id, second_party_type: $second_party_type, due: $due, accepted_at: $accepted_at, note: $note, user_id: $user_id, paper_ref: $paper_ref, system: $system);
    }

    public function createType(string $type, int $groupId, $items, float $amount, int $warehouse_id, int $second_party_id = null, int $bill_id = null, string $second_party_type = null, int $invoice_id = null,
                                      $due = null, $accepted_at = null, string $note = null, $user_id = null, $paper_ref = null, $discount_rate = 0, $system = 1)
    {
        switch ($type) {
            case 'RS':
                $transaction = $this->rs(groupId: $groupId, amount: $amount, warehouse_id: $warehouse_id, second_party_id: $second_party_id, second_party_type: $second_party_type, bill_id: $bill_id, due: $due, accepted_at: $accepted_at, note: $note, user_id: $user_id, paper_ref: $paper_ref, system: $system);
                break;
            case 'IO':
                $transaction = $this->io(groupId: $groupId, amount: $amount, warehouse_id: $warehouse_id, second_party_id: $second_party_id, second_party_type: $second_party_type, bill_id: $bill_id, due: $due, accepted_at: $accepted_at, note: $note, user_id: $user_id, paper_ref: $paper_ref, system: $system);
                break;
            default:
                return false;
        }
//        foreach ($items as $item) {
//            $cost = $item['price'] - ($item['price'] * $discount_rate / 100);
//            (new InvTransactionItemService())->store(warehouseId: $warehouse_id, invTransactionId: $transaction->id, productId: $item['product_id'], quantity: $item['quantity'], price: $cost, unitId: $item['unit_id'] ?? null);
//        }
//        $transaction->save();
        return $transaction;
    }


    public function update($transaction, array $data)
    {
        try {
            $transaction->update([...$data,
                'edited_by' => auth()->id(),
            ]);
            return $transaction;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function destroy($client)
    {
        if ($client->projects_count > 0) {
            return 0;
        } else {
            $client->delete();
        }
    }


    public function orders(array $data = null)
    {
        $columns = $data['columns'];

        $dataset = [];

        $query = InvTransaction::query();
//        $query->locked(0);
//        $query->posted($data['posted']);
//        $dataset[] = $data['posted'] ? 'Posted' : 'Un Posted';


        $query->with(['warehouse', 'responsible']);

//        if (isset($columns['responsible']) && $columns['responsible']) {

//        }

        if (isset($columns['edited_by']) && $columns['edited_by']) {
            $query->with('editor');
        }
        if (isset($columns['second_party']) && $columns['second_party']) {
            $query->with('secondParty');
        }


        if (!empty($data['codes'])) {
            $query->where(fn($q) => $q->whereIn('code', $data['codes']));
            foreach ($data['codes'] as $code) {
                $dataset[] = 'Code ' . $code;
            }
        }

        if (!empty($data['orderTypes'])) {
            $query->whereIn('type', $data['orderTypes']);
            foreach ($data['orderTypes'] as $type) {
                $dataset[] = 'Type ' . $type;
            }
        } else {
            $dataset[] = 'All Types';
        }

        if (!empty($data['sellers'])) {
//            $query->whereIn('responsible_id', $data['sellers']);
//            foreach ($data['sellers'] as $type) {
//                $dataset[] = 'sellers ' . $type->name;
//            }
        }


        if (!empty($data['warehouses'])) {
            $query->where(fn($q) => $q->whereIn('warehouse_id', [1])->orWhereIn('second_party_id', [1]));
        }

        if (!empty($data['suppliers'])) {
            $query->whereIn('supplier_id', $data['suppliers']);
        }

        if (!empty($data['clients'])) {
            $query->whereIn('client_id', $data['clients']);
        }

        if (!empty($data['users'])) {
            $query->whereIn('created_by', $data['users']);
//            foreach ($data['sellers'] as $type) {
//                $dataset[] = 'sellers ' . $type->name;
//            }
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

    public function export()
    {
        return Excel::download(new ClientsExport, 'clients_' . date('d-m-Y') . '.xlsx');
    }
}
