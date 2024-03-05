<?php

namespace App\Providers;

use App\Http\Resources\Dashboard\NotificationCollection;
use App\Models\Accounting\Account;
use App\Models\Accounting\CostCenter;
use App\Models\Accounting\CostCenterNode;
use App\Models\Accounting\Currency;
use App\Models\Accounting\Ledger;
use App\Models\Accounting\Node;
use App\Models\Accounting\Tax;
use App\Models\Accounting\Transaction;
use App\Models\Inventory\InvTransaction;
use App\Models\Inventory\Item;
use App\Models\Inventory\ItemHistory;
use App\Models\Inventory\Product;
use App\Models\Inventory\Unit;
use App\Models\Inventory\Warehouse;
use App\Models\Purchases\Bill;
use App\Models\Purchases\BillItem;
use App\Models\Purchases\Supplier;
use App\Models\Sales\Client;
use App\Models\Sales\Invoice;
use App\Models\System\Address;
use App\Models\System\Contact;
use App\Models\System\Group;
use App\Models\System\Profile;
use App\Models\System\Tag;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Models\User;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
//        Paginator::useBootstrap();
        Schema::defaultStringLength(191);

        Relation::morphMap([
            'user' => User::class,
            'group' => Group::class,
            'contact' => Contact::class,
            'address' => Address::class,
            'tag' => Tag::class,
            'profile' => Profile::class,

            'node' => Node::class,
            'account' => Account::class,
            'costCenterNode' => CostCenterNode::class,
            'costCenter' => CostCenter::class,
            'ledger' => Ledger::class,
            'transaction' => Transaction::class,
            'tax' => Tax::class,
            'currency' => Currency::class,

            'product' => Product::class,
            'warehouse' => Warehouse::class,
            'item' => ItemHistory::class,
            'unit' => Unit::class,
            'invTransaction' => InvTransaction::class,
            'InvTransactionItem' => Item::class,


            'supplier' => Supplier::class,
            'bill' => Bill::class,
            'billItem' => BillItem::class,

            'client' => Client::class,
            'invoice' => Invoice::class,

        ]);

        $localeDirs = config('languages.localeDirs');
        $langs = config('languages.langs');
        View::share('localeDirs', $localeDirs);
        View::share('langs', $langs);
//        JsonResource::withoutWrapping();


    }



}
