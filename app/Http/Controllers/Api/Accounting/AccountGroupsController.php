<?php

namespace App\Http\Controllers\Api\Accounting;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Accounting\RelatedAccountRequest;
use App\Http\Resources\Accounting\AccountChartResource;
use App\Http\Resources\NameResource;
use App\Services\Accounting\AccountGroupService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccountGroupsController extends ApiController
{
    public function __construct()
    {
        parent::__construct();
        $this->class = "accounts";
        $this->table = "accounts";
        $this->middleware('auth');
        $this->middleware('permission:accounting.accounts.related', ['only' => ['index', 'edit']]);
        $this->service = new AccountGroupService();
    }


    public function index(Request $request, $code)
    {
        if (auth('api')->user()->cannot('accounting.accounts.related')) {
            return $this->deniedResponse(null, null, 403);
        }
        [$account, $relatedAccounts] = $this->service->get($code);


        return $this->successResponse(['account' => new NameResource($account), 'accounts' => NameResource::collection($relatedAccounts)]);
    }


    public function store(RelatedAccountRequest $request)
    {
        $data =$request->validated() ;
        DB::beginTransaction();
        try {
           $this->service->update($data['account'] , $data['accounts'] ?? []);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e->getMessage(), 409);
        }
        return $this->successResponse(null, __('message.updated', ['model' => __('names.account')]));
    }

}
