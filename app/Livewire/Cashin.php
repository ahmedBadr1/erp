<?php

namespace App\Livewire;

use App\Models\Accounting\Account;
use App\Models\Accounting\Category;
use Livewire\Component;

class Cashin extends Component
{
    public $accounts = [];
    public $cashAccounts = [];

    public function mount(){
        $cashCategory = Category::with(['descendants'=>fn($q)=>$q->with('accounts'),'accounts'])->where('name','cash')->first();
        $this->cashAccounts = collect($cashCategory->accounts);
        foreach ($cashCategory->descendants as $child){
            $this->cashAccounts = $this->cashAccounts->merge($child->accounts);
        }
                dd($this->cashAccounts);
    }
    public function render()
    {
        $ids = $this->cashAccounts->pluck('id')->toArray();
//        dd($ids);
        $this->accounts = Account::credit()->pluck('name','id')->toArray();
        $this->cashAccounts = Account::where()->pluck('name','id')->toArray();
        return view('livewire.cashin');
    }
}
