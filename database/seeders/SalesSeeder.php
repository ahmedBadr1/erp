<?php

namespace Database\Seeders;

use App\Models\Inventory\InvTransaction;
use App\Models\Inventory\Item;
use App\Models\Inventory\ItemHistory;
use App\Models\Inventory\Product;
use App\Models\Inventory\Stock;
use App\Models\Purchases\Bill;
use App\Models\Purchases\BillItem;
use App\Models\Sales\InvoiceItem;
use App\Models\System\ModelGroup;
use Illuminate\Database\Seeder;

class SalesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
//        \App\Models\Sales\Client::factory(3)->create(); // ACCOUNT WILL CREATE IT
        \App\Models\Crm\Action::factory(10)->create();
    }


    public static function seedType($type, $warehouse, $client, $treasury)
    {
        if ($type === 'SO') {
            $group = ModelGroup::create();
//            $items = collect([]);
//            for ($i=1 ;$i <= random_int(2, 5) ; $i ++){
//                $product = Item::where
//            }
//
//            $price = fake()->numberBetween(5,100);
//            $quantity =  fake()->numberBetween(1,10);


            $items = BillItem::factory(random_int(2, 5))->create([
               'product_id' => Product::whereHas('stocks',fn($q)=>$q->where('warehouse_id',$warehouse->id)->where('balance','>=',10))->get()->random()->id
            ]);

            $gross_total = 0;
            $tax_total = 0;

            $sub_total = 0;
            $total = 0;
            $discountRate =  random_int(1, 30);
            foreach ($items as $item) {
                $gross_total += $item->sub_total;
                $tax_total += $item->tax_value;
            }
            $discount = $gross_total * $discountRate / 100;
            $sub_total = $gross_total - $discount;
            $total = $sub_total + $tax_total;

            $bill = Bill::factory()->create([
                'type' => $type ,
                'gross_total' => $gross_total,
                'discount' => $discount,
                'sub_total' => $sub_total,
                'tax_total' => $tax_total,
                'total' => $total,
                'currency_id' => 1,
                'ex_rate' => 1,
                'treasury_id' => $treasury->id,
                'warehouse_id' => $warehouse->id,
                'second_party_type' => 'client',
                'second_party_id' => $client->id,
                'group_id' => $group->id,
            ]);

            $transaction = InvTransaction::factory()->create([
                'type' => 'IO',
                'amount' => $sub_total,
                'warehouse_id' => $warehouse->id,
                'second_party_type' => 'client',
                'second_party_id' => $client->id,
                'group_id' => $group->id,
                'accepted_at' => fake()->boolean(80) ? now() : null
            ]);

             $total_cost = 0 ;
            foreach ($items as $item) {
                $cost = $item->price - ($item->price * $discountRate / 100);
                $avg_cost = Product::whereId($item->product_id)->value('avg_cost');
                $total_cost += $avg_cost ;
                $item->update([
                    'bill_id' => $bill->id,
                    'cost' => $cost,
                    'avg_cost' =>$avg_cost
                ]);
                Item::factory()->create([
                    'quantity' => $item->quantity,
                    'price' => $cost ,
                    'product_id' => $item->product_id,
//                    'warehouse_id' => $warehouse->id,
                    'inv_transaction_id' => $transaction->id,
                    'accepted' => (bool) $transaction->accepted_at,
                ]);
            }

            AccountingSeeder::seedType('CI', $group->id, $treasury, $client->account, $bill->total);
            AccountingSeeder::seedSI(type: 'SI', groupId: $group->id, clientAccId: $client->account->id,
                total: $bill->total, subTotal: $bill->sub_total, grossTotal: $bill->gross_total, tax: $bill->tax_total, discount: $bill->discount);
            AccountingSeeder::seedCOGS('COGS', $group->id, $warehouse->account, $total_cost);



            if (isset($transaction->accepted_at)) {
                foreach ($transaction->items()->get() as $item){
                    $stock  = Stock::firstOrCreate([
                        'product_id' => $item->product_id,
                        'warehouse_id' => $transaction->warehouse_id,
                    ]);
                    if ($stock->balance >= $item->quantity ) {
                        $balance = $stock->balance - $item->quantity;
                    }else{
                       return ;
                    }
                    $stock->update([
                        'balance' => $balance
                    ]);

                    ItemHistory::factory()->create([
                        'quantity' => $item->quantity,
                        'price' => $item->price ,
                        'balance' => $balance ,
                        'product_id' => $item->product_id,
                        'warehouse_id' => $warehouse->id,
                        'inv_transaction_id' => $transaction->id,
                        'second_party_type' => 'client',
                        'second_party_id' => $client->id,
                        'in' => false ,
                    ]);

                }
            }
//            dd($items->pluck('id'));
        }
    }
}
