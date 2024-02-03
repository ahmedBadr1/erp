<?php

namespace App\Services\Inventory;
use App\Models\Inventory\InvTransaction;
use App\Services\MainService;
use App\Services\Purchases\ItemService;
use Exception;
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

    public function search($search)
    {
        $search = trim($search);
        return empty($search) ? InvTransaction::query()
            : InvTransaction::query()->where('amount', 'like', '%' . $search . '%')
                ->orWhere('note', 'like', '%' . $search . '%')
                ->orWhere('type', 'like', '%' . $search . '%');
//                ->orWhereHas('entries', fn($q) => $q->where('description', 'like', '%' . $search . '%'))
//                ->orWhereHas('transaction', fn($q) => $q->where('type', 'like', '%' . $search . '%'))
//                ->orWhereHas('entries.account', fn($q) => $q->where('name', 'like', '%' . $search . '%'));
    }

    public function createInvTransaction(string $type, int $groupId, float $amount, int $from_id, int $to_id = null,
     int $supplier_id = null, int $client_id = null, int $bill_id = null, int $invoice_id = null,
     $due = null, $accepted_at = null, string $note = null, $user_id = null, $paper_ref = null, $system = 1)
    {

        return InvTransaction::create([
            'type' => $type,
            'group_id' => $groupId,
            'amount' => $amount,
            'from_id' => $from_id,
            'to_id' => $to_id,
            'supplier_id' => $supplier_id,
            'client_id' => $client_id,
            'bill_id' => $bill_id,
            'invoice_id' => $invoice_id,
            'note' => $note ?? null,
            'due' => $due ?? now(),
            'accepted_at' => $accepted_at ?? null,
            'paper_ref' => $paper_ref ?? null,
            'responsible' => $user_id ?? auth()->id(),
            'created_by' => auth()->id(),
            'system' => $system,
        ]);
    }


    public function rs(int $groupId, float $amount, int $from_id, int $supplier_id = null, int $bill_id = null,
                                 $due = null, $accepted_at = null, string $note = null, $user_id = null, $paper_ref = null, $system = 1)
    {
        return $this->createInvTransaction(type: 'RS', groupId: $groupId, amount: $amount, from_id: $from_id, supplier_id: $supplier_id, bill_id: $bill_id, due: $due, accepted_at: $accepted_at, note: $note, user_id: $user_id, paper_ref: $paper_ref, system: $system);
    }

    public function io(int $groupId, float $amount, int $from_id, int $client_id = null, int $invoice_id = null,
     $due = null, $accepted_at = null, string $note = null, $user_id = null, $paper_ref = null, $system = 1)
    {
        return $this->createInvTransaction(type: 'IO',groupId: $groupId,amount: $amount,  from_id: $from_id,client_id:  $client_id,invoice_id:  $invoice_id, due: $due, accepted_at: $accepted_at, note: $note, user_id: $user_id, paper_ref: $paper_ref, system: $system);
    }

    public function createIR(int $groupId, float $amount, int $from_id, int $supplier_id = null,  int $bill_id = null,
                                 $due = null, $accepted_at = null, string $note = null, $user_id = null, $paper_ref = null, $system = 1)
    {
        return $this->createInvTransaction(type: 'IR', groupId: $groupId, amount: $amount, from_id: $from_id, supplier_id: $supplier_id, bill_id: $bill_id, due: $due, accepted_at: $accepted_at, note: $note, user_id: $user_id, paper_ref: $paper_ref, system: $system);
    }

    public function createRR(int $groupId, float $amount, int $from_id, int $client_id = null, int $invoice_id = null,
                                 $due = null, $accepted_at = null, string $note = null, $user_id = null, $paper_ref = null, $system = 1)
    {
        return $this->createInvTransaction(type: 'RR',groupId: $groupId,amount: $amount, from_id: $from_id,client_id:  $client_id,invoice_id:  $invoice_id, due: $due, accepted_at: $accepted_at, note: $note, user_id: $user_id, paper_ref: $paper_ref, system: $system);
    }

    public function createIT(int $groupId, float $amount, int $from_id,int$to_id, $due = null, $accepted_at = null, string $note = null, $user_id = null, $paper_ref = null, $system = 1)
    {
        return $this->createInvTransaction(type: 'IT',groupId: $groupId,amount: $amount, from_id: $from_id,to_id:  $to_id,due: $due, accepted_at: $accepted_at, note: $note, user_id: $user_id, paper_ref: $paper_ref, system: $system);
    }

    public function createRT(int $groupId, float $amount, int $from_id,int$to_id, $due = null, $accepted_at = null, string $note = null, $user_id = null, $paper_ref = null, $system = 1)
    {
        return $this->createInvTransaction(type: 'RT',groupId: $groupId,amount: $amount, from_id: $from_id,to_id:  $to_id,due: $due, accepted_at: $accepted_at, note: $note, user_id: $user_id, paper_ref: $paper_ref, system: $system);
    }

    public function createType(string $type,int $groupId,$items, float $amount, int $from_id, int $supplier_id = null,int $bill_id = null, int $client_id = null, int $invoice_id = null,
                                 $due = null, $accepted_at = null, string $note = null, $user_id = null, $paper_ref = null,$discount_rate = 0,$tax_rate = 0, $system = 1)
    {
        switch ($type){
            case 'RS':
                $transaction = $this->rs(groupId: $groupId, amount: $amount, from_id: $from_id, supplier_id: $supplier_id, bill_id: $bill_id, due: $due, accepted_at: $accepted_at, note: $note, user_id: $user_id, paper_ref: $paper_ref, system: $system);
                break;
            case 'IO':
                $transaction = $this->io(groupId: $groupId, amount: $amount, from_id: $from_id, client_id: $client_id, invoice_id: $invoice_id, due: $due, accepted_at: $accepted_at, note: $note, user_id: $user_id, paper_ref: $paper_ref, system: $system);
                break;
            default:
                return false ;
        }
        foreach ($items as $item) {
            (new ItemService())->store(invTransactionId:$transaction->id,   productId: $item['product_id'], quantity: $item['quantity'], price: $item['price'], billId: $bill_id, comment: $item['comment'], userId: $user_id, unitId: $item['unit_id'] ?? null, expireAt: $item['expire_at'] ?? null);
            $sub_cost = $item['price'] - ( $item['price'] * $discount_rate / 100 );
            $cost = $sub_cost + ($sub_cost * $tax_rate / 100 ) ;
                $transaction->products()->attach($item['product_id'], [
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'cost' => (float) $cost ,
            ]);
        }
        $transaction->save();
        return true ;
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

    public function export()
    {
        return Excel::download(new ClientsExport, 'clients_' . date('d-m-Y') . '.xlsx');
    }
}
