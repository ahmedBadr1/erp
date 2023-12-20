<?php

namespace App\Http\Controllers\Api\Accounting;

use App\Http\Controllers\Controller;
use App\Http\Requests\ListRequest;
use App\Models\Accounting\Account;
use App\Models\Accounting\Entry;
use App\Models\Accounting\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EntriesController extends Controller
{
    public function __construct()
    {
        return $this->middleware('permission:accounting');
    }

    public function index(){
       $transactions = Transaction::with('entries')->paginate(10);
       return $this->successResponse($transactions);
    }

    public function list(ListRequest $request)
    {
        $input = $request->all();
        //
        return  TransactionsResourses::collection( Transaction::search($input['keywords'])
//            ->orderBy($input['orderBy'], $input['orderDesc'] ? 'desc' : 'asc')
            ->paginate($input['limit']));
    }

    public function create(){
        $accounts = Account::with(['category'=> fn($q)=>$q->select('id','name')])->get(['id','name','type']);
        return $this->successResponse($accounts);
    }
     public function store(Request $request)
        {
//            return $this->successResponse($request->all());
            $data = $request->validate([
                'description' => 'required',
                'date' => 'required|date',
//                'user_id' => 'required|exits',
                'entries.*.account_id' => 'required',
                'entries.*.credit' => 'required|numeric|min:0',
                'entries.*.debit' => 'required|numeric|min:0',
            ]);

            try {
                DB::beginTransaction();

                $transaction = Transaction::create([
                    'description' => $data['description'],
                    'date' => $data['date'],
                    'user_id' => auth()->id(),//$data['user_id']
                ]);

                $credit = 0;
                $debit = 0;
                $total = 0;
                foreach ($data['entries'] as $entry) {
                    $transactionEntry = new Entry([
                        'account_id' => $entry['account_id'],
                        'credit' => $entry['credit'],
                        'debit' => $entry['debit'],
                    ]);

                    $transaction->entries()->save($transactionEntry);

                    $credit += $entry['credit'] ;
                    $debit += $entry['debit'];
                }
                if ($credit !== $debit){
                    return response()->json(['message' => 'transaction is not balanced'], 500);

                }
                    $total = $credit ;
                $transaction->total = $total;
                $transaction->save();

                DB::commit();

                return response()->json(['message' => 'Transaction created successfully'], 201);
            } catch (\Exception $e) {
                DB::rollback();

                return response()->json(['message' => 'Failed to create Transaction','error'=>$e], 500);
            }
        }

}
