<?php

namespace App\Http\Controllers\Api\Inventory;

use App\Enums\Inventory\UnitTypesEnum;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Inventory\StoreProductRequest;
use App\Http\Requests\Inventory\StoreUnitRequest;
use App\Http\Requests\Inventory\StoreWarehouseRequest;
use App\Http\Requests\ListRequest;
use App\Http\Resources\Inventory\UnitCategoryResource;
use App\Http\Resources\Inventory\WarehouseCollection;
use App\Http\Resources\Inventory\WarehouseResource;
use App\Http\Resources\NameResource;
use App\Models\Accounting\Tax;
use App\Models\Inventory\Brand;
use App\Models\Inventory\ProductCategory;
use App\Models\Inventory\Product;
use App\Models\Inventory\Unit;
use App\Models\Inventory\UnitCategory;
use App\Models\Inventory\Warehouse;
use App\Models\Purchases\Supplier;
use App\Models\User;
use App\Services\Inventory\ProductCategoryService;
use App\Services\Inventory\UnitGroupService;
use App\Services\Inventory\UnitService;
use App\Services\Inventory\WarehouseService;
use App\Services\Sales\PriceListService;
use App\Services\System\TagService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class UnitsController extends ApiController
{
    public function __construct()
    {
        parent::__construct();
        $this->class = "units";
        $this->table = "units";
        $this->middleware('auth');
        $this->middleware('permission:inventory.units.view', ['only' => ['index', 'show']]);
        $this->middleware('permission:inventory.units.create', ['only' => ['create', 'store']]);
        $this->middleware('permission:inventory.units.edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:inventory.units.delete', ['only' => ['destroy']]);

        $this->service = new UnitService();
    }

    public function index(ListRequest $request)
    {
        if (auth('api')->user()->cannot('inventory.units.index')) {
            return $this->deniedResponse(null, null, 403);
        }
        $warehouses = $this->service->search($request->get('keywords'))
            ->with('category')
            ->latest()
            ->limit($request->get('per_page') ?? $this->limit)
            ->get();
        return $this->resourceResponse( UnitCategoryResource::collection($warehouses));
    }

    public function categories(Request $request)
    {
        if (auth('api')->user()->cannot('inventory.units.categoires')) {
            return $this->deniedResponse();
        }
        $unitCategoires = $this->service->categories(fields: ['id','name'],relations: ['units']);
        return $this->successResponse(UnitCategoryResource::collection($unitCategoires));
    }



    public function create()
    {
        $categories = (new ProductCategoryService())->all(['name', 'id']);
        $priceLists = (new PriceListService())->all(['name', 'id']);
        $tags = (new TagService())->all(['name', 'id'], 'warehouse');
        return $this->successResponse(['categories' => $categories, 'priceLists' => $priceLists, 'tags' => $tags]);
    }

    public function store(StoreUnitRequest $request)
    {
//        dd($request->all());
        DB::beginTransaction();
        try {
            $this->service->store($request->validated());
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e->getMessage(), 409);
        }
        DB::commit();
        return $this->successResponse(null, __('message.created', ['model' => __('Units')]));
    }

    public function show(Request $request, $id)
    {

        $warehouse = Warehouse::with(['items'])
            ->withSum(['items as items_sum' => function ($query) {
                $query->where('expire_at', '>=', now());
            }], 'quantity')
            ->withSum(['items as expired_items_sum' => function ($query) {
                $query->where('expire_at', '<', now());
            }], 'quantity')
            ->where('id', $id)
            ->firstOrFail();

        return $this->successResponse(new WarehouseResource($warehouse));
    }

    public function update(StoreProductRequest $request, $id)
    {

        $data = $request->all();
//        return $data ;
        $product = Product::findOrFail($id);
        try {
            DB::beginTransaction();

            $product->update($data);

            DB::commit();

            return $this->successResponse(null ,  __("message.updated",['model' => __('Warehouse')]) ,201);
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json(['message' => 'Failed to Update Product', 'error' => $e], 422);
        }
    }

    public function destroy(Request $request, Warehouse $warehouse)
    {
        Gate::authorize('delete', $warehouse);

        if ($warehouse->items()->exists()) {
            return $this->errorResponse(__('message.still-has', ['model' => __('Warehouse'), 'relation' => __('Items')]),422);
        }
        $warehouse->delete();
        return $this->successResponse(null, __("message.deleted",['model' => __('Warehouse')]));
    }

}
