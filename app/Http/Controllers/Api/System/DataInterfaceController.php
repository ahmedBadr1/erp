<?php

namespace App\Http\Controllers\Api\System;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\System\DownloadTemplateRequest;
use App\Http\Requests\System\OtherPartyInterfaceRequest;
use App\Http\Requests\System\WarehouseInterfaceRequest;
use App\Models\Accounting\Account;
use App\Models\Accounting\CostCenter;
use App\Models\Inventory\Product;
use App\Models\Inventory\Warehouse;
use App\Models\Purchases\Supplier;
use App\Models\Sales\Client;
use App\Models\Sales\Service;
use App\Models\System\Group;
use App\Models\User;
use App\Services\Accounting\AccountService;
use App\Services\Accounting\CostCenterService;
use App\Services\Inventory\OtherPartyService;
use App\Services\Inventory\WarehouseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DataInterfaceController extends ApiController
{

    public function __construct()
    {
        parent::__construct();

        $this->middleware('auth');
        $this->middleware('permission:import.interface', ['only' => ['index', 'show']]);

//        $this->service = new ImportService();
    }

    public function index(Request $request)
    {
        $warehouses = (new WarehouseService())->all(['id','name','account_id','cog_account_id','s_account_id','p_account_id','sr_account_id','pr_account_id','sd_account_id','pd_account_id',
            'cost_center_id', 'ss_account_id','or_account_id','active'],null);
        $costCenters = (new CostCenterService())->all(['name', 'id' , 'code']);
        $others = (new OtherPartyService())->all(['name', 'id','account_id'],null,null,['account']);

        $invAccounts = (new AccountService())->all(['id', 'name','code'], 'I');
        $pAccounts = (new AccountService())->all(['id', 'name','code'], 'P');
        $prAccounts = (new AccountService())->all(['id', 'name','code'], 'PR');
        $pdAccounts = (new AccountService())->all(['id', 'name','code'], 'PD');
        $sAccounts = (new AccountService())->all(['id', 'name','code'], 'S');
        $srAccounts = (new AccountService())->all(['id', 'name','code'], 'SR');
        $sdAccounts = (new AccountService())->all(['id', 'name','code'], 'SD');
        $cogsAccounts = (new AccountService())->all(['id', 'name','code'], 'COG');
        $orAccounts = (new AccountService())->all(['id', 'name','code'], 'OR');
        $ssAccounts = (new AccountService())->all(['id', 'name','code'], 'SS');


        return $this->successResponse([
            'warehouses' => $warehouses,
            'costCenters' => $costCenters,
            'invAccounts' => $invAccounts,
            'pAccounts' => $pAccounts,
            'prAccounts' => $prAccounts,
            'pdAccounts' => $pdAccounts,
            'sAccounts' => $sAccounts,
            'srAccounts' => $srAccounts,
            'sdAccounts' => $sdAccounts,
            'cogsAccounts' => $cogsAccounts,
            'orAccounts' => $orAccounts,
            'ssAccounts' => $ssAccounts,
            'others' =>$others,
        ]);
    }

    public function warehouse(WarehouseInterfaceRequest $request)
    {
        $data = $request->validated();
        $id = $data['warehouse_id'];
        unset($data['warehouse_id']);
//        Log::warning('Warehouse Selected');

        DB::beginTransaction();
        try {
             (new WarehouseService())->update($id,$data);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e->getMessage(), 409);
        }
        DB::commit();
        return $this->successResponse(null, __('message.updated', ['model' => __('Warehouse')]));
    }

    public function others(OtherPartyInterfaceRequest $request)
    {
        $data = $request->validated();
        $id = $data['other_party_id'];
        unset($data['other_party_id']);

        DB::beginTransaction();
        try {
            (new OtherPartyService())->update($id,$data);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e->getMessage(), 409);
        }
        DB::commit();
        return $this->successResponse(null, __('message.updated', ['model' => __('Other Party')]));
    }





}
