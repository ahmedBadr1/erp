<?php

namespace App\Http\Controllers\Api\Accounting;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Accounting\PostingRequest;
use App\Http\Requests\Accounting\StoreTransactionRequest;
use App\Http\Requests\Accounting\StoreTransactionTypeRequest;
use App\Http\Requests\ListRequest;
use App\Http\Resources\Accounting\LedgerResource;
use App\Http\Resources\Accounting\TransactionResource;
use App\Models\Accounting\Account;
use App\Models\Accounting\CostCenter;
use App\Models\Accounting\Ledger;
use App\Models\Accounting\Transaction;
use App\Services\Accounting\LedgerService;
use App\Services\System\ModelGroupService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends ApiController
{
    public function __construct()
    {
        parent::__construct();
        $this->class = "transactions";
        $this->table = "transactions";
        $this->middleware('auth');
        $this->middleware('permission:accounting.transactions.view', ['only' => ['index', 'show']]);
        $this->middleware('permission:accounting.transactions.create', ['only' => ['create', 'store']]);
        $this->middleware('permission:accounting.transactions.edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:accounting.transactions.delete', ['only' => ['destroy']]);
        $this->service = new LedgerService();
    }

    public function index()
    {
        $transactions = Transaction::with('entries.account')->latest()->get();
        return $this->successResponse(TransactionResource::collection($transactions));
    }

    public function list(ListRequest $request)
    {
        $input = $request->all();
        //
        return TransactionsResourses::collection(Transaction::search($input['keywords'])
//            ->orderBy($input['orderBy'], $input['orderDesc'] ? 'desc' : 'asc')
            ->paginate($input['limit']));
    }

    public function posting(PostingRequest $request)
    {
        $this->service->post($request->get('ids'), $request->get('posting'));
        return $this->successResponse(null, __(($request->get('posting') ? '' : 'Un ') . 'Posted Successfully'));
    }

    public function create()
    {
        $accounts = Account::with(['node' => fn($q) => $q->select('id', 'name')])->get(['id', 'name', 'node_id']);
        return $this->successResponse($accounts);
    }

    public function show(Request $request, $code)
    {
        if (preg_match('/^JE-(\d+)/', $code, $matches)) {
            $id = $matches[1];
            $ledger = Ledger::with('transactions', 'group.invTransactions', 'group.ledgers', 'group.transactions','group.po','group.so', 'entries.account', 'entries.costCenter', 'responsible')->whereId($id)->firstOrFail();
            return $this->successResponse(new  LedgerResource($ledger));
        } else {
            $transaction = Transaction::with('ledger', 'group.invTransactions', 'group.ledgers', 'group.transactions','group.po','group.so', 'ledger.entries', 'ledger.entries.account', 'ledger.entries.costCenter', 'firstParty', 'secondParty', 'responsible')->where('code', $code)->firstOrFail();
            return $this->successResponse(new  TransactionResource($transaction));
        }
    }

    public function store(StoreTransactionRequest $request)
    {
        $data = $request->validated();
//        return $data['accounts'];
        DB::beginTransaction();
        try {
            $group = (new ModelGroupService)->store();
            if ($data['type'] === 'CI') {
                $this->service->cashin(groupId: $group->id, treasuryId: $data['treasury'], accounts: $data['accounts'], amount: $data['amount'], currencyId: $data['currency_id'], date: $data['due'] ?? null, note: $data['note'] ?? null, paperRef: $data['paper_ref'] ?? null, responsible: $data['responsible'] ?? auth('api')->id(), system: 0);
            } elseif ($data['type'] === 'CO') {
                $this->service->cashout(groupId: $group->id, treasuryId:  $data['treasury'], accounts: $data['accounts'], amount: $data['amount'], currencyId: $data['currency_id'], date: $data['due'] ?? null, note: $data['note'] ?? null, paperRef: $data['paper_ref'] ?? null, responsible: $data['responsible'] ?? auth('api')->id(), system: 0);
            } else {
                $this->service->jouranlEntry(data: $data, groupId: $group->id);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e->getMessage(), 409);
        }
        DB::commit();

        return $this->successResponse(null, __('message.created', ['model' => __('Transaction')]), 201);
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
