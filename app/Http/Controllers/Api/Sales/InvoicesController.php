<?php

namespace App\Http\Controllers\Api\Sales;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Sales\InvoiceRequest;
use App\Http\Requests\Sales\StoreInvoiceRequest;
use App\Http\Resources\Sales\InvoicesResource;
use App\Models\Sales\Invoice;
use App\Models\Sales\Vendor;
use App\Models\System\Status;
use App\Services\Accounting\AccountService;
use App\Services\Accounting\CurrencyService;
use App\Services\CalculationService;
use App\Services\Hr\BranchService;
use App\Services\Inventory\WarehouseService;
use App\Services\Sales\InvoiceService;
use App\Services\System\TaxService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoicesController extends ApiController
{
    public function __construct()
    {
        parent::__construct();
        $this->class = "invoices";
        $this->table = "invoices";
        $this->middleware('auth');
        $this->middleware('permission:sales.invoices.view', ['only' => ['index', 'show']]);
        $this->middleware('permission:sales.invoices.create', ['only' => ['create', 'store']]);
        $this->middleware('permission:sales.invoices.edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:sales.invoices.delete', ['only' => ['destroy']]);

        $this->service = new InvoiceService();
    }


    public function create()
    {
        if (auth('api')->user()->cannot('sales.invoices.create')) {
            return $this->deniedResponse(null, null, 403);
        }


        $warehouses = (new WarehouseService())->all(['name', 'id']);
        $branches = (new BranchService())->all(['name', 'id']);

        $treasuries = (new AccountService())->all(['name', 'id'], 'TR');

        $currencies = (new CurrencyService())->all(['code', 'id']);
        $buyers = (new UserService())->all(['username', 'id'], 'buyer');

        $statuses = Status::where('type', 'client')->get(['id', 'name']);

        $taxes = (new TaxService())->all(['id', 'name', 'rate']);

        // Notes Payable

        $invoiceCode = Invoice::latest()->value('code')  ;

        if (preg_match('/^PO-[1-9]-(\d+)/', $invoiceCode, $matches)) {
            $count = $matches[1] + 1;
        }
        return $this->successResponse([
            'count' => $count ?? null,
            'warehouses' => $warehouses,
            'branches' => $branches,
            'treasuries' => $treasuries,
            'currencies' => $currencies,
            'buyers' => $buyers,
            'statuses' => $statuses,
            'taxes' => $taxes]);
    }


    public function store(StoreInvoiceRequest $request)
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
        $invoice = Invoice::with('items.product','group.invTransactions', 'group.ledgers','group.transactions', 'group.po','group.so', 'secondParty', 'warehouse','treasury', 'currency','tax', 'responsible')->where('code', $code)->firstOrFail();
        return $this->successResponse(['invoice' => new  InvoicesResource($invoice)]);
    }
}
