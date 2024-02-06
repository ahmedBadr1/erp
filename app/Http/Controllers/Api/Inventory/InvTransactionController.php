<?php

namespace App\Http\Controllers\Api\Inventory;

use App\Http\Controllers\Api\Accounting\TransactionsResourses;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Accounting\PostingRequest;
use App\Http\Requests\Accounting\StoreTransactionTypeRequest;
use App\Http\Requests\Inventory\StoreInvTransactionRequest;
use App\Http\Requests\ListRequest;
use App\Http\Requests\TypeRequest;
use App\Http\Resources\Inventory\InvTransactionResource;
use App\Models\Accounting\Account;
use App\Models\Accounting\CostCenter;
use App\Models\Inventory\InvTransaction;
use App\Models\Purchases\Bill;
use App\Models\System\ModelGroup;
use App\Services\Accounting\LedgerService;
use App\Services\Inventory\InvTransactionService;
use App\Services\Inventory\WarehouseService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvTransactionController extends ApiController
{
    public function __construct()
    {
        parent::__construct();
        $this->class = "inv_transactions";
        $this->table = "inv_transactions";
        $this->middleware('auth');
        $this->middleware('permission:inventory.transactions.view', ['only' => ['index', 'show']]);
        $this->middleware('permission:inventory.transactions.create', ['only' => ['create', 'store']]);
        $this->middleware('permission:inventory.transactions.edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:inventory.transactions.delete', ['only' => ['destroy']]);
        $this->service = new InvTransactionService();
    }

    public function index()
    {
        $transactions = InvTransaction::with('entries.account')->latest()->get();
        return $this->successResponse(InvTransactionResource::collection($transactions));
    }

    public function list(ListRequest $request)
    {
        $input = $request->all();
        //
        return InvTransactionResource::collection(InvTransaction::search($input['keywords'])
//            ->orderBy($input['orderBy'], $input['orderDesc'] ? 'desc' : 'asc')
            ->paginate($input['limit']));
    }


    public function getPending(TypeRequest $request)
    {
        return $this->successResponse(['orders'=>InvTransactionResource::collection($this->service->getPending($request->get('type')))]);
    }

    public function accept(Request $request,$code)
    {
        DB::beginTransaction();
        try {
            $this->service->accept($code);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e->getMessage(), 409);
        }
        DB::commit();
        return $this->successResponse(null, __('Accepted Successfully'));
    }

    public function create(TypeRequest $request)
    {
        if (auth('api')->user()->cannot('inventory.transactions.create')) {
            return $this->deniedResponse(null, null, 403);
        }
        $warehouses = (new WarehouseService())->all(['name', 'id']);
        $users = (new UserService())->all(['username', 'id']);
        if (in_array($request->type,['RS','IR'])){
         $others = InvTransaction::$inOther;
        }else{
            $others = InvTransaction::$outOther;
        }

        $TransactionCode = InvTransaction::where('type',$request->get('type'))->latest()->value('code')  ;

        if (preg_match('/^[A-z]*-[1-9]*-(\d+)/', $TransactionCode, $matches)) {
            $count = $matches[1] + 1;
        }

        return $this->successResponse([
            'count' => $count ?? null,
            'warehouses' => $warehouses,
            'users' => $users,
            'others' => $others
        ]);
    }

    public function show(Request $request, $code)
    {
            $transaction = InvTransaction::with('warehouse','group.ledgers', 'group.transactions','group.invTransactions', 'group.po','group.so', 'items.product', 'secondParty', 'responsible')->where('code', $code)->firstOrFail();
            return $this->successResponse(new  InvTransactionResource($transaction));
    }

    public function store(StoreInvTransactionRequest $request)
    {
        $data = $request->validated();

        DB::beginTransaction();
        try {
            $groupId = ModelGroup::create()->id;
                $this->service->createType(type: $data['type'], groupId: $groupId,items: $data['items'], amount: $data['total'], warehouse_id: $data['warehouse_id'], supplier_id: $data['supplier_id'] ?? null,client_id: $data['client_id'] ?? null, due: $data['date'], note: $data['note'] ?? null, user_id: $data['responsible'] ?? null, paper_ref: $data['paper_ref'] ?? null, system: 0);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e->getMessage(), 409);
        }
        DB::commit();

        return $this->successResponse(null, __('message.created', ['model' => __('Inventory Transaction')]), 201);
    }

    public function storeType(StoreTransactionTypeRequest $request)
    {
        $data = $request->validated();

        $credit_account = Account::where('code', $data['credit_account'])->value('id');
        $debit_account = Account::where('code', $data['debit_account'])->value('id');
        $costCenter = CostCenter::where('code', $data['cost_center'])->value('id');

        if (!$credit_account || !$debit_account) {
            return $this->errorResponse('Accounts Not Found', 404);
        }

        $e = (new LedgerService())->cashin($debit_account, $credit_account, $data["amount"], $costCenter, $data['due'] ?? now(), $data["description"], $data['user_id'] ?? null);
        if (is_string($e)) {
            return $this->errorResponse($e);
        }

        return $this->successResponse([], 'Transaction created successfully', 201);
    }

}
