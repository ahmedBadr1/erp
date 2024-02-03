<?php

namespace App\Http\Controllers\Api\Purchases;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Purchases\BillRequest;
use App\Http\Requests\Purchases\StoreBillRequest;
use App\Http\Resources\Purchases\BillsResource;
use App\Models\Purchases\Bill;
use App\Models\Purchases\Vendor;
use App\Models\System\Status;
use App\Services\Accounting\AccountService;
use App\Services\Accounting\CurrencyService;
use App\Services\CalculationService;
use App\Services\Hr\BranchService;
use App\Services\Inventory\WarehouseService;
use App\Services\Purchases\BillService;
use App\Services\System\TaxService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BillsController extends ApiController
{
    public function __construct()
    {
        parent::__construct();
        $this->class = "bills";
        $this->table = "bills";
        $this->middleware('auth');
        $this->middleware('permission:purchases.bills.view', ['only' => ['index', 'show']]);
        $this->middleware('permission:purchases.bills.create', ['only' => ['create', 'store']]);
        $this->middleware('permission:purchases.bills.edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:purchases.bills.delete', ['only' => ['destroy']]);

        $this->service = new BillService();
    }


    public function create()
    {
        if (auth('api')->user()->cannot('purchases.bills.create')) {
            return $this->deniedResponse(null, null, 403);
        }


        $warehouses = (new WarehouseService())->all(['name', 'id']);
        $branches = (new BranchService())->all(['name', 'id']);

        $treasuries = (new AccountService())->all(['name', 'id'], 'TR');

        $currencies = (new CurrencyService())->all(['code', 'id']);
        $buyers = (new UserService())->all(['username', 'id'], 'buyer');

        $statuses = Status::where('type', 'supplier')->get(['id', 'name']);

        $taxes = (new TaxService())->all(['id', 'name', 'rate']);

        // Notes Payable

//        $billNumber = Bill::latest()->first()->number; + 1

        return $this->successResponse([
            'warehouses' => $warehouses,
            'branches' => $branches,
            'treasuries' => $treasuries,
            'currencies' => $currencies,
            'buyers' => $buyers,
            'statuses' => $statuses,
            'taxes' => $taxes]);
    }


    public function store(StoreBillRequest $request)
    {
        DB::beginTransaction();
        try {
            $this->service->store($request->validated());
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e->getMessage(), 409);
        }
        DB::commit();
        return $this->successResponse(null, __('message.created', ['model' => __('Purchase Order')]));
    }

    public function show(Request $request, $code)
    {
        $bill = Bill::with('items.product','group.invTransactions', 'group.ledgers','group.transactions', 'group.bills', 'supplier', 'warehouse','treasury', 'currency','tax', 'responsible')->where('code', $code)->firstOrFail();
        return $this->successResponse(['bill' => new  BillsResource($bill)]);
    }
}
