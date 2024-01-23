<?php

namespace App\Http\Controllers\Api\Accounting;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Accounting\AccountRequest;
use App\Http\Requests\Accounting\DuplicateRequest;
use App\Http\Requests\Accounting\StoreCostCenterRequest;
use App\Http\Requests\Accounting\UpdateAccountRequest;
use App\Http\Requests\Accounting\UpdateNodeRequest;
use App\Http\Requests\ListRequest;
use App\Http\Resources\Accounting\AccountResource;
use App\Http\Resources\Accounting\CostCenterNodeResource;
use App\Http\Resources\Accounting\CostCenterResource;
use App\Http\Resources\Accounting\NodeResource;
use App\Models\Accounting\Account;
use App\Models\Accounting\AccountType;
use App\Models\Accounting\CostCenter;
use App\Models\Accounting\CostCenterNode;
use App\Models\Accounting\Currency;
use App\Models\Accounting\Node;
use App\Services\Accounting\AccountService;
use App\Services\Accounting\CostCenterNodeService;
use App\Services\Accounting\CostCenterService;
use App\Services\Accounting\CurrencyService;
use App\Services\System\GroupService;
use App\Services\System\TagService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CostCentersController extends ApiController
{
    public function __construct()
    {
        parent::__construct();
        $this->class = "centers";
        $this->table = "centers";
        $this->middleware('auth');
        $this->middleware('permission:accounting.centers.view', ['only' => ['index', 'show']]);
        $this->middleware('permission:accounting.centers.create', ['only' => ['create', 'store']]);
        $this->middleware('permission:accounting.centers.edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:accounting.centers.delete', ['only' => ['destroy']]);


        $this->service = new CostCenterService();
    }



    public function index(Request $request)
    {
        if (auth('api')->user()->cannot('accounting.accounts.index')) {
            return $this->deniedResponse(null, null, 403);
        }
        $costCenters = CostCenter::active()->get();

        return $this->successResponse(['costCenters' => CostCenterResource::collection($costCenters)]);
    }

    public function list(Request $request)
    {
        if (auth('api')->user()->cannot('accounting.costCenters.index')) {
            return $this->deniedResponse(null, null, 403);
        }
        if ($request->has("keywords")) {
            $costCenters = $this->service->search($request->get("keywords"))->limit(5)->get();
            return $this->successResponse(['costCenters' => CostCenterResource::collection($costCenters)]);
        }
        $costCenters = CostCenter::active()->pluck('name', 'code')->toArray();
        return $this->successResponse(['costCenters' => $costCenters]);
    }

    public function tree()
    {
        if (auth('api')->user()->cannot('accounting.centers.index')) {
            return $this->deniedResponse(null, null, 403);
        }
        $tree = (new CostCenterNodeService())->tree(null, ['costCenters'], ['children']);
        return $this->successResponse(CostCenterNodeResource::collection($tree));
    }

    public function nodes(Request $request)
    {
        if (auth('api')->user()->cannot('accounting.accounts.index')) {
            return $this->deniedResponse(null, null, 403);
        }
        $nodes = CostCenterNode::active()->pluck('name', 'code')->toArray();
        return $this->successResponse(['nodes' => $nodes]);
    }

    public function node(Request $request,$code)
    {
        if (auth('api')->user()->cannot('accounting.accounts.index')) {
            return $this->deniedResponse(null, null, 403);
        }
        $node = CostCenterNode::active()->where('code', $code)->firstOrFail() ;
        return $this->successResponse(isset($node) ? new NodeResource($node) : null);
    }

    public function updateNode(UpdateNodeRequest $request, $code)
    {
        $node = CostCenterNode::where('code', $code)->first();
        if (!$node){
            return $this->errorResponse('Node Not Found',404);
        }
//        return $request->get('name') ;
        try {
            $node->update(['name'=>$request->get('name')])  ;
        }catch (\Exception  $e){
            return $this->errorResponse('something went wrong please try again',);
        }
        return $this->successResponse(null, __('message.updated', ['model' => __('Cost Center Node')]));

    }



    public function create(Request $request)
    {
        if (auth('api')->user()->cannot('accounting.centers.create')) {
            return $this->deniedResponse(null, null, 403);
        }

        if ($request->has('code')) {
            $costCenter = CostCenter::with('node', 'lastContact', 'lastAddress', 'tags', 'accesses.user', 'userAccesses')->where('code', $request->get('code'))->firstorFail();
        }

        if ($request->has('nodeId')) {
            $node = CostCenterNode::where('code', $request->get('nodeId'))->first();
        }

        $nodes = CostCenterNode::isLeaf()->active()->select('name', 'id', 'code')->get();
        $tags = (new TagService())->all(['name', 'id'], 'costCenter');
        $groups = (new GroupService())->all(['name', 'id']);
        $users = (new UserService())->all(['id', 'username']);
        return $this->successResponse([
            'nodes' => $nodes,
            'costCenter' => isset($costCenter) ? new CostCenterResource($costCenter) : null,
            'tags' => $tags,
            'groups' => $groups,
            'users' => $users,
            'node' =>   isset($node) ? new CostCenterNodeResource($node) : null,
        ]);
    }

    public function store(StoreCostCenterRequest $request)
    {
        try {
            $res = (new CostCenterService())->store($request->validated());
            if ($res !== true){
                return $this->errorResponse($res, 409);
            }
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 409);
        }

        return $this->successResponse(null, __('message.created', ['model' => __('Cost Center')]));
    }

    public function update(StoreCostCenterRequest $request, $code)
    {
        try {
            $res = (new CostCenterService())->store($request->validated(), $code);
            if ($res !== true){
                return $this->errorResponse($res, 409);
            }
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 409);
        }
//        return $res ;

        return $this->successResponse(null, __('message.updated', ['model' => __('Cost Center')]));
    }
    public function duplicate(DuplicateRequest $request )
    {
        $data = $request->validated();

            $node = CostCenterNode::where('code',$data['costCenterNode'])->first();
            $newName = $node->name ;
        $slug = Str::slug($newName);
            $counter = 1;
            do {
                $newName = $node->name .' ('. $counter .')';
                $slug = Str::slug($newName);
                $counter++;
            }
            while (CostCenterNode::where('name', $newName)->exists()) ;
            CostCenterNode::create([
                'name' =>$newName,
                'slug' =>$slug,
                'parent_id' => $node->parent_id,
                'system' => 0,
            ]);
            return $this->successResponse(null,__('message.created', ['model' => __('Node')]));

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

        $account = Account::with(['entries.transaction', 'node','currency','status'])
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
