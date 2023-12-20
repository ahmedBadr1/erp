<?php

namespace App\Http\Controllers\Api\Accounting;

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Controller;
use App\Http\Resources\Accounting\AccCategoryResource;
use App\Http\Resources\Accounting\AccountChartResource;
use App\Models\Accounting\AccCategory;
use App\Models\Accounting\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AccountsController extends ApiController
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
        $tree = AccCategory::tree()->withCount('children')->with('accounts')->get()->toTree();
        return $this->successResponse(['tree'=>AccCategoryResource::collection($tree)]);
    }

    public function list(){
        $accounts = Account::active()->pluck('name','code')->toArray();
        return $this->successResponse(['accounts'=>$accounts]);
    }

    public function duplicateCategory($id){
        $category = AccCategory::find($id);
        AccCategory::create([
            'name' => $category->name . ' copy',
            'slug' => Str::slug($category->name . ' copy'),
            'credit' => $category->credit,
            'parent_id' => $category->parent_id,
            'system' => 0,
            'usable' => 1
        ]);
        $this->successResponse(__('message.created',['model'=>__('Category')]));
    }

    public function duplicateAccount($id){
        $account = Account::find($id);
        Account::create([
            'name' => $account->name . ' copy',
            'credit' => $account->credit,
            'category_id' => $account->category_id,
            'currency_id' => $account->currency_id,
            'description' => $account->description,
            'system' => 0,
        ]);
        $this->successResponse(__('message.created',['model'=>__('Account')]));
    }

}
