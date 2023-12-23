<?php

namespace App\Http\Controllers\Api\Accounting;

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Controller;
use App\Http\Requests\ListRequest;
use App\Http\Resources\Accounting\AccCategoryResource;
use App\Http\Resources\Accounting\AccountChartResource;
use App\Http\Resources\Accounting\AccountResource;
use App\Models\Accounting\AccCategory;
use App\Models\Accounting\Account;
use App\Services\Accounting\AccountService;
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
        $this->service = new AccountService();
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

    public function journal(ListRequest $request){

        $accounts = $this->service->search($request->get('keywords'))
            ->when($request->get('start_date'), function ($query) use ($request) {
                $query->where('created_at', '>=',$request->get('start_date'));
            })
            ->when($request->get('end_date'), function ($query) use ($request) {
                $query->where('created_at','<=', $request->get('start_date'));
            })
            ->with('category')
            ->withSum(['entries as credit_sum' => function ($query) {
                $query->where('credit', true);}],'amount')
            ->withSum(['entries as debit_sum' => function ($query) {
                $query->where('credit', false);}],'amount')
            ->orderBy($request->get('OrderBy') ?? $this->orderBy, $request->get('OrderBy')  ?? $this->orderDesc ? 'desc' : 'asc')
           ->get();
        return $this->successResponse(['accounts'=> AccountResource::collection($accounts)]); //AccountResource
    }

    public function show(Request $request , $code)
    {
        $account = Account::with(['entries.transaction','transactions','category'])
            ->withSum(['entries as credit_sum' => function ($query) {
                $query->where('locked',false)->where('post',false)->where('credit', true);}],'amount')
            ->withSum(['entries as debit_sum' => function ($query) {
                $query->where('locked',false)->where('post',false)->where('credit', false);}],'amount')
            ->where('code',$code)
            ->firstOrFail();

     return $this->successResponse(new AccountResource($account));  ;
    }

}
