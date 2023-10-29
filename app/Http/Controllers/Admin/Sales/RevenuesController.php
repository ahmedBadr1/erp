<?php

namespace App\Http\Controllers\Admin\Sales;

use App\Http\Controllers\Controller;
use App\Http\Controllers\MainController;
use App\Models\Sales\Revenue;
use App\Services\CalculationService;
use Illuminate\Http\Request;

class RevenuesController extends MainController
{
    public function __construct()
    {
        parent::__construct();
        $this->class = "revenue";
        $this->table = "revenues";
        $this->middleware('auth');
        $this->middleware('permission:sales.revenues.view', ['only' => ['index', 'show']]);
        $this->middleware('permission:sales.revenues.create', ['only' => ['create', 'store']]);
        $this->middleware('permission:sales.revenues.edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:sales.revenues.delete', ['only' => ['destroy']]);
    }
    public function index(){
        $tree = $this->tree;
        $chart = Category::account()->tree()->with('accounts')->get()->toTree();
        return view('admin.sales.revenues.index', compact('tree'));
    }

    public function create()
    {
        $tree = array_merge($this->tree, [route('admin.sales.revenues.index') => 'revenues']);
        return view('admin.sales.revenues.create', compact('tree'));
    }

    public function edit(string $id)
    {
        $tree = array_merge($this->tree, [route('admin.sales.revenues.index') => 'banners-setting']);
        return view('admin.sales.revenues.create', compact('tree','id'));
    }
}
