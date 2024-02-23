<?php

namespace App\Http\Controllers\Api\System;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\System\DownloadTemplateRequest;
use App\Http\Requests\System\ImportRequest;
use App\Imports\Inventory\ProductsImport;
use App\Models\Accounting\Account;
use App\Models\Accounting\CostCenter;
use App\Models\Inventory\Product;
use App\Models\Inventory\Warehouse;
use App\Models\Purchases\Supplier;
use App\Models\Sales\Client;
use App\Models\Sales\Service;
use App\Models\System\Group;
use App\Models\User;
use App\Services\System\ImportService;

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
        $importer = match ($request->get('name')) {
            'products' => ProductsImport::class,
//            'warehouses' => Warehouse::class,
            default => throw  new \RuntimeException('Model Name Not Valid'),
        };
//        sleep(2);
        $this->service->import($request->file('file') ,$importer, $request->get('name'));

        return $this->successResponse(null,__('message.imported'));
    }

    public function template(DownloadTemplateRequest $request)
    {
        $model = match ($request->get('name')) {
            'products' => Product::class,
            'warehouses' => Warehouse::class,
            'services' => Service::class,
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
