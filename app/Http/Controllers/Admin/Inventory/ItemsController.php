<?php

namespace App\Http\Controllers\Admin\Inventory;

use App\Http\Controllers\MainController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ItemsController extends MainController
{
    public function __construct()
    {
        parent::__construct();
        $this->class = "warehouse";
        $this->table = "warehouses";
        $this->middleware('auth');
        $this->middleware('permission:inventory.warehouses.view', ['only' => ['index', 'show']]);
        $this->middleware('permission:inventory.warehouses.create', ['only' => ['create', 'store']]);
        $this->middleware('permission:inventory.warehouses.edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:inventory.warehouses.delete', ['only' => ['destroy']]);
    }
    public function index(){
        $tree = $this->tree;
        $chart = Category::account()->tree()->with('accounts')->get()->toTree();
        return view('admin.settings.platforms.banners.index', compact('tree'));
    }

    public function create()
    {
        $tree = array_merge($this->tree, [route('admin.settings.platforms.banners.index') => 'banners-setting']);
        return view('admin.settings.platforms.banners.create', compact('tree'));
    }

    public function edit(string $id)
    {
        $tree = array_merge($this->tree, [route('admin.settings.platforms.banners.index') => 'banners-setting']);
        return view('admin.settings.platforms.banners.create', compact('tree','id'));
    }
}
