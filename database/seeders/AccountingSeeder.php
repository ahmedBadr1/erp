<?php

namespace Database\Seeders;

use App\Enums\TransactionTypeGroups;
use App\Models\Accounting\Account;
use App\Models\Accounting\AccountType;
use App\Models\Accounting\CostCenter;
use App\Models\Accounting\CostCenterNode;
use App\Models\Accounting\Currency;
use App\Models\Accounting\Entry;
use App\Models\Accounting\Ledger;
use App\Models\Accounting\Node;
use App\Models\Accounting\Tax;
use App\Models\Accounting\Transaction;
use App\Models\Accounting\TransactionGroup;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AccountingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {


        $this->seedTypes();

        $this->seedNodes();

        $this->seedCurrencies();

        CostCenterNode::factory()->create(['name' => 'مركز تكلفة رئيسي', 'slug' => 'main_cost_center_node']);

        CostCenter::factory()->create(['name' => 'مركز تكلفة رئيسي', 'system' => 1]);
        CostCenter::factory(3)->create();

        $this->seedAccounts();

//        Account::factory(100)->create();

        $TaxAccount = Account::whereHas('type', fn($q) => $q->where('code', 'TAX'))->value('id');
        Tax::factory()->create([
            'name' => 'ضريبة الضيمة المضافة',
            'code' => 'VAT',
            'rate' => '14',
            'scope' => ['any'],
            'account_id' => $TaxAccount,
            'active' => true
        ]);
//         Ledger::factory()->create(['amount' => 100]);

//        $count = 10;
//        $types = Transaction::$TYPES;
//        $accountsIds = Account::pluck('id')->toArray();
//
//        for ($i = 0; $i <= $count; $i++) {
//            $amount = rand(100, 1000);
//            Ledger::factory()->create(['amount' => $amount])->each(function ($ledger) use ($amount, $types, $accountsIds) {
//                $ledger->entries()->saveMany(Entry::factory(rand(2, 5))->create(['amount' => $ledger->amount]));
//                $ledger->transactions()->save(Transaction::factory()->create([
//                    'amount' => $ledger->amount,
//                ]));
//                //,'account_id'=>$accountsIds[array_rand($accountsIds)
//            });
//        }


    }

    public static function seed()
    {
        $ledger = Ledger::factory()->create(['amount' => rand(100, 1000), 'currency_id' => 1, 'ex_rate' => 1]);
        Entry::factory()->create(['amount' => $ledger->amount, 'ledger_id' => $ledger->id, 'credit' => 1]);
        Entry::factory()->create(['amount' => $ledger->amount, 'ledger_id' => $ledger->id, 'credit' => 0]);
        Transaction::factory(rand(1, 3))->create([
            'amount' => $ledger->amount,
            'ledger_id' => $ledger->id,
        ]);
        unset($ledger);
    }

    public static function seedType($type, $groupId , $treasuryId,$secondPartyId= null, $amount = null)
    {
        if (!$groupId){
            $groupId = TransactionGroup::create()->id;
        }
        if (!$amount) {
            $amount = rand(100, 1000);
        }

        $ledger = Ledger::factory()->create(['group_id'=>$groupId,'amount' => $amount, 'currency_id' => 1, 'ex_rate' => 1 ]);
        if ($type == 'CI') {
            if (!$secondPartyId){
                $secondPartyId = Account::whereHas('type',fn($q)=>$q->where('code','AR'))->get()->random()->id;
            }
            Entry::factory()->create(['amount' => $ledger->amount, 'ledger_id' => $ledger->id, 'account_id' => $treasuryId, 'credit' => 0]);
            Entry::factory()->create(['amount' => $ledger->amount, 'ledger_id' => $ledger->id,'account_id'=>$secondPartyId, 'credit' => 1]);

        } elseif ($type == 'CO') {
            if (!$secondPartyId){
                $secondPartyId = Account::whereHas('type',fn($q)=>$q->where('code','AP'))->get()->random()->id;
            }
            Entry::factory()->create(['amount' => $ledger->amount, 'ledger_id' => $ledger->id,'account_id'=>$secondPartyId, 'credit' => 0]);
            Entry::factory()->create(['amount' => $ledger->amount, 'ledger_id' => $ledger->id, 'account_id' => $treasuryId, 'credit' => 1]);
        }
        Transaction::factory()->create([
            'type_group' => TransactionTypeGroups::ACC,
            'type' => $type,
            'first_party_id' => $treasuryId,
            'amount' => $ledger->amount,
            'ledger_id' => $ledger->id,
            'second_party_id' => $secondPartyId,
            'group_id'=>$groupId,
        ]);
    }

    public static function seedPI($type,$groupId,$warehouseId , $supplierId ,$total ,$subtotal,$tax = 0 )
    {
        if(!$type){
            $type = 'PI' ;
        }
        $ledger = Ledger::factory()->create(['group_id'=>$groupId,'amount' => $total, 'currency_id' => 1, 'ex_rate' => 1]);

        Entry::factory()->create(['amount' => $subtotal, 'ledger_id' => $ledger->id, 'account_id' => $warehouseId, 'credit' => 0]);
        if ($tax){
            $taxAccountId = Account::whereHas('type',fn($q)=>$q->where('code','T'))->value('id');
            Entry::factory()->create(['amount' =>$tax, 'ledger_id' => $ledger->id, 'account_id' => $taxAccountId, 'credit' => 0]);
        }
        Entry::factory()->create(['amount' => $ledger->amount, 'ledger_id' => $ledger->id, 'account_id' => $supplierId, 'credit' => 1]);
        Transaction::factory()->create([
            'type_group' => TransactionTypeGroups::ACC,
            'type' => $type,
            'first_party_id' => $warehouseId,
            'amount' => $ledger->amount,
            'ledger_id' => $ledger->id,
            'second_party_id' => $supplierId,
            'group_id'=>$groupId,
        ]);
    }


    /**
     * @return string
     */
    public function seedTypes(): string
    {
        $types = AccountType::$types;

        foreach ($types as $key => $val) {
            AccountType::factory()->create([
                'code' => $key,
                'name' => $val
            ]);
        }
        return true;
    }

    /**
     * @return array
     */
    public function seedNodes()
    {
        $tr = AccountType::where('code', 'TR')->value('id');
        $b = AccountType::where('code', 'B')->value('id');
        $sales = AccountType::where('code', 'S')->value('id');
        $ss = AccountType::where('code', 'SS')->value('id');
        $inventory = AccountType::where('code', 'I')->value('id');
        $ap = AccountType::where('code', 'AP')->value('id');
        $ar = AccountType::where('code', 'AR')->value('id');
        $cog = AccountType::where('code', 'COG')->value('id');
        $sd = AccountType::where('code', 'SD')->value('id');
        $tax = AccountType::where('code', 'T')->value('id');


        $nodes = [
            ['code' => 1, 'name' => 'اﻷصول', 'parent_id' => null, 'credit' => 0,],
            ['code' => 2, 'name' => 'الخصوم', 'parent_id' => null, 'credit' => 1],
            ['code' => 3, 'name' => 'حقوق الملكية', 'parent_id' => null, 'credit' => 1],
            ['code' => 4, 'name' => 'الإيرادات', 'parent_id' => null, 'credit' => 0],
            ['code' => 5, 'name' => 'المصروفات', 'parent_id' => null, 'credit' => 1],

            ['name' => 'اﻷصول المتداولة', 'parent_id' => 1], // 6
            ['name' => 'اﻷصول الثابتة', 'parent_id' => 1], // 7
            ['name' => 'الخصوم المتداولة', 'parent_id' => 2,], // 8  'account_type_id' => $ap
            ['name' => 'رأس المال', 'parent_id' => 3,], // 9
            ['name' => 'جاري الشركاء', 'parent_id' => 3,], // 10
            ['name' => 'أرباح مرحلة', 'parent_id' => 3,], // 11
            ['name' => 'المبيعات', 'parent_id' => 4,], // 12
            ['name' => 'إيرادات أخري', 'parent_id' => 4,], // 13
            ['name' => 'تكلفة المبيعات', 'parent_id' => 5,], // 14
            ['name' => 'مصروفات إدارية وعمومية', 'parent_id' => 5,], // 15

            ['name' => 'النقدية', 'parent_id' => 6,], // 16
            ['name' => 'النقدية بالصندوق', 'parent_id' => 16, 'account_type_id' => $tr], // 17
            ['name' => 'النقدية بالبنك', 'parent_id' => 16, 'account_type_id' => $b], // 18
            ['name' => 'المخزون', 'parent_id' => 6, 'account_type_id' => $inventory], // 19
            ['name' => 'العملاء', 'parent_id' => 6, 'account_type_id' => $ar], // 20
            ['name' => 'أرصدة مدينة اخري', 'parent_id' => 6, 'account_type_id' => $ar], // 21
            ['name' => 'أصول غير ملموسة', 'parent_id' => 7,], // 22
            ['name' => 'أصول ملموسة', 'parent_id' => 7,], // 23
            ['name' => 'اﻹستثمارات', 'parent_id' => 7,], // 24
            ['name' => 'أجهزة كومبيزتر', 'parent_id' => 23,], // 25
            ['name' => 'أثاث', 'parent_id' => 23,], // 26
            ['name' => 'أجهزة كهربائية', 'parent_id' => 23,], // 27
            ['name' => 'تشطبيات ومستلزمات', 'parent_id' => 23,], // 28
            ['name' => 'الموردين', 'parent_id' => 8,'account_type_id'=>$ap], // 29
            ['name' => 'أرصدة دائنة أخري', 'parent_id' => 8, 'account_type_id' => $ap ], // 30
            ['name' => 'إيراد المبيعات', 'parent_id' => 12, 'account_type_id' => $sales], //  31
            ['name' => 'مردودات المبيعات', 'parent_id' => 12, 'account_type_id' => $ss], // 32
            ['name' => 'إيرادات أخري', 'parent_id' => 12,'account_type_id' => $ar], // 33
            ['name' => 'تكلفة البضاعة المباعة', 'parent_id' => 14, 'account_type_id' => $cog], // 34
            ['name' => 'خصم مسموح به', 'parent_id' => 14, 'account_type_id' => $sd], //

            ['name' => 'مصلحة الضرائب', 'parent_id' => 30,'account_type_id'=>$tax], //

            ['name' => 'مرتبات', 'parent_id' => 15], //
            ['name' => 'مصروف كهرباء - مياه - غاز', 'parent_id' => 15], //
            ['name' => 'مصروف إيجار', 'parent_id' => 15], //
            ['name' => 'مصروف بوفيه', 'parent_id' => 15], //
            ['name' => 'مصروف شحن', 'parent_id' => 15], //
            ['name' => 'مصروف إنتقالات', 'parent_id' => 15], //
            ['name' => 'مصروف نظافة', 'parent_id' => 15], //
            ['name' => 'مصروف إكرامية', 'parent_id' => 15], //
            ['name' => 'أدوات مكتبية ومطبوعات', 'parent_id' => 15], //
            ['name' => 'رسوم ومصروفات حكومية', 'parent_id' => 15], //
            ['name' => 'مصروفات صيانة', 'parent_id' => 15], //
            ['name' => 'مصروف مكافأت', 'parent_id' => 15], //
            ['name' => 'مصروف ضريبة خارجية', 'parent_id' => 15], //
            ['name' => 'مصروفات نثرية أخري', 'parent_id' => 15], //
            ['name' => 'مصروف عمولة', 'parent_id' => 15], //
            ['name' => 'مصروف ديون معدومة', 'parent_id' => 15], //
            ['name' => 'مصروف مشال', 'parent_id' => 15], //
            ['name' => 'مصروف إنترنت', 'parent_id' => 15], //
            ['name' => 'مصروف تليفونات', 'parent_id' => 15], //
            ['name' => 'مصروف دعاية وتسويق', 'parent_id' => 15], //
            ['name' => 'مصروف تأمينات إجتماعية', 'parent_id' => 15], //
            ['name' => 'مصروف الضرائب العامة', 'parent_id' => 15], //
            ['name' => 'مصروف بنكية', 'parent_id' => 15], //
            ['name' => 'مصروف سيرفر', 'parent_id' => 15], //
            ['name' => 'مصروف دعاية أونلاين', 'parent_id' => 15], //
            ['name' => 'مصروف الموقع اﻹلكتروني', 'parent_id' => 15], //
            ['name' => 'مصروف البريد اﻹلكرتوني', 'parent_id' => 15], //
            ['name' => 'مصروف أصول ثابتة', 'parent_id' => 15], //

//            ['name' => 'أوراق القبض', 'parent_id' => 8], //
//            ['name' => 'الحسابات المدينة المتنوعة', 'parent_id' => 8], //
//            ['name' => 'مخزن المستلزمات السلعية', 'parent_id' => 9], //
//            ['name' => 'مخزن مشتريات بغرض البيع', 'parent_id' => 9], //
//            ['name' => 'أعمال تحت التنفيذ', 'parent_id' => 9], //
//            ['name' => 'استثمارات فى أوراق مالية', 'parent_id' => 10], //
//            ['name' => 'مشاركـات أخرى', 'parent_id' => 10], //
//
//            ['name' => 'الدائنون', 'parent_id' => 12,], // مصحلة الضرائب
//            ['name' => 'مصروفات مستحقة', 'parent_id' => 12,], //
//            ['name' => 'رواتب مستحقة', 'parent_id' => 12,], //
//            ['name' => 'ضرائب مستحقة', 'parent_id' => 12,], //
//            ['name' => 'إيرادات مقدمة', 'parent_id' => 12,], //
//            ['name' => 'مجمع اﻹستهلاك', 'parent_id' => 12,], //
//            ['name' => 'قروض طويلة اﻷجل', 'parent_id' => 13,], //
//            ['name' => 'قروض قصيرة اﻷجل', 'parent_id' => 13,], //


        ];
        foreach ($nodes as $key => $node) {
            Node::factory()->create([
                'code' => $node['code'] ?? null,
                'name' => $node['name'],
                'slug' => Str::slug($node['name']),
                'credit' => $node['credit'] ?? null,
                'parent_id' => $node['parent_id'],
                'account_type_id' => $node['account_type_id'] ?? null
            ]);
        }
        return true;
    }

    /**
     * @return string
     */
    public function seedCurrencies(): string
    {
        $currencies = [
            [
                'name' => 'جنيه',
                'code' => 'EGP',
                'symbol' => 'جم',
            ],
            [
                'name' => 'dollar',
                'code' => 'USD',
                'symbol' => '$',
            ],
            [
                'name' => 'euro',
                'code' => 'EUR',
                'symbol' => '€',
            ],
        ];

        foreach ($currencies as $currency) {
            Currency::factory()->create([
                'name' => $currency['name'],
                'code' => $currency['code'],
                'symbol' => $currency['symbol'],
            ]);
        }
        return true;
    }

    /**
     * @return void
     */
    public function seedAccounts(): void
    {
        $treasuryType = Node::whereHas('type',fn($q)=>$q->where('code', 'TR'))->value('id');
        $warehouseType =  Node::whereHas('type',fn($q)=>$q->where('code', 'I'))->value('id');
        $supplierType = Node::whereHas('type',fn($q)=>$q->where('code', 'AP'))->value('id');
        $clientType =  Node::whereHas('type',fn($q)=>$q->where('code', 'AR'))->value('id');
        $tax = Node::whereHas('type',fn($q)=>$q->where('code', 'T'))->value('id');

        $accounts = [
            [
                'name' => 'الخزنة الرئيسية',
                'node_id' => $treasuryType,
            ],
            [
                'name' => 'الخزنة الفرعية',
                'node_id' => $treasuryType,
            ],
            [
                'name' => 'مخزن 1',
                'node_id' => $warehouseType,
            ],
            [
                'name' => 'مخزن 2',
                'node_id' => $warehouseType,
            ],
            [
                'name' => 'مورد 1',
                'node_id' => $supplierType,
            ],
            [
                'name' => 'مورد 2',
                'node_id' => $supplierType,
            ],
            [
                'name' => 'العميل 1',
                'node_id' => $clientType,
            ],
            [
                'name' => 'العميل 2',
                'node_id' => $clientType,
            ],
            [
                'name' => 'ضريبة المبيعات',
                'node_id' => $tax,
            ],
        ];
        foreach ($accounts as $account) {
            Account::factory()->create([
                'name' => $account['name'],
                'node_id' => $account['node_id'],
            ]);
        }

    }
}
