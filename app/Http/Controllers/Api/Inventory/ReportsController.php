<?php

namespace App\Http\Controllers\Api\Inventory;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Accounting\Reports\CashReportRequest;
use App\Http\Requests\Accounting\Reports\ShowPostingRequest;
use App\Http\Requests\Inventory\Reports\CostReportRequest;
use App\Http\Requests\Inventory\Reports\InventoryReportRequest;
use App\Http\Requests\Inventory\Reports\StockCardsReportRequest;
use App\Http\Requests\Inventory\Reports\WarehousesReportRequest;
use App\Http\Requests\Inventory\Reports\WorkOrdersReportRequest;
use App\Http\Resources\Accounting\LedgerResource;
use App\Http\Resources\Accounting\TransactionResource;
use App\Http\Resources\Inventory\InvTransactionResource;
use App\Http\Resources\Inventory\ItemResource;
use App\Http\Resources\Inventory\ProductResource;
use App\Http\Resources\Inventory\ProductStockResource;
use App\Http\Resources\Inventory\StockResource;
use App\Models\Inventory\InvTransaction;
use App\Models\Inventory\Warehouse;
use App\Services\Accounting\LedgerService;
use App\Services\Accounting\TransactionService;
use App\Services\ClientService;
use App\Services\Hr\BranchService;
use App\Services\Inventory\BrandService;
use App\Services\Inventory\InvTransactionService;
use App\Services\Inventory\ItemHistoryService;
use App\Services\Inventory\ProductCategoryService;
use App\Services\Inventory\ProductService;
use App\Services\Inventory\StockService;
use App\Services\Inventory\WarehouseService;
use App\Services\Purchases\SupplierService;
use App\Services\System\TagService;
use App\Services\UserService;

class ReportsController extends ApiController
{
    public function __construct()
    {
        parent::__construct();
        $this->class = "reports";
        $this->table = "reports";
        $this->middleware('auth');
        $this->middleware('permission:inventory.reports.index', ['only' => ['index']]);
        $this->middleware('permission:inventory.reports.stocks', ['only' => ['stocks']]);
        $this->middleware('permission:inventory.reports.warehouses', ['only' => ['warehouses']]);
        $this->middleware('permission:inventory.reports.stock-items', ['only' => ['stockItems']]);


    }

    public function index(InventoryReportRequest $request)
    {
        $data = [];

        if ($request->get('orderTypes')) { // && auth('api')->user()->can('accounting.accounts.index')
            $data['orderTypes'] = (new InvTransactionService())->types();
        }

        if ($request->get('warehouses')) { // && auth('api')->user()->can('accounting.nodes.index')
            $data['warehouses'] = (new WarehouseService())->all(['id', 'name']);
        }

        if ($request->get('products')) { // && auth('api')->user()->can('accounting.nodes.index')
            $data['products'] = (new ProductService())->all(['id', 'name']);
        }

//        if ($request->get('warehouseTypes')) { // && auth('api')->user()->can('accounting.nodes.index')
//            $data['warehouseTypes'] = (new WarehouseService())->types();
//        }

        if ($request->get('users')) { // && auth('api')->user()->can('accounting.nodes.index')
            $data['users'] = (new UserService())->all(['id', 'username']);
        }

        if ($request->get('buyers')) { // && auth('api')->user()->can('accounting.nodes.index')
            $data['buyers'] = (new UserService())->all(['id', 'username'], 'buyers');
        }

        if ($request->get('sellers')) { // && auth('api')->user()->can('accounting.nodes.index')
            $data['sellers'] = (new UserService())->all(['id', 'username'], 'seller');
        }
        if ($request->get('clients')) { // && auth('api')->user()->can('accounting.nodes.index')
            $data['clients'] = (new ClientService())->all(['id', 'name']);
        }
        if ($request->get('suppliers')) { // && auth('api')->user()->can('accounting.nodes.index')
            $data['suppliers'] = (new SupplierService())->all(['id', 'name']);
        }
        if ($request->get('tags')) { // && auth('api')->user()->can('accounting.nodes.index')
            $data['tags'] = (new TagService())->all(['id', 'name'], 'invTransaction');
        }

        if ($request->get('brands')) { // && auth('api')->user()->can('accounting.accounts.index')
            $data['brands'] = (new BrandService())->all(['id', 'name']);
        }

        if ($request->get('branches')) { // && auth('api')->user()->can('accounting.accounts.index')
            $data['branches'] = (new BranchService())->all(['id', 'name']);
        }

        if ($request->get('productCategories')) { // && auth('api')->user()->can('accounting.accounts.index')
            $data['productCategories'] = (new ProductCategoryService())->all(['id', 'name']);
        }

        if ($request->get('inOther')) { // && auth('api')->user()->can('accounting.accounts.index')
            $data['inOther'] = InvTransaction::$inOther;
        }

        if ($request->get('outOther')) { // && auth('api')->user()->can('accounting.accounts.index')
            $data['outOther'] = InvTransaction::$outOther;
        }

        return $this->successResponse($data);
    }
    public function warehouses(WarehousesReportRequest $request)//
    {
        $result = (new ProductService())->stocks($request->validated()); // ->collection->groupBy('product.name')

        return $this->successResponse(['rows' => ProductStockResource::collection($result['rows']), 'dataset' => $result['dataset']]);
    }

    public function cost(CostReportRequest $request)//
    {
        [$rows ,$dataset ] = (new ProductService())->cost($request->validated()); // ->collection->groupBy('product.name')
        return $this->successResponse(['rows' => ProductStockResource::collection($rows), 'dataset' => $dataset]);
    }

    public function cards(StockCardsReportRequest $request)//
    {
        [$rows ,$dataset ] = (new ItemHistoryService())->cards($request->validated()); // ->collection->groupBy('product.name')
        return $this->successResponse(['rows' => ItemResource::collection($rows), 'dataset' => $dataset]);
    }

    public function orders(WorkOrdersReportRequest $request)//
    {
        $result = (new InvTransactionService())->orders($request->validated());
        return $this->successResponse(['rows' => InvTransactionResource::collection($result['rows']), 'dataset' => $result['dataset']]);
    }



}
