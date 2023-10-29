<?php

namespace App\Http\Controllers\Admin\Sales;

use App\Http\Controllers\Controller;
use App\Http\Controllers\MainController;
use App\Models\sales\Bill;
use Illuminate\Http\Request;

class ReturnsController extends MainController
{
    public function __construct()
    {
        parent::__construct();
        $this->class = "returns";
        $this->table = "sales_returns";
        $this->middleware('auth');
        $this->middleware('permission:sales.returns.view', ['only' => ['index', 'show']]);
        $this->middleware('permission:sales.returns.create', ['only' => ['create', 'store']]);
        $this->middleware('permission:sales.returns.edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:sales.returns.delete', ['only' => ['destroy']]);
    }

    public function index(){
        $tree = $this->tree;
        $chart = Category::account()->tree()->with('accounts')->get()->toTree();
        return view('admin.sales.returns.index', compact('tree'));
    }

    public function create()
    {
        $tree = array_merge($this->tree, [route('admin.sales.returns.index') => 'sales_returns']);
        return view('admin.sales.payments.create', compact('tree'));
    }

    public function edit(string $id)
    {
        $tree = array_merge($this->tree, [route('admin.sales.returns.index') => 'sales_returns']);
        return view('admin.sales.returns.create', compact('tree','id'));
    }
}
