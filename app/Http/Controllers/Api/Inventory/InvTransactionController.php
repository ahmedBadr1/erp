<?php

namespace App\Http\Controllers\Api\Inventory;

use App\Http\Controllers\Api\Accounting\TransactionsResourses;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Accounting\PostingRequest;
use App\Http\Requests\Accounting\StoreTransactionRequest;
use App\Http\Requests\Accounting\StoreTransactionTypeRequest;
use App\Http\Requests\Inventory\StoreInvTransactionRequest;
use App\Http\Requests\ListRequest;
use App\Http\Requests\TypeRequest;
use App\Http\Resources\Accounting\LedgerResource;
use App\Http\Resources\Accounting\TransactionResource;
use App\Http\Resources\Inventory\InvTransactionResource;
use App\Models\Accounting\Account;
use App\Models\Accounting\CostCenter;
use App\Models\Accounting\Ledger;
use App\Models\Accounting\Transaction;
use App\Models\Accounting\TransactionGroup;
use App\Models\Inventory\InvTransaction;
use App\Models\Inventory\Warehouse;
use App\Models\System\Status;
use App\Services\Accounting\AccountService;
use App\Services\Accounting\CurrencyService;
use App\Services\Accounting\LedgerService;
use App\Services\Hr\BranchService;
use App\Services\Inventory\InvTransactionService;
use App\Services\Inventory\WarehouseService;
use App\Services\System\TaxService;
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

    public function pending(PostingRequest $request)
    {
        $this->service->pending($request->get('ids'), $request->get('posting'));
        return $this->successResponse(null, __(($request->get('posting') ? '' : 'Un ') . 'Posted Successfully'));
    }

    public function create(TypeRequest $request)
    {
        if (auth('api')->user()->cannot('inventory.transactions.create')) {
            return $this->deniedResponse(null, null, 403);
        }
        $warehouses = (new WarehouseService())->all(['name', 'id']);
        $users = (new UserService())->all(['username', 'id']);
        if (in_array($request->type,['RS','IR'])){
            $others = [
                [
                    'id' => 1,
                    'name' => 'إستبدالات',
                ],
                [
                    'id' => 2,
                    'name' => 'تسوية زيادة الجرد',
                ],
            ];
        }else{
            $others = [
                [
                    'id' => 3,
                    'name' => 'أصول ثابتة',
                ],
                [
                    'id' => 4,
                    'name' => 'تسوية عجز الجرد',
                ],
            ];
        }


        return $this->successResponse([
            'warehouses' => $warehouses,
            'users' => $users,
            'others' => $others
        ]);
    }

    public function show(Request $request, $code)
    {
            $transaction = InvTransaction::with('from','to','group.ledgers', 'group.transactions','group.invTransactions', 'group.bills', 'items.product', 'supplier','client','bill','invoice', 'responsible')->where('code', $code)->firstOrFail();
            return $this->successResponse(new  InvTransactionResource($transaction));
    }

    public function store(StoreInvTransactionRequest $request)
    {
        $data = $request->validated();

        DB::beginTransaction();
        try {
            $groupId = TransactionGroup::create()->id;
                $this->service->createType(type:$data['type'], groupId: $groupId,items: $data['items'], amount: $data['total'], from_id: $data['warehouse_id'], supplier_id: $data['supplier_id'] ?? null,client_id: $data['client_id'] ?? null, due: $data['date'], note: $data['note'] ?? null, user_id: $data['responsible'] ?? null, paper_ref: $data['paper_ref'] ?? null, system: 0);
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
