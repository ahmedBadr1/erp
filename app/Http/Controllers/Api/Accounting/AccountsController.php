<?php

namespace App\Http\Controllers\Api\Accounting;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Accounting\AccountRequest;
use App\Http\Requests\Accounting\DuplicateRequest;
use App\Http\Requests\Accounting\IndexAccountsRequest;
use App\Http\Requests\Accounting\UpdateAccountRequest;
use App\Http\Requests\Accounting\UpdateNodeRequest;
use App\Http\Requests\ListRequest;
use App\Http\Resources\Accounting\AccountChartResource;
use App\Http\Resources\Accounting\AccountResource;
use App\Http\Resources\Accounting\CurrencyResource;
use App\Http\Resources\Accounting\NodeResource;
use App\Models\Accounting\Account;
use App\Models\Accounting\AccountType;
use App\Models\Accounting\Currency;
use App\Models\Accounting\Node;
use App\Models\System\Group;
use App\Models\System\Tag;
use App\Models\User;
use App\Services\Accounting\AccountService;
use App\Services\Accounting\CurrencyService;
use App\Services\Accounting\NodeService;
use App\Services\System\GroupService;
use App\Services\System\TagService;
use App\Services\UserService;
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


    public function index(IndexAccountsRequest $request)
    {
        if (auth('api')->user()->cannot('accounting.accounts.index')) {
            return $this->deniedResponse(null, null, 403);
        }
        $input = $request->validated();
        $query = Account::active();
        if (isset($input['node_id'])) {
            $nodeAccounts =$this->service->all(['code','name','description','credit_limit','debit_limit','account_type_id','active'] , $input['account_type_id'] ?? null,$input['node_id']?? null);
            return $this->successResponse(['accounts' => $nodeAccounts]);
        }
        $accounts = $query->get(['name', 'code', 'credit', 'system', 'active']);
        if ($request->has('type')) {
//            $cashNode = Node::active()->with(['descendants' => fn($q) => $q->with('accounts'), 'accounts'])->where('slug', 'alnkdy')->first();
//            $cashAccounts = collect($cashNode->accounts);
//            foreach ($cashNode->descendants as $child) {
//                $cashAccounts = $cashAccounts->merge($child->accounts);
//            }
//            $treasuries = Account::whereHas('type',fn($q)=>$q->where('code','TR'))->get();
            $treasuries = Account::where('type_code', 'like', 'TR%')->get();

//            $currencies = Currency::active()->pluck('name', 'id')->toArray();
            $currencies = CurrencyResource::collection(Currency::active()->get());
//            $costCenters = CostCenterResource::collection(CostCenter::active()->get());


            $users = User::active()->withoutAppends()->get(['id', "username"]);

            return $this->successResponse([
                'accounts' => AccountChartResource::collection($accounts),
                'treasuries' => AccountChartResource::collection($treasuries),
                'currencies' => $currencies,
                'users' => $users]);
        }
        return $this->successResponse(['accounts' => AccountChartResource::collection($accounts)]);
    }

    public function list(ListRequest $request)
    {
        if (auth('api')->user()->cannot('accounting.accounts.index')) {
            return $this->deniedResponse(null, null, 403);
        }
        if ($request->has("keywords")) {
            $accounts = $this->service->search($request->get("keywords"))->limit(5)->get();
            return $this->successResponse(['accounts' => AccountChartResource::collection($accounts)]);
        }
        $accounts = Account::active()->pluck('name', 'code')->toArray();
        return $this->successResponse(['accounts' => $accounts]);
    }

    public function tree(Request $request)
    {
        if (auth('api')->user()->cannot('accounting.accounts.index')) {
            return $this->deniedResponse(null, null, 403);
        }
        $tree = (new NodeService())->tree(null, ['accounts'], ['children']);
        return $this->successResponse(NodeResource::collection($tree));
    }

    public function nodes(Request $request)
    {
        if (auth('api')->user()->cannot('accounting.accounts.index')) {
            return $this->deniedResponse(null, null, 403);
        }
        $nodes = Node::active()->pluck('name', 'code')->toArray();
        return $this->successResponse(['nodes' => $nodes]);
    }

    public function node(Request $request, $code)
    {
        if (auth('api')->user()->cannot('accounting.accounts.index')) {
            return $this->deniedResponse(null, null, 403);
        }
        $node = Node::active()->where('code', $code)->firstOrFail();
        return $this->successResponse(isset($node) ? new NodeResource($node) : null);
    }

    public function updateNode(UpdateNodeRequest $request, $code)
    {
        $node = Node::where('code', $code)->first();
        if (!$node) {
            return $this->errorResponse('Node Not Found', 404);
        }
//        return $request->get('name') ;
        try {
            $node->update(['name' => $request->get('name')]);
        } catch (\Exception  $e) {
            return $this->errorResponse('something went wrong please try again',);
        }
        return $this->successResponse(null, __('message.updated', ['model' => __('Node')]));

    }


    public function create(Request $request)
    {
        if (auth('api')->user()->cannot('accounting.accounts.create')) {
            return $this->deniedResponse(null, null, 403);
        }

        if ($request->has('code')) {
            $account = Account::with('node', 'currency', 'lastContact', 'lastAddress', 'tags', 'accesses.user', 'userAccesses')->where('code', $request->get('code'))->firstorFail();
        }

        if ($request->has('nodeId')) {
            $node = Node::where('code', $request->get('nodeId'))->first();
        }

        $nodes = Node::isLeaf()->active()->select('name', 'id', 'code')->get();
        $currencies = (new CurrencyService())->all(['code', 'id']);
        $tags = (new TagService())->all(['name', 'id'], 'account');
        $groups = (new GroupService())->all(['name', 'id']);
        $users = (new UserService())->all(['id', 'username']);
        $accountTypes = AccountType::active()->select('name', 'id')->get();
        return $this->successResponse([
            'nodes' => $nodes,
            'currencies' => $currencies,
            'account' => isset($account) ? new AccountResource($account) : null,
            'tags' => $tags,
            'accountTypes' => $accountTypes,
            'groups' => $groups,
            'users' => $users,
            'node' => $node ?? null,
        ]);
    }

    public function store(AccountRequest $request)
    {

        try {
            $res = (new AccountService())->store($request->validated());
            if ($res !== true){
                return $this->errorResponse($res, 409);
            }
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 409);
        }

        return $this->successResponse(null, __('message.created', ['model' => __('names.account')]));
    }

    public function update(UpdateAccountRequest $request, $code)
    {
        try {
            $res = (new AccountService())->store($request->validated());
            if ($res !== true){
                return $this->errorResponse($res, 409);
            }
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 409);
        }

        return $this->successResponse(null, __('message.updated', ['model' => __('Account')]));
    }

    public function duplicate(DuplicateRequest $request)
    {
        $data = $request->validated();
        if ($data['type'] == 'account') {
            $account = Account::where('code', $data['account'])->first();
            Account::create([
                'name' => $account->name . ' copy',
                'credit' => $account->credit,
                'node_id' => $account->node_id,
                'account_type_id' => $account->account_type_id,
                'currency_id' => $account->currency_id,
                'description' => $account->description,
                'system' => 0,
            ]);
            return $this->successResponse(null, __('message.created', ['model' => __('Account')]));
        } else {
            $node = Node::where('code', $data['node'])->first();
            $newName = $node->name;
            $slug = Str::slug($newName);
            $counter = 1;
            do {
                $newName = $node->name . ' (' . $counter . ')';
                $slug = Str::slug($newName);
                $counter++;
            } while (Node::where('name', $newName)->orWhere('slug', $slug)->exists());
            Node::create([
                'name' => $newName,
                'slug' => Str::slug($slug),
                'credit' => $node->credit,
                'account_type_id' => $node->account_type_id,
                'parent_id' => $node->parent_id,
                'system' => 0,
                'usable' => 1
            ]);
            return $this->successResponse(null, __('message.created', ['model' => __('Node')]));
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
            ->with('node')
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

        $account = Account::with(['entries.transaction', 'node', 'currency', 'status'])
            ->withSum(['entries as credit_sum' => function ($query) {
                $query->where('locked', false)->where('posted', false)->where('credit', true);
            }], 'amount')
            ->withSum(['entries as debit_sum' => function ($query) {
                $query->where('locked', false)->where('posted', false)->where('credit', false);
            }], 'amount')
            ->where('code', $code)
            ->firstOrFail();

        return $this->successResponse(new AccountResource($account));
    }

}
