<?php

namespace App\Http\Controllers\Admin\Sales;

use App\Http\Controllers\Controller;
use App\Models\Sales\PriceOffer;
use Illuminate\Http\Request;

class PriceOfferController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->class = "price-offer";
        $this->table = "price-offers";
        $this->middleware('auth');
        $this->middleware('permission:sales.price-offers.view', ['only' => ['index', 'show']]);
        $this->middleware('permission:sales.price-offers.create', ['only' => ['create', 'store']]);
        $this->middleware('permission:sales.price-offers.edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:sales.price-offers.delete', ['only' => ['destroy']]);
    }
    public function index(){
        $tree = $this->tree;
        $chart = Category::account()->tree()->with('accounts')->get()->toTree();
        return view('admin.sales.price-offers.index', compact('tree'));
    }

    public function create()
    {
        $tree = array_merge($this->tree, [route('admin.sales.price-offers.index') => 'price-offers']);
        return view('admin.sales.price-offers.create', compact('tree'));
    }

    public function edit(string $id)
    {
        $tree = array_merge($this->tree, [route('admin.sales.price-offers.index') => 'banners-setting']);
        return view('admin.sales.price-offers.create', compact('tree','id'));
    }

}
