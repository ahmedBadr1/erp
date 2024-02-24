<?php

namespace App\Http\Controllers\Api\System;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\System\DownloadTemplateRequest;
use App\Http\Requests\System\ImportRequest;
use App\Imports\Accounting\AccountImporter;
use App\Imports\Inventory\BrandImporter;
use App\Imports\Inventory\ProductsImport;
use App\Imports\Inventory\WarehouseImporter;
use App\Models\Accounting\Account;
use App\Models\Accounting\CostCenter;
use App\Models\Inventory\Brand;
use App\Models\Inventory\Product;
use App\Models\Inventory\Warehouse;
use App\Models\Purchases\Supplier;
use App\Models\Sales\Client;
use App\Models\Sales\Service;
use App\Models\System\Group;
use App\Models\User;
use App\Services\Accounting\AccountService;
use App\Services\Inventory\BrandService;
use App\Services\Inventory\ProductService;
use App\Services\Inventory\WarehouseService;
use App\Services\System\ImportService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ImportController extends ApiController
{

    public function __construct()
    {
        parent::__construct();

        $this->middleware('auth');
        $this->middleware('permission:import.data', ['only' => ['index', 'show']]);

        $this->service = new ImportService();
    }

    public function index(ImportRequest $request)
    {
        $method = match ($request->get('name')) {
            'products' => 'importProduct',
            'warehouses' =>'importWarehouse',
            'brands' => 'importBrand',
            'accounts' => 'importAccount',
            'costCenters' => 'importCostCenter',
            default => throw  new \RuntimeException('Model Name Not Valid'),
        };
        DB::transaction(function () use ($method, $request) {
            $this->service->$method(file: $request->file('file') ,type: 'csv' ,node: $request->get('node_code'));
        });

        return $this->successResponse(null, __('message.imported',['model'=> ucfirst($request->get('name'))]));
    }

    public function template(DownloadTemplateRequest $request)
    {
        $model = match ($request->get('name')) {
            'products' => Product::class,
            'warehouses' => Warehouse::class,
            'brands' => Brand::class,
//            'services' => Service::class,
            'accounts' => Account::class,
            'costCenters' => CostCenter::class ,
            'suppliers' => Supplier::class ,
            'clients' => Client::class ,
            'groups' => Group::class ,
            'users' => User::class,
            default => throw  new \RuntimeException('Model Name Not Valid'),
        };
        return $this->service->generateModelTemplate($model, $request->get('name'));
    }





}
