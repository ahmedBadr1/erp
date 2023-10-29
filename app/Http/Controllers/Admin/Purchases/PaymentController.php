<?php

namespace App\Http\Controllers\Admin\Purchases;

use App\Http\Controllers\Controller;
use App\Http\Controllers\MainController;
use App\Models\Purchases\Payment;
use App\Services\CalculationService;
use Illuminate\Http\Request;

class PaymentController extends MainController
{
    public function __construct()
    {
        parent::__construct();
        $this->class = "payment";
        $this->table = "payments";
        $this->middleware('auth');
        $this->middleware('permission:purchases.payments.view', ['only' => ['index', 'show']]);
        $this->middleware('permission:purchases.payments.create', ['only' => ['create', 'store']]);
        $this->middleware('permission:purchases.payments.edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:purchases.payments.delete', ['only' => ['destroy']]);
    }
    public function index(){
        $tree = $this->tree;
        $chart = Category::account()->tree()->with('accounts')->get()->toTree();
        return view('admin.purchases.payments.index', compact('tree'));
    }

    public function create()
    {
        $tree = array_merge($this->tree, [route('admin.purchases.payments.index') => 'payments']);
        return view('admin.purchases.payments.create', compact('tree'));
    }

    public function edit(string $id)
    {
        $tree = array_merge($this->tree, [route('admin.purchases.payments.index') => 'payments']);
        return view('admin.purchases.payments.create', compact('tree','id'));
    }
}
