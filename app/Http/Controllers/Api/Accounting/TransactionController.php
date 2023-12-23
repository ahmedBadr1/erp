<?php

namespace App\Http\Controllers\Api\Accounting;

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Controller;
use App\Http\Requests\ListRequest;
use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\StoreTransactionTypeRequest;
use App\Http\Resources\Accounting\TransactionResource;
use App\Models\Accounting\Account;
use App\Models\Accounting\Entry;
use App\Models\Accounting\Transaction;
use App\Services\Accounting\EntryService;
use App\Services\Accounting\TransactionService;
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
        $transactions = Transaction::with('entries.account')->get();
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
        $accounts = Account::with(['category' => fn($q) => $q->select('id', 'name')])->get(['id', 'name', 'acc_category_id']);
        return $this->successResponse($accounts);
    }

    public function show(Request $request, $id)
    {
        $transaction = Transaction::with('entries.account')->first();
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
                Entry::create([
                    'amount' => $ent['amount'],
                    'credit' => $ent['credit'],
                    'account_id' => $ent['account_id'],
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

        DB::transaction(function () use ($data) {
            $transaction = Transaction::create([
                'amount' => $data['amount'],
                'description' => $data['description'] ?? $data['type'] == 'co' ? 'cash out' : 'cash in',
                'type' => $data['type'] ,
                'due' => now(),//$validated['due']
                'user_id' => auth('api')->id()
            ]);
            Entry::create([
                'amount' => $data['amount'],
                'credit' => 1,
                'account_id' => $data['credit_account'],
                'transaction_id' => $transaction->id
            ]);

            Entry::create([
                'amount' => $data['amount'],
                'credit' => 0,
                'account_id' => $data['debit_account'],
                'transaction_id' => $transaction->id
            ]);
        });
        return $this->successResponse([], 'Transaction created successfully', 201);
    }

}
