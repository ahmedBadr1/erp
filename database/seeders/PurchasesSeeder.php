<?php

namespace Database\Seeders;

use App\Models\Inventory\InvTransaction;
use App\Models\Inventory\Item;
use App\Models\Inventory\ItemHistory;
use App\Models\Inventory\Product;
use App\Models\Inventory\Stock;
use App\Models\Purchases\Bill;
use App\Models\Purchases\BillItem;
use App\Models\System\ModelGroup;
use Illuminate\Database\Seeder;

class PurchasesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
//        Supplier::factory(2)->create(); // ACCOUNT WILL CREATE IT
//        Bill::factory(10)->create()->each(function ($bill){
//            $bill->items()->saveMany(Item::factory(rand(2,5))->create());
//        });
    }

    /**
     * @throws RandomException
     */
    public static function seedType($type, $warehouse, $supplier, $treasury)
    {
        if ($type === 'PO') {
            $group = ModelGroup::create();

            $items = BillItem::factory(random_int(2, 5))->create();

            $gross_total = 0;
            $tax_total = 0;

            $sub_total = 0;
            $total = 0;
            $discountRate = random_int(1, 30);
            foreach ($items as $item) {
                $gross_total += $item->sub_total;
                $tax_total += $item->tax_value;
            }
            $discount = $gross_total * $discountRate / 100;
            $sub_total = $gross_total - $discount;
            $total = $sub_total + $tax_total;

            $bill = Bill::factory()->create([
                'type' => $type,
                'gross_total' => $gross_total,
                'discount' => $discount,
                'sub_total' => $sub_total,
                'tax_total' => $tax_total,
                'total' => $total,
                'currency_id' => 1,
                'ex_rate' => 1,
                'treasury_id' => $treasury->id,
                'warehouse_id' => $warehouse->id,
                'second_party_type' => 'supplier',
                'second_party_id' => $supplier->id,
                'group_id' => $group->id,
            ]);

            $transaction = InvTransaction::factory()->create([
                'bill_id' => $bill->id,
                'type' => 'RS',
                'amount' => $gross_total,
                'warehouse_id' => $warehouse->id,
                'second_party_type' => 'supplier',
                'second_party_id' => $supplier->id,
                'group_id' => $group->id,
                'accepted_at' => fake()->boolean(80) ? now() : null
            ]);


            foreach ($items as $item) {
//                $cost = $item->price - ($item->price * $discountRate / 100);
                $item->update([
                    'bill_id' => $bill->id,
                    'inv_transaction_id' => $transaction->id,
                    'cost' => $item->price,
                ]);
//                ItemHistory::factory()->create([
//                    'quantity' => $item->quantity,
//                    'price' => $cost,
//                    'product_id' => $item->product_id,
////                    'warehouse_id' => $warehouse->id,
//                    'inv_transaction_id' => $transaction->id,
//                    'accepted' => (bool)$transaction->accepted_at,
//                ]);
            }

            AccountingSeeder::seedType('CO', $group->id, $treasury, $supplier->account, $bill->total);
            AccountingSeeder::seedPI(type: 'PI', groupId: $group->id, warehouseId: $warehouse->account->id, supplierId: $supplier->account->id,
                total: $bill->total, subTotal: $bill->sub_total, grossTotal: $bill->gross_total, tax: $bill->tax_total, discount: $bill->discount);


            if (isset($transaction->accepted_at)) {
                foreach ($transaction->items()->get() as $item) {
                    $stock  = Stock::firstOrCreate([
                        'product_id' => $item->product_id,
                        'warehouse_id' => $transaction->warehouse_id,
                    ]);
//                    $product = Product::with(["stock" => fn($q) => $q->where('warehouse_id', $transaction->warehouse_id)])->find($item->product_id);
                    $product = Product::withSum('stocks as stocks_balance','balance')->find($item->product_id);

                    if ($stock->balance) {
                        $balance = $stock->balance + $item->quantity;
                    } else {
                        $balance = $item->quantity;
                    }
                    $stock->update([
                        'balance' => $balance
                    ]);

                    $avg_cost = ( $product->avg_cost * $product->stocks_balance + $item->quantity * $item->price ) / ($product->stocks_balance +  $item->quantity);


                    $product->update(['avg_cost'=>$avg_cost]);

                    ItemHistory::factory()->create([
                        'quantity' => $item->quantity,
                        'price' => $item->price,
                        'avg_cost' => $avg_cost ,
                        'balance' => $balance,
                        'product_id' => $item->product_id,
                        'warehouse_id' => $warehouse->id,
                        'inv_transaction_id' => $transaction->id,
                        'second_party_type' => 'supplier',
                        'second_party_id' => $supplier->id,
                        'in' => true,
                    ]);


                }
            }

        }
    }
}
