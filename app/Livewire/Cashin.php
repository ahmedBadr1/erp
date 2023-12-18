<?php

namespace App\Livewire;

use App\Livewire\Basic\BasicForm;
use App\Models\Accounting\Account;
use App\Models\Accounting\AccCategory;
use App\Models\Accounting\Entry;
use App\Models\Accounting\Transaction;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Rule;
use Livewire\Component;

class Cashin extends BasicForm
{
    public $credit_account, $debit_account, $amount, $description;
//    #[Rule([
//        'entries' => 'required',
//        'entries.*.account_id' => [
//            'required',
//            'exists:accounts,id',
//        ],
//        'entries.*.amount'=> ['required','numeric','gt:0']
//    ])]
//    public array $entries ;
    public $accounts = [];
    public $cashAccounts = [];
    public $cashOut =false ;

    public function mount($cashOut = false)
    {
        $this->cashOut = $cashOut ;
        $cashCategory = AccCategory::with(['descendants' => fn($q) => $q->with('accounts'), 'accounts'])->where('slug', 'alnkdy')->first();
        $this->cashAccounts = collect($cashCategory->accounts);
        foreach ($cashCategory->descendants as $child) {
            $this->cashAccounts = $this->cashAccounts->merge($child->accounts);
        }
//        if ($this->cashOut ){
//            $category = Category::with(['descendants' => fn($q) => $q->with('accounts'), 'accounts'])->where('slug', 'almsrofat')->first();
//        }else{
//            $category = Category::with(['descendants' => fn($q) => $q->with('accounts'), 'accounts'])->where('slug', 'alayradat')->first();
//        }
        $this->accounts = Account::active()->get();
//        $category = Category::with(['descendants' => fn($q) => $q->with('accounts'), 'accounts'])->get();
//        $this->accounts = collect($category->accounts);
//        foreach ($category->descendants as $child) {
//            $this->accounts = $this->accounts->merge($child->accounts);
//        }
    }

    public function render()
    {
//        $ids = $this->cashAccounts->pluck('id')->toArray();
//        $this->accounts = Account::credit()->pluck('name','id')->toArray();
//        $this->cashAccounts = Account::where()->pluck('name','id')->toArray();
        return view('livewire.cashin');
    }

    public function save()
    {
        $validated = $this->validate();

        DB::transaction(function () use ($validated) {
            $transaction = Transaction::create([
                'amount' => $validated['amount'],
                'description' => $validated['description'] ??  $this->cashOut ?  'cash out': 'cash in' ,
                'type' => $this->cashOut ?  'co': 'ci',
                'due' => now(),//$validated['due']
                'user_id' => auth()->id()
            ]);
            Entry::create([
                'amount' => $validated['amount'],
                'credit' => 1,
                'account_id' => $validated['credit_account'],
                'transaction_id' => $transaction->id
            ]);

            Entry::create([
                'amount' => $validated['amount'],
                'credit' => 0,
                'account_id' => $validated['debit_account'],
                'transaction_id' => $transaction->id
            ]);

        });
        $this->toast(__('message.created', ['model' => __('names.transaction')]));
        $this->reset();
        return redirect()->route('admin.accounting.entries.index')->with('success', __('message.updated', ['model' => __('names.entry')]));
    }

    public function rules()
    {
        return [
            'credit_account' => 'required|exists:accounts,id',
            'debit_account' => 'required|exists:accounts,id',
            'amount' => 'required|numeric|gt:0',
            'description' => 'nullable|string',
        ];
    }
}
