<?php

namespace App\Http\Controllers\Admin\Purchases;

use App\Http\Controllers\Controller;
use App\Http\Controllers\MainController;
use App\Models\Purchases\Bill;
use Illuminate\Http\Request;

class ReturnsController extends MainController
{
    public function __construct()
    {
        parent::__construct();
        $this->class = "returns";
        $this->table = "purchase_returns";
        $this->middleware('auth');
        $this->middleware('permission:purchases.returns.view', ['only' => ['index', 'show']]);
        $this->middleware('permission:purchases.returns.create', ['only' => ['create', 'store']]);
        $this->middleware('permission:purchases.returns.edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:purchases.returns.delete', ['only' => ['destroy']]);
    }

    public function index(){
        $tree = $this->tree;
        $chart = Category::account()->tree()->with('accounts')->get()->toTree();
        return view('admin.purchases.returns.index', compact('tree'));
    }

    public function create()
    {
        $tree = array_merge($this->tree, [route('admin.purchases.returns.index') => 'purchase_returns']);
        return view('admin.purchases.payments.create', compact('tree'));
    }

    public function edit(string $id)
    {
        $tree = array_merge($this->tree, [route('admin.purchases.returns.index') => 'purchase_returns']);
        return view('admin.purchases.returns.create', compact('tree','id'));
    }
}
