<?php

namespace App\Http\Controllers\Api\Accounting;

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Controller;
use App\Http\Requests\Accounting\AccountRequest;
use App\Http\Requests\Accounting\DuplicateRequest;
use App\Http\Requests\Accounting\UpdateCategoryRequest;
use App\Http\Requests\ListRequest;
use App\Http\Requests\TypeRequest;
use App\Http\Requests\UpdateAccountRequest;
use App\Http\Resources\Accounting\AccCategoryResource;
use App\Http\Resources\Accounting\AccountChartResource;
use App\Http\Resources\Accounting\AccountResource;
use App\Http\Resources\UserResource;
use App\Models\Accounting\AccCategory;
use App\Models\Accounting\Account;
use App\Models\System\Currency;
use App\Services\Accounting\WarehouseService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
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


        $this->service = new WarehouseService();
    }



    public function index(Request $request)
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

    public function list(Request $request)
    {
        if (auth('api')->user()->cannot('accounting.accounts.index')) {
            return $this->deniedResponse(null, null, 403);
        }
        $accounts = Account::active()->pluck('name', 'code')->toArray();
        return $this->successResponse(['accounts' => $accounts]);
    }

    public function tree()
    {
        if (auth('api')->user()->cannot('accounting.accounts.index')) {
            return $this->deniedResponse(null, null, 403);
        }
        $tree = AccCategory::tree()->withCount('children')->with('accounts')->get()->toTree();
        return $this->successResponse(AccCategoryResource::collection($tree));
    }

    public function categories(Request $request)
    {
        if (auth('api')->user()->cannot('accounting.accounts.index')) {
            return $this->deniedResponse(null, null, 403);
        }
        $accounts = AccCategory::active()->pluck('name', 'code')->toArray();
        return $this->successResponse(['accounts' => $accounts]);
    }

    public function category(Request $request,$code)
    {
        if (auth('api')->user()->cannot('accounting.accounts.index')) {
            return $this->deniedResponse(null, null, 403);
        }
        $category = AccCategory::active()->where('code', $code)->firstOrFail() ;
        return $this->successResponse(isset($category) ? new AccCategoryResource($category) : null);
    }

    public function updateCategory(UpdateCategoryRequest $request,$code)
    {
        $category = AccCategory::where('code', $code)->first();
        if (!$category){
            return $this->errorResponse('Category Not Found',404);
        }
//        return $request->get('name') ;
        try {
            $category->update(['name'=>$request->get('name')])  ;
        }catch (\Exception  $e){
            return $this->errorResponse('something went wrong please try again',);
        }
        return $this->successResponse(null, __('message.updated', ['model' => __('names.category')]));

    }



        public function create(Request $request)
    {
        if (auth('api')->user()->cannot('accounting.accounts.create')) {
            return $this->deniedResponse(null, null, 403);
        }

        if ($request->has('code')) {
            $account = Account::with('category', 'currency')->where('code',$request->get('code'))->firstorFail();
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

    public function duplicate(DuplicateRequest $request )
    {
        $data = $request->validated();
        if ($data['type'] == 'account'){
            $account = Account::where('code',$data['account'])->first();
            Account::create([
                'name' => $account->name . ' copy',
                'credit' => $account->credit,
                'acc_category_id' => $account->acc_category_id,
                'currency_id' => $account->currency_id,
                'description' => $account->description,
                'system' => 0,
            ]);
            return $this->successResponse(null,__('message.created', ['model' => __('Account')]));
        }else{
            $category = AccCategory::where('code',$data['category'])->first();
            $newName = $category->name ;
            $slug = Str::slug($newName);
            $counter = 1;
            do {
                $newName = $category->name .' ('. $counter .')';
                $slug = Str::slug($newName);
                $counter++;
            }
            while (AccCategory::where('name', $newName)->orWhere('slug', $slug)->exists()) ;
            AccCategory::create([
                'name' =>$newName,
                'slug' =>  Str::slug($slug),
                'credit' => $category->credit,
                'parent_id' => $category->parent_id,
                'system' => 0,
                'usable' => 1
            ]);
            return $this->successResponse(null,__('message.created', ['model' => __('Category')]));
        }
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

        $account = Account::with(['entries.transaction', 'category','currency','status'])
            ->withSum(['entries as credit_sum' => function ($query) {
                $query->where('locked', false)->where('post', false)->where('credit', true);
            }], 'amount')
            ->withSum(['entries as debit_sum' => function ($query) {
                $query->where('locked', false)->where('post', false)->where('credit', false);
            }], 'amount')
            ->where('code', $code)
            ->firstOrFail();

        return $this->successResponse(new AccountResource($account));
    }

}
