<?php

namespace App\Http\Controllers\Api\Accounting;

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Controller;
use App\Http\Requests\Accounting\AccountRequest;
use App\Http\Requests\ListRequest;
use App\Http\Requests\UpdateAccountRequest;
use App\Http\Resources\Accounting\AccCategoryResource;
use App\Http\Resources\Accounting\AccountChartResource;
use App\Http\Resources\Accounting\AccountResource;
use App\Http\Resources\UserResource;
use App\Models\Accounting\AccCategory;
use App\Models\Accounting\Account;
use App\Models\System\Currency;
use App\Services\Accounting\AccountService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AccountsController extends ApiController
{
    public function __construct()
    {
        parent::__construct();
        $this->class = "accounts";
        $this->table = "accounts";
        $this->middleware('auth');
        $this->middleware('permission:accounting.accounts.view', ['only' => ['index', 'show']]);
        $this->middleware('permission:accounting.accounts.create', ['only' => ['create', 'store']]);
        $this->middleware('permission:accounting.accounts.edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:accounting.accounts.delete', ['only' => ['destroy']]);
        $this->service = new AccountService();
    }

    public function index()
    {
        if (auth('api')->user()->cannot('accounting.accounts.index')) {
            return $this->deniedResponse(null, null, 403);
        }

        $tree = AccCategory::tree()->withCount('children')->with('accounts')->get()->toTree();
        return $this->successResponse(['tree' => AccCategoryResource::collection($tree)]);
    }

    public function list(Request $request)
    {
        if (auth('api')->user()->cannot('accounting.accounts.index')) {
            return $this->deniedResponse(null, null, 403);
        }
        $accounts = Account::active()->pluck('name', 'code')->toArray();
        return $this->successResponse(['accounts' => $accounts]);
    }

    public function categories(Request $request)
    {
        if (auth('api')->user()->cannot('accounting.accounts.index')) {
            return $this->deniedResponse(null, null, 403);
        }
        $accounts = Account::active()->get(['name', 'code','credit','system','active']);
        if ($request->has('type')){
            $cashCategory = AccCategory::active()->with(['descendants' => fn($q) => $q->with('accounts'), 'accounts'])->where('slug', 'alnkdy')->first();
            $cashAccounts = collect($cashCategory->accounts);
            foreach ($cashCategory->descendants as $child) {
                $cashAccounts = $cashAccounts->merge($child->accounts);
            }
            return $this->successResponse(['accounts' => AccountChartResource::collection($accounts),'cashAccounts' => AccountChartResource::collection($cashAccounts)]);
        }
        return $this->successResponse(['accounts' =>AccountChartResource::collection($accounts)]);
    }

    public function create(Request $request)
    {
        if (auth('api')->user()->cannot('accounting.accounts.create')) {
            return $this->deniedResponse(null, null, 403);
        }

        if ($request->has('id')) {
            $account = Account::with('category', 'currency')->whereId($request->get('id'))->first();
        }

        $categories = AccCategory::isLeaf()->active()->pluck('name', 'id')->toArray();
        $currencies = Currency::active()->pluck('name', 'id')->toArray();
        return $this->successResponse(['categories' => $categories, 'currencies' => $currencies, 'account' => isset($account) ? new AccountResource($account) : null]);
    }

    public function store(AccountRequest $request)
    {
        $input = $request->all();

        $category = AccCategory::isLeaf()->withCount('accounts')->whereId($input['acc_category_id'])->first();
        if (!$category) {
            return $this->errorResponse(__('Category Not Found'), 200);
        }

//        return $category->id ;
        DB::transaction(function () use ($input, $category) {
            $account = Account::create([
                'name' => $input['name'],
//                'code' => $category->id . ((int)$category->accounts_count + 1),
                'description' => $input['description'],
                'currency_id' => $input['currency_id'],
                'acc_category_id' => $category->id,
                'opening_balance' => $input['opening_balance'] ?? null,
                'opening_balance_date' => $input['opening_balance_date'] ?? null,
                'credit' => $category->credit,

            ]);
//                if ($validated['opening_balance']){
//                    $transaction =  Transaction::create([
//                        'amount' => $validated['opening_balance'],
//                        'description' =>'Opening Balance For Account' . $account->name,
//                        'type' => 'user',
//                        'due' => $validated['opening_balance_date'] ?? now(),//$validated['due']
//                        'user_id' => auth()->id()
//                    ]);
//                    Entry::create([
//                        'amount' => $validated['opening_balance'],
//                        'credit' =>$account->credit,
//                        'account_id' => $account->id,
//                        'transaction_id' => $transaction->id
//                    ]);
//                }

//            activity()
//                ->performedOn($account)
//                ->causedBy($user)
//                ->event('updated')
//                ->useLog($user->name)
//                ->log('Account Has been Created');

        });

        return $this->successResponse(null, __('message.created', ['model' => __('names.account')]));
    }

    public function update(UpdateAccountRequest $request,$code)
    {
        $account = Account::where('code', $code)->firstOrFail();
        try {
            $account->update($request->validated());
        }catch (\Exception  $e){
            return $this->errorResponse('something went wrong please try again',);
        }

        return $this->successResponse(null, __('message.updated', ['model' => __('names.account')]));
    }

    public function duplicateCategory($id)
    {
        $category = AccCategory::find($id);
        AccCategory::create([
            'name' => $category->name . ' copy',
            'slug' => Str::slug($category->name . ' copy'),
            'credit' => $category->credit,
            'parent_id' => $category->parent_id,
            'system' => 0,
            'usable' => 1
        ]);
        $this->successResponse(__('message.created', ['model' => __('Category')]));
    }

    public function duplicateAccount($id)
    {
        $account = Account::find($id);
        Account::create([
            'name' => $account->name . ' copy',
            'credit' => $account->credit,
            'category_id' => $account->category_id,
            'currency_id' => $account->currency_id,
            'description' => $account->description,
            'system' => 0,
        ]);
        $this->successResponse(__('message.created', ['model' => __('Account')]));
    }

    public function journal(ListRequest $request)
    {

        $accounts = $this->service->search($request->get('keywords'))
            ->when($request->get('start_date'), function ($query) use ($request) {
                $query->where('created_at', '>=', $request->get('start_date'));
            })
            ->when($request->get('end_date'), function ($query) use ($request) {
                $query->where('created_at', '<=', $request->get('start_date'));
            })
            ->with('category')
            ->withSum(['entries as credit_sum' => function ($query) {
                $query->where('credit', true);
            }], 'amount')
            ->withSum(['entries as debit_sum' => function ($query) {
                $query->where('credit', false);
            }], 'amount')
            ->orderBy($request->get('OrderBy') ?? $this->orderBy, $request->get('OrderBy') ?? $this->orderDesc ? 'desc' : 'asc')
            ->get();
        return $this->successResponse(['accounts' => AccountResource::collection($accounts)]); //AccountResource
    }

    public function show(Request $request, $code)
    {
        $account = Account::with(['entries.transaction', 'transactions', 'category'])
            ->withSum(['entries as credit_sum' => function ($query) {
                $query->where('locked', false)->where('post', false)->where('credit', true);
            }], 'amount')
            ->withSum(['entries as debit_sum' => function ($query) {
                $query->where('locked', false)->where('post', false)->where('credit', false);
            }], 'amount')
            ->where('code', $code)
            ->firstOrFail();

        return $this->successResponse(new AccountResource($account));;
    }

}
