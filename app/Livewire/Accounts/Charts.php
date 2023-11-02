<?php

namespace App\Livewire\Accounts;

use App\Models\Accounting\Category;
use Livewire\Component;

class Charts extends Component
{
    public function render()
    {
        $charts = Category::tree()->with('accounts')->get()->toTree();
        return view('livewire.accounts.charts',compact('charts'));
    }
}
