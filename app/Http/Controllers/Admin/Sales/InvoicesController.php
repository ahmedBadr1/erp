<?php

namespace App\Http\Controllers\Admin\Sales;

use App\Http\Controllers\Controller;
use App\Http\Controllers\MainController;
use App\Models\Sales\Invoice;
use Illuminate\Http\Request;

class InvoicesController extends MainController
{
    public function __construct()
    {
        parent::__construct();
        $this->class = "invoice";
        $this->table = "invoices";
        $this->middleware('auth');
        $this->middleware('permission:sales.invoices.view', ['only' => ['index', 'show']]);
        $this->middleware('permission:sales.invoices.create', ['only' => ['create', 'store']]);
        $this->middleware('permission:sales.invoices.edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:sales.invoices.delete', ['only' => ['destroy']]);
    }
    public function index(){
        $tree = $this->tree;
        $chart = Category::account()->tree()->with('accounts')->get()->toTree();
        return view('admin.sales.invoices.index', compact('tree'));
    }

    public function create()
    {
        $tree = array_merge($this->tree, [route('admin.sales.invoices.index') => 'invoices']);
        return view('admin.sales.invoices.create', compact('tree'));
    }

    public function edit(string $id)
    {
        $tree = array_merge($this->tree, [route('admin.sales.invoices.index') => 'banners-setting']);
        return view('admin.sales.invoices.create', compact('tree','id'));
    }
}
