<?php

namespace App\Http\Controllers\Admin\Purchases;

use App\Http\Controllers\Controller;
use App\Models\Purchases\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->class = "supplier";
        $this->table = "suppliers";
        $this->middleware('auth');
        $this->middleware('permission:purchases.suppliers.view', ['only' => ['index', 'show']]);
        $this->middleware('permission:purchases.suppliers.create', ['only' => ['create', 'store']]);
        $this->middleware('permission:purchases.suppliers.edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:purchases.suppliers.delete', ['only' => ['destroy']]);
    }
    public function index(){
        $tree = $this->tree;
        $chart = Category::account()->tree()->with('accounts')->get()->toTree();
        return view('admin.purchases.suppliers.index', compact('tree'));
    }

    public function create()
    {
        $tree = array_merge($this->tree, [route('admin.purchases.suppliers.index') => 'suppliers']);
        return view('admin.purchases.suppliers.create', compact('tree'));
    }

    public function edit(string $id)
    {
        $tree = array_merge($this->tree, [route('admin.purchases.suppliers.index') => 'banners-setting']);
        return view('admin.purchases.suppliers.create', compact('tree','id'));
    }

}
