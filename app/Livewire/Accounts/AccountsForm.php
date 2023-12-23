<?php

namespace App\Livewire\Accounts;

use App\Livewire\Basic\BasicForm;
use App\Models\Accounting\Account;
use App\Models\Accounting\AccCategory;
use App\Models\Accounting\Entry;
use App\Models\Accounting\Transaction;
use App\Models\System\Currency;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;

class AccountsForm extends  BasicForm
{
    #[Rule('required|string')]
    public $name ;

    #[Rule('required|string')]
    public $description ;
    #[Rule('required|numeric')]
    public $opening_balance = 0;

    #[Rule('nullable|date')]
    public $opening_balance_date  ;

    #[Rule('required|boolean')]
    public $active = true;

    #[Rule('required|exists:acc_categories,id')]
    public $category_id ;

    #[Rule('required|exists:currencies,id')]
    public $currency_id ;


    public $account  ;
    public $categories = [] ;
    public $currencies = [] ;

    public function mount($id = null){
        if ($id) {
            $this->account = Account::with('category','currency')->whereId($id)->first() ;
            $this->name = $this->account->name ;
            $this->type = $this->account->type ;
            $this->active = $this->account->active ;
            $this->category_id = $this->account->category_id ;
            $this->currency_id = $this->account->currency_id ;
            $this->description = $this->account->description ;
            $this->title = 'edit';
            $this->button = 'update';
            $this->color = 'primary';
        }
    }
    public function render()
    {
        $this->categories = AccCategory::isLeaf()->active()->pluck('name','id')->toArray();
        $this->currencies = Currency::active()->pluck('name','id')->toArray();
        return view('livewire.accounts.accounts-form');
    }

    public  function save()
    {
        $validated =  $this->validate();
//        dd($validated);
        if ($this->account) {
            $this->account->update([
                'name' => $validated['name'],
                'description' => $validated['description']
            ]);
            $this->toast( __('message.updated', ['model' => __('names.account')]));
        }else{
            $category = AccCategory::withCount('accounts')->whereId($validated['category_id'])->first();
            if (!$category){
                $this->toast(__('Category Not Found'),'error');
                return ;
            }

            DB::transaction(function () use ($validated,$category){
                $account =  Account::create([
                    'name' => $validated['name'],
                    'code' => $category->id . ((int)$category->accounts_count + 1),
                    'description' => $validated['description'],
                    'currency_id' => $validated['currency_id'],
                    'category_id' => $validated['category_id'],
                    'opening_balance' => $validated['opening_balance'],
                    'opening_balance_date' => $validated['opening_balance_date'],
                    'credit'  => $category->credit,

                ]);
//                if ($validated['opening_balance']){
//                    $transaction =  Transaction::create([
//                        'amount' => $validated['opening_balance'],
//                        'description' =>'Opening Balance For Account' . $account->name,
//                        'type' => 'user',
//                        'due' => $validated['opening_balance_date'] ?? now(),//$validated['due']
//                        'user_id' => auth()->id()
//                    ]);
//                    Entry::create([
//                        'amount' => $validated['opening_balance'],
//                        'credit' =>$account->credit,
//                        'account_id' => $account->id,
//                        'transaction_id' => $transaction->id
//                    ]);
//                }

            });
//            $user =auth()->user() ;
//            activity()
//                ->performedOn($entry)
//                ->causedBy($user)
//                ->event('updated')
//                ->useLog($user->name)
//                ->log('entry Has been Updated');
            $this->toast(__('message.created',['model'=>__('names.account')]));
        }

        $this->reset();
        return redirect()->route('admin.accounting.accounts.index')->with('success', __('message.updated',['model'=>__('names.account')]));
    }
}
