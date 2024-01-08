<?php

namespace App\Livewire\Accounts;

use App\Livewire\Basic\Toast;
use App\Models\Accounting\Account;
use App\Models\Accounting\Node;
use Illuminate\Support\Str;
use Livewire\Component;

class Charts extends Component
{
    use Toast;
    public function render()
    {
        $charts = Node::tree()->withCount('children')->with('accounts')->get()->toTree();

        return view('livewire.accounts.charts',compact('charts'));
    }

    public function duplicateCategory($id){
        $category = Node::find($id);
        Node::create([
           'name' => $category->name . ' copy',
            'slug' => Str::slug($category->name . ' copy'),
            'credit' => $category->credit,
            'parent_id' => $category->parent_id,
            'system' => 0,
            'usable' => 1
        ]);
        $this->toast(__('message.created',['model'=>__('Category')]));
    }

    public function duplicateAccount($id){
        $account = Account::find($id);
        Account::create([
            'name' => $account->name . ' copy',
            'credit' => $account->credit,
            'category_id' => $account->category_id,
            'currency_id' => $account->currency_id,
            'description' => $account->description,
            'system' => 0,
        ]);
        $this->toast(__('message.created',['model'=>__('Account')]));
    }
}
