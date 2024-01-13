<?php

namespace App\Http\Controllers\Api\Accounting;

use App\Filters\ByCode;
use App\Filters\ByName;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Accounting\CreateTaxRequest;
use App\Http\Requests\Accounting\UpdateTaxRequest;
use App\Http\Requests\ListRequest;
use App\Http\Resources\Accounting\TaxResource;
use App\Models\Accounting\Account;
use App\Models\Accounting\Tax;
use App\Services\Accounting\TaxService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Pipeline;

class TaxesController extends ApiController
{
    public function __construct()
    {
        parent::__construct();
        $this->class = "taxes";
        $this->table = "taxes";
        $this->middleware('auth');
        $this->middleware('permission:accounting.taxes.view', ['only' => ['index', 'show']]);
        $this->middleware('permission:accounting.taxes.create', ['only' => ['create', 'store']]);
        $this->middleware('permission:accounting.taxes.edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:accounting.taxes.delete', ['only' => ['destroy']]);
        $this->service = new TaxService();
    }

    public function index(){
       $taxes = TaxResource::collection(Tax::with('account')->get());
       return $this->successResponse(['taxes'=>$taxes]);
    }

    public function list(ListRequest $request)
    {
        return  TaxResource::collection($this->service->search($request->get('keywords'))->get());
    }

    public function taxes(ListRequest $request)
    {
        return $this->successResponse(Pipeline::send(Tax::search($request->get('keywords'))->active())
            ->through([ByName::class,ByCode::class])
            ->thenReturn()
            ->orderBy($request->get('orderBy') ?? $this->orderBy, ($request->get('orderDesc') ?? $this->orderDesc) ? 'desc' : 'asc')
            ->limit($request->get('limit') ?? $this->limit)->pluck('name', 'id')->toArray());
    }

    public function show(\Illuminate\Support\Facades\Request $request, Tax $tax)
    {
        return $tax->active ? $this->successResponse(new TaxResource($tax)): $this->errorResponse('Not Active',404);
    }
    public function create(){
        $accounts = Account::with()->get(['id','name','type','code']);
        return $this->successResponse($accounts);
    }
    public function store(CreateTaxRequest $request)
    {
        $data = $request->validated();

        try {
            DB::beginTransaction();

            Tax::create($data);

            DB::commit();

            return $this->successResponse(null,__('message.created',['model'=>__('Tax')]),201);
        } catch (\Exception $e) {
            DB::rollback();
            return  $this->errorResponse($e->getMessage());
        }
    }

    public function update(UpdateTaxRequest $request,int $id)
    {
        $data = $request->validated();
//        return $data ;
        $tax = Tax::findOrFail($id);
        $tax->update($data);
        $taxes = TaxResource::collection(Tax::with('account')->get());
//            sleep(3);
        return $this->successResponse(['taxes'=>$taxes],__('message.updated',['model'=>__('Tax')]),201);
    }

    public function destroy(Tax $tax)
    {
//        if ($tax->id === 1){
            return $this->errorResponse('Can\'t Delete this Tax, Try Deactivate it');
//        }
        $tax->delete();

        return $this->successResponse(null,__('message.deleted',['model'=>__('Tax')]),201);

    }

}
