<?php

namespace App\Http\Controllers\Api\Accounting;

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Controller;
use App\Http\Requests\Accounting\Reports\AccountingReportRequest;
use App\Http\Requests\Accounting\Reports\AccountLedgerRequest;
use App\Http\Requests\Accounting\Reports\WorkOrdersReportRequest;
use App\Http\Requests\Accounting\Reports\CashReportRequest;
use App\Http\Requests\Accounting\Reports\InventoryReportRequest;
use App\Http\Requests\Accounting\Reports\ShowPostingRequest;
use App\Http\Resources\Accounting\AccountChartResource;
use App\Http\Resources\Accounting\AccountResource;
use App\Http\Resources\Accounting\EntryResource;
use App\Http\Resources\Accounting\LedgerResource;
use App\Http\Resources\Accounting\NodeResource;
use App\Http\Resources\Accounting\TransactionResource;
use App\Models\Accounting\Account;
use App\Models\Accounting\AccountType;
use App\Models\Accounting\CostCenter;
use App\Models\Accounting\Ledger;
use App\Services\Accounting\AccountService;
use App\Services\Accounting\CostCenterService;
use App\Services\Accounting\CurrencyService;
use App\Services\Accounting\EntryService;
use App\Services\Accounting\LedgerService;
use App\Services\Accounting\NodeService;
use App\Services\Accounting\TaxService;
use App\Services\Accounting\TransactionService;
use App\Services\ClientService;
use App\Services\UserService;
use Illuminate\Http\Request;

class ReportsController extends ApiController
{
    public function __construct()
    {
        parent::__construct();
        $this->class = "reports";
        $this->table = "reports";
        $this->middleware('auth');
        $this->middleware('permission:accounting.reports.index', ['only' => ['index']]);
        $this->middleware('permission:accounting.reports.account', ['only' => ['accountLedger']]);
        $this->middleware('permission:accounting.reports.ledger', ['only' => ['generalLedger']]);
        $this->middleware('permission:accounting.reports.cash', ['only' => ['cashReport']]);
        $this->middleware('permission:accounting.reports.nr', ['only' => ['nrReport']]);
        $this->middleware('permission:accounting.reports.nd', ['only' => ['ndReport']]);
        $this->middleware('permission:accounting.reports.flow', ['only' => ['cashFlow']]);

    }

    public function index(AccountingReportRequest $request)
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

    public function accountLedger(AccountLedgerRequest $request)//
    {
//        $account = Account::find(7);
//        $entries = $account->entriesWithCumulativeBalance();
//        return $this->successResponse($entries) ;
        [$rows , $dataset , $accounts] = (new EntryService())->accounts($request->validated()) ;
//        return $this->successResponse($accounts) ;
        $rows =  EntryResource::collection($rows) ;
//        $credit_sum = $rows->collection->when(true, function ($collection) {
//                return $collection->where('credit', true);
//            })->sum('amount');
//        $debit_sum = $rows->collection->when(true, function ($collection) {
//        return $collection->where('credit', false);
//    })->sum('amount');
        return $this->successResponse(['rows'=> $rows,'dataset'=> $dataset,'accounts'=> AccountResource::collection($accounts)]);
    }

    public function cash(CashReportRequest $request)//
    {
        return $this->successResponse(TransactionResource::collection((new TransactionService())->cash($request->validated())),);
    }

    public function posting(ShowPostingRequest $request)
    {
        $result = (new LedgerService())->posting($request->validated());
        return $this->successResponse(['rows'=> LedgerResource::collection($result['rows']),'dataset'=>$result['dataset']]);
    }
}
