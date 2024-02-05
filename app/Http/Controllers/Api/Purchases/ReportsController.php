<?php

namespace App\Http\Controllers\Api\Purchases;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Accounting\Reports\WorkOrdersReportRequest;
use App\Http\Requests\Accounting\Reports\CashReportRequest;
use App\Http\Requests\Accounting\Reports\InventoryReportRequest;
use App\Http\Requests\Accounting\Reports\ShowPostingRequest;
use App\Http\Resources\Accounting\LedgerResource;
use App\Http\Resources\Accounting\TransactionResource;
use App\Models\Accounting\AccountType;
use App\Services\Accounting\AccountService;
use App\Services\Accounting\CostCenterService;
use App\Services\Accounting\CurrencyService;
use App\Services\Accounting\LedgerService;
use App\Services\Accounting\NodeService;
use App\Services\Accounting\TaxService;
use App\Services\Accounting\TransactionService;
use App\Services\ClientService;
use App\Services\UserService;

class ReportsController extends ApiController
{
    public function __construct()
    {
        parent::__construct();
        $this->class = "reports";
        $this->table = "reports";
        $this->middleware('auth');
        $this->middleware('permission:purchases.reports.index', ['only' => ['index']]);
        $this->middleware('permission:purchases.reports.orders', ['only' => ['orders']]);
        $this->middleware('permission:purchases.reports.purchases', ['only' => ['purchases']]);
    }

    public function index(InventoryReportRequest $request)
    {
        $data = [];

        if ($request->get('tree')) { // && auth('api')->user()->can('accounting.accounts.index')
            $data['tree'] = (new NodeService())->tree(null);
        }

        if ($request->get('treeNodes')) { // && auth('api')->user()->can('accounting.nodes.index')
            $data['treeNodes'] = (new NodeService())->all(['id','code', 'name','parent_id'],[],['children']);
        }

        if ($request->get('nodeRoots')) { // && auth('api')->user()->can('accounting.accounts.index')
            $data['nodeRoots'] = (new NodeService())->root();
        }

        if ($request->get('accountTypes')) { // && auth('api')->user()->can('accounting.accounts.index')
            $data['accountTypes'] = (new AccountService)->types(['id', 'name']);
        }

        if ($request->get('transactionTypes')) { // && auth('api')->user()->can('accounting.accounts.index')
            $data['transactionTypes'] = (new TransactionService())->types();
        }

        if ($request->get('accounts')) { // && auth('api')->user()->can('accounting.accounts.index')
            $data['accounts'] = (new AccountService)->all(['code', 'name']);
        }

        if ($request->get('treasuries')) { // && auth('api')->user()->can('accounting.accounts.index')
            $type_id = AccountType::where('code', 'TR')->value('id');
            $data['treasuries'] = (new AccountService)->all(['type_code', 'name'], $type_id);
        }

        if ($request->get('nodes')) { // && auth('api')->user()->can('accounting.nodes.index')
            $data['nodes'] = (new NodeService())->all(['code', 'name']);
        }
        if ($request->get('costCenters')) { // && auth('api')->user()->can('accounting.nodes.index')
            $data['costCenters'] = (new CostCenterService())->all(['code', 'name']);
        }
        if ($request->get('currencies')) { // && auth('api')->user()->can('accounting.nodes.index')
            $data['currencies'] = (new CurrencyService())->all(['code', 'id']);
        }
        if ($request->get('taxes')) { // && auth('api')->user()->can('accounting.nodes.index')
            $data['taxes'] = (new TaxService())->all(['code', 'id']);
        }
        if ($request->get('clients')) { // && auth('api')->user()->can('accounting.nodes.index')
            $data['clients'] = (new ClientService())->all(['id', 'name']);
        }

        if ($request->get('sellers')) { // && auth('api')->user()->can('accounting.nodes.index')
            $data['sellers'] = (new UserService())->all(['id', 'username'], 'seller');
        }

        return $this->successResponse($data);
    }

    public function orders(WorkOrdersReportRequest $request)//
    {
        return $this->successResponse(LedgerResource::collection((new LedgerService())->accounts($request->validated())), 'yaaaaaah');
    }

    public function purchases(CashReportRequest $request)//
    {
        return $this->successResponse(TransactionResource::collection((new TransactionService())->cash($request->validated())), 'yaaaaaah');
    }

}
