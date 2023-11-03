<?php

namespace App\Http\Controllers\Admin\Accounting;

use App\Http\Controllers\Controller;
use App\Http\Controllers\MainController;
use App\Models\Accounting\Category;
use Illuminate\Http\Request;

class TransactionsController extends MainController
{
    public function __construct()
    {
        parent::__construct();
        $this->class = "accounts";
        $this->table = "accounts";
        $this->middleware('auth');
        $this->middleware('permission:accounting.accounts.view', ['only' => ['index', 'show']]);
        $this->middleware('permission:accounting.accounts.create', ['only' => ['create', 'store']]);
        $this->middleware('permission:accounting.accounts.edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:accounting.accounts.delete', ['only' => ['destroy']]);
    }
    public function index(){
        $tree = $this->tree;
        $chart = Category::account()->tree()->with('accounts')->get()->toTree();
        return view('admin.accounting.accounts.index', compact('tree'));
    }

    public function create()
    {
        $tree = array_merge($this->tree, [route('admin.accounting.accounts.index') => 'accounts']);
        return view('admin.accounting.accounts.create', compact('tree'));
    }

    public function edit(string $id)
    {
        $tree = array_merge($this->tree, [route('admin.accounting.accounts.index') => 'accounts']);
        return view('admin.accounting.accounts.create', compact('tree','id'));
    }

    public function cashIn( )
    {
        $tree = array_merge($this->tree, [route('admin.accounting.accounts.index') => 'accounts']);
        return view('admin.accounting.cashin', compact('tree'));
    }

    public function cashOut()
    {
        $tree = array_merge($this->tree, [route('admin.accounting.accounts.index') => 'accounts']);
        return view('admin.accounting.cashout', compact('tree'));
    }
}
