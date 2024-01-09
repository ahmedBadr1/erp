<?php

namespace Database\Seeders;

use App\Models\Accounting\Account;
use App\Models\Accounting\AccountType;
use App\Models\Accounting\CostCenter;
use App\Models\Accounting\Entry;
use App\Models\Accounting\Ledger;
use App\Models\Accounting\Node;
use App\Models\Accounting\Transaction;
use App\Models\System\Currency;
use App\Models\System\Tax;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AccountingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        Tax::factory()->create([
            'name' => 'VAT',
            'rate' => '14',
            'active' => true
        ]);

        $this->seedTypes();

        $this->seedNodes();

        $this->seedCurrencies();

        CostCenter::factory()->create(['name' => 'مركز تكلفة رئيسي', "code" => "01", 'system' => 1, 'parent_id' => null]);
        CostCenter::factory(10)->create();
        Account::factory(100)->create();
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
        $ledger = Ledger::factory()->create(['amount' => rand(100, 1000)]);
        Entry::factory()->create(['amount' => $ledger->amount, 'ledger_id' => $ledger->id, 'credit' => 1]);
        Entry::factory()->create(['amount' => $ledger->amount, 'ledger_id' => $ledger->id, 'credit' => 0]);
        Transaction::factory(rand(1, 3))->create([
            'amount' => $ledger->amount,
            'ledger_id' => $ledger->id,
        ]);
        unset($ledger);
    }


    /**
     * @return string
     */
    public function seedTypes(): string
    {
        $types = [
            'أصل متداول' => 'أصل متداول',
            'أصل ثابت' => 'أصل ثابت',
            'رأس المال' => 'رأس المال',
            'مصروف' => '-',
            'نقدية' => 'نقدية',
            'إيراد' => '-',
            'مخزون' => '-',
            'إستثمار' => '-',
            'مورد' => '-',
            'عميل' => '-',
        ];

        foreach ($types as $key => $val) {
            AccountType::factory()->create([
                'name' => $key,
                'description' => $val
            ]);
        }
        return true;
    }

    /**
     * @return array
     */
    public function seedNodes()
    {
        $nodes = [
            ['code' => 1, 'name' => 'اﻷصول', 'parent_id' => null, 'credit' => 0],
            ['code' => 2, 'name' => 'الخصوم', 'parent_id' => null, 'credit' => 1],
            ['code' => 3, 'name' => 'حقوق الملكية', 'parent_id' => null, 'credit' => 1],
            ['code' => 4, 'name' => 'الإيرادات', 'parent_id' => null, 'credit' => 0],
            ['code' => 5, 'name' => 'المصروفات', 'parent_id' => null, 'credit' => 1],

            ['name' => 'اﻷصول المتداولة', 'parent_id' => 1], // 6
            ['name' => 'اﻷصول الثابتة', 'parent_id' => 1], // 7
            ['name' => 'الخصوم المتداولة', 'parent_id' => 2,], // 8
            ['name' => 'رأس المال', 'parent_id' => 3,], // 9
            ['name' => 'جاري الشركاء', 'parent_id' => 3,], // 10
            ['name' => 'أرباح مرحلة', 'parent_id' => 3,], // 11
            ['name' => 'المبيعات', 'parent_id' => 4,], // 12
            ['name' => 'إيرادات أخري', 'parent_id' => 4,], // 13
            ['name' => 'تكلفة المبيعات', 'parent_id' => 5,], // 14
            ['name' => 'مصروفات إدارية وعمومية', 'parent_id' => 5,], // 15

            ['name' => 'النقدية', 'parent_id' => 6,], // 16
            ['name' => 'النقدية بالصندوق', 'parent_id' => 16,], // 17
            ['name' => 'النقدية بالبنك', 'parent_id' => 16,], // 18
            ['name' => 'المخزون', 'parent_id' => 6,], // 19
            ['name' => 'العملاء', 'parent_id' => 6,], // 20
            ['name' => 'أرصدة مدينة اخري', 'parent_id' => 6,], // 21
            ['name' => 'أصول غير ملموسة', 'parent_id' => 7,], // 22
            ['name' => 'أصول ملموسة', 'parent_id' => 7,], // 23
            ['name' => 'اﻹستثمارات', 'parent_id' => 7,], // 24
            ['name' => 'أجهزة كومبيزتر', 'parent_id' => 23,], // 25
            ['name' => 'أثاث', 'parent_id' => 23,], // 26
            ['name' => 'أجهزة كهربائية', 'parent_id' => 23,], // 27
            ['name' => 'تشطبيات ومستلزمات', 'parent_id' => 23,], // 28
            ['name' => 'الموردين', 'parent_id' => 8,], // 29
            ['name' => 'أرصدة دائنة أخري', 'parent_id' => 8,], // 30
            ['name' => 'إيراد المبيعات', 'parent_id' => 12,], //
            ['name' => 'مردودات المبيعات', 'parent_id' => 12,], //
            ['name' => 'إيرادات أخري', 'parent_id' => 12,], //
            ['name' => 'تكلفة البضاعة المباعة', 'parent_id' => 14,], //
            ['name' => 'خصم مسموح به', 'parent_id' => 14,], //

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
            'جم' => 'EGP',
            '$' => 'USD',
//            'EUR' => 'Euro',
//            'SAR' => 'Saudi Riyal',
//            'QAR' => 'Qatari Riyal',
//            'IQD' => 'Iraqi Dinar',
//            'IRR' => 'Iranian Rial',
//            'JOD' => 'Jordanian Dinar',
//            'KWD' => 'Kuwaiti Dinar',
//            'LBP' => 'Lebanese Pound',
//            'LYD' => 'Libyan Dinar',
//            'MAD' => 'Moroccan Dirham',
//            'OMR' => 'Omani Rial',
//            'SYP' => 'Syrian Pound',
//            'TND' => 'Tunisian Dinar',
//            'YER' => 'Yemeni Rial',
//            'JPY' => 'Japanese Yen',
//            'GBP' => 'British Pound',
//            'AUD' => 'Australian Dollar',
//            'CAD' => 'Canadian Dollar',
//            'CHF' => 'Swiss Franc',
//            'CNY' => 'Chinese Yuan',
//            'HKD' => 'Hong Kong Dollar',
//            'NZD' => 'New Zealand Dollar',
//            'SEK' => 'Swedish Krona',
//            'KRW' => 'South Korean Won',
//            'SGD' => 'Singapore Dollar',
//            'NOK' => 'Norwegian Krone',
//            'MXN' => 'Mexican Peso',
//            'INR' => 'Indian Rupee',
//            'RUB' => 'Russian Ruble',
//            'ZAR' => 'South African Rand',
//            'TRY' => 'Turkish Lira',
//            'BRL' => 'Brazilian Real',
//            'AED' => 'United Arab Emirates Dirham',
//            'BHD' => 'Bahraini Dinar',

        ];

        foreach ($currencies as $key => $val) {
            Currency::factory()->create([
                'code' => $key,
                'name' => $val,
            ]);
        }
        return true;
    }
}
