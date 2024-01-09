<?php

namespace App\Http\Controllers\Api\Accounting;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Accounting\StoreTransactionRequest;
use App\Http\Requests\Accounting\StoreTransactionTypeRequest;
use App\Http\Requests\ListRequest;
use App\Http\Resources\Accounting\TransactionResource;
use App\Models\Accounting\Account;
use App\Models\Accounting\CostCenter;
use App\Models\Accounting\Entry;
use App\Models\Accounting\Transaction;
use App\Services\Accounting\LedgerService;
use App\Services\Accounting\TransactionService;
use Exception;
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
        $this->service = new TransactionService();
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

    public function create()
    {
        $accounts = Account::with(['node' => fn($q) => $q->select('id', 'name')])->get(['id', 'name', 'node_id']);
        return $this->successResponse($accounts);
    }

    public function show(Request $request, $code)
    {
        $transaction = Transaction::with('entries.account', 'user')->where('code', $code)->firstOrFail();
        return $this->successResponse(new  TransactionResource($transaction));
    }

    public function store(StoreTransactionRequest $request)
    {
        $data = $request->validated();

        $credit = 0;
        $debit = 0;

        foreach ($data['entries'] as $ent) {
            if (empty($ent['credit'])) {
                $debit += $ent['amount'];
            } else {
                $credit += $ent['amount'];
            }
        }

        if ($credit !== $debit) {
            return $this->errorResponse(__('Entries Must Be Equals'));
        }

        try {
            DB::beginTransaction();

            $transaction = Transaction::create([
                'amount' => $credit,
                'description' => $data['description'],
                'type' => 'user',
                'due' => $data['due'] ?? now(),
                'user_id' => auth('api')->id()
            ]);
            foreach ($data['entries'] as $ent) {
                $account = Account::where('code', $ent['account_id'])->value('id');
                Entry::create([
                    'amount' => $ent['amount'],
                    'credit' => $ent['credit'],
                    'account_id' => $account,
                    'transaction_id' => $transaction->id
                ]);
            }

            DB::commit();

            return $this->successResponse([], 'Transaction created successfully', 201);
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json(['message' => 'Failed to create Transaction', 'error' => $e], 500);
        }
    }

    public function storeType(StoreTransactionTypeRequest $request)
    {
        $data = $request->validated();

        $credit_account = Account::where('code', $data['credit_account'])->value('id');
        $debit_account = Account::where('code', $data['debit_account'])->value('id');
        $costCenter = CostCenter::where('code', $data['cost_center'])->value('id');

        if (!$credit_account || !$debit_account) {
            return $this->errorResponse('Accounts Not Fount', 404);
        }

        $e = (new LedgerService())->cashin($debit_account, $credit_account, $data["amount"], $costCenter, $data['due'] ?? now(), $data["description"], $data['user_id'] ?? null);
        if (is_string($e)) {
            return $this->errorResponse($e);
        }

        return $this->successResponse([], 'Transaction created successfully', 201);
    }

}
