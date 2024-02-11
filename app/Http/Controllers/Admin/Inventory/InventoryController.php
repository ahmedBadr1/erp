<?php

namespace App\Http\Controllers\Admin\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Controllers\MainController;
use App\Http\Requests\ItemsInsertRequest;
use App\Models\Element;
use App\Models\Inventory\Warehouse;
use App\Models\Inventory\ItemHistory;
use Illuminate\Http\Request;


class InventoryController extends MainController
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

    public function pending()
    {
        $pendingItems = ItemHistory::where('type','material')->doesnthave('inventory')->orderByDesc('id')->get();

        return view('inventory.pending',compact('pendingItems'));
    }
    public function add(Request $request)
    {

       $this->validate($request,[
          'items' => 'array'
       ]);
       if (!$request->items){
           //toastError('Pleas Select some items to move');
           return back();
       }

       foreach ($request->items as $id){
          $item = ItemHistory::find($id) ;
          $item->inventory_id = 1;
          $item->update();
       }
       //toastSuccess('Items added to Warehouse successfully');
       return redirect()->route('inventory.index');
    }
    public function products()
    {
        $inventory = Warehouse::where('type','products')->first();
        return view('inventory.products',compact('inventory'));
    }

    public function productsPending()
    {
        $pendingItems = ItemHistory::where('type','product')->doesnthave('inventory')->get();

        return view('inventory.products-pending',compact('pendingItems'));
    }

    public function addProducts(Request $request)
    {

        $this->validate($request,[
            'items' => 'array'
        ]);
        if (!$request->items){
       //     toastError('Pleas Select some items to move');
            return back();
        }

        foreach ($request->items as $id){
            $item = ItemHistory::find($id) ;
            $item->inventory_id = 2;
            $item->update();
        }
       // toastSuccess('Items added to Warehouse successfully');
        return redirect()->route('inventory.products');
    }


}
