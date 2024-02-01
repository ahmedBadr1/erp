<?php

namespace Database\Seeders;

use App\Enums\TransactionTypeGroups;
use App\Models\Accounting\Transaction;
use App\Models\Accounting\TransactionGroup;
use App\Models\Inventory\Item;
use App\Models\Purchases\Bill;
use App\Models\Purchases\Supplier;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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

    public static function seedType($type,$warehouseId,$supplierId,$treasuryId)
    {
        if ($type === 'PO'){
            $group = TransactionGroup::create();

           $bill = Bill::factory()->create([
                'warehouse_id' => $warehouseId,
                'supplier_id' => $supplierId,
               'group_id' => $group->id,
            ]);
           $transaction =  Transaction::factory()->create([
               'type_group' => TransactionTypeGroups::INV,
                'type' => 'RS',
                "first_party_id" => $warehouseId,
                "second_party_id" => $supplierId,
                'amount' => $bill->total,
               'group_id' => $group->id,
            ]);

            $items =  Item::factory(rand(2,5))->create([
                'bill_id' => $bill->id,
                'warehouse_id' => $warehouseId ,
            ]);

            AccountingSeeder::seedType('CO',$group->id,$treasuryId,$supplierId,$bill->total);
            AccountingSeeder::seedPI('PI',$group->id,$warehouseId,$supplierId,$bill->total,$bill->sub_total,$bill->tax_total);

//            dd($items->pluck('id'));
        }
    }
}
