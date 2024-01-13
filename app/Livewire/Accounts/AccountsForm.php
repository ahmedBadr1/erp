<?php

namespace App\Livewire\Accounts;

use App\Livewire\Basic\BasicForm;
use App\Models\Accounting\Account;
use App\Models\Accounting\Currency;
use App\Models\Accounting\Node;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Rule;

class AccountsForm extends  BasicForm
{
    #[Rule('required|string')]
    public $name ;

    #[Rule('required|string')]
    public $description ;
    #[Rule('required|numeric')]
    public $opening_balance = 0;

    #[Rule('nullable|date')]
    public $opening_date  ;

    #[Rule('required|boolean')]
    public $active = true;

    #[Rule('required|exists:nodes,id')]
    public $node_id ;

    #[Rule('required|exists:currencies,id')]
    public $currency_id ;


    public $account  ;
    public $nodes = [] ;
    public $currencies = [] ;

    public function mount($id = null){
        if ($id) {
            $this->account = Account::with('node','currency')->whereId($id)->first() ;
            $this->name = $this->account->name ;
            $this->type = $this->account->type ;
            $this->active = $this->account->active ;
            $this->node_id = $this->account->node_id ;
            $this->currency_id = $this->account->currency_id ;
            $this->description = $this->account->description ;
            $this->title = 'edit';
            $this->button = 'update';
            $this->color = 'primary';
        }
    }
    public function render()
    {
        $this->nodes = Node::isLeaf()->active()->pluck('name','id')->toArray();
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
            $node = Node::withCount('accounts')->whereId($validated['node_id'])->first();
            if (!$node){
                $this->toast(__('Node Not Found'),'error');
                return ;
            }

            DB::transaction(function () use ($validated,$node){
                $account =  Account::create([
                    'name' => $validated['name'],
                    'code' => $node->id . ((int)$node->accounts_count + 1),
                    'description' => $validated['description'],
                    'currency_id' => $validated['currency_id'],
                    'node_id' => $validated['node_id'],
                    'opening_balance' => $validated['opening_balance'],
                    'opening_date' => $validated['opening_date'],
                    'credit'  => $node->credit,

                ]);
//                if ($validated['opening_balance']){
//                    $transaction =  Transaction::create([
//                        'amount' => $validated['opening_balance'],
//                        'description' =>'Opening Balance For Account' . $account->name,
//                        'type' => 'user',
//                        'due' => $validated['opening_date'] ?? now(),//$validated['due']
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
