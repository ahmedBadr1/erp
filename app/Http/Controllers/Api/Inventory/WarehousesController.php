<?php

namespace App\Http\Controllers\Api\Inventory;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Inventory\StoreProductRequest;
use App\Http\Requests\ListRequest;
use App\Http\Resources\Inventory\WarehouseCollection;
use App\Http\Resources\Inventory\WarehouseResource;
use App\Models\Accounting\Tax;
use App\Models\Inventory\Brand;
use App\Models\Inventory\InvCategory;
use App\Models\Inventory\Product;
use App\Models\Inventory\Unit;
use App\Models\Inventory\Warehouse;
use App\Models\Purchases\Supplier;
use App\Models\User;
use App\Services\Inventory\WarehouseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class WarehousesController extends ApiController
{
    public function __construct()
    {
        parent::__construct();
        $this->class = "warehouse";
        $this->table = "warehouses";
        $this->middleware('auth');
        $this->middleware('permission:inventory.warehouses.view', ['only' => ['index', 'show']]);
        $this->middleware('permission:inventory.warehouses.create', ['only' => ['create', 'store']]);
        $this->middleware('permission:inventory.warehouses.edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:inventory.warehouses.delete', ['only' => ['destroy']]);

        $this->service = new WarehouseService();
    }


    public function index(ListRequest $request)
    {
        if (auth('api')->user()->cannot('inventory.warehouses.index')) {
            return $this->deniedResponse(null, null, 403);
        }
        $warehouses = $this->service->search($request->get('keywords'))
            ->withCount('items', 'manager')
            ->when($request->get('start_date'), function ($query) use ($request) {
                $query->where('created_at', '>=', $request->get('start_date'));
            })
            ->when($request->get('end_date'), function ($query) use ($request) {
                $query->where('created_at', '<=', $request->get('start_date'));
            })
            ->latest()
            ->paginate($request->get('per_page') ?? $this->limit);
        return $this->resourceResponse(new WarehouseCollection($warehouses));
    }

    public function list(Request $request)
    {
        if (auth('api')->user()->cannot('inventory.products.index')) {
            return $this->deniedResponse();
        }
        $products = Product::active()->pluck('name', 'id')->toArray();
        return $this->successResponse(['products' => $products]);
    }

    public function create()
    {
        $categories = InvCategory::pluck('name', 'id')->toArray();
        $warehouses = Warehouse::pluck('name', 'id')->toArray();
        $units = Unit::pluck('name', 'id')->toArray();
        $brands = Brand::pluck('name', 'id')->toArray();
        $taxes = Tax::select('id', 'name', 'rate')->get();
        $suppliers = Supplier::pluck('name', 'id')->toArray();
        $users = User::whereHas('roles', fn($q) => $q->where('name', 'employee'))->pluck('name', 'id')->toArray();

        return $this->successResponse(['categories' => $categories, 'warehouses' => $warehouses, 'units' => $units, 'brands' => $brands, 'taxes' => $taxes, 'suppliers' => $suppliers, 'users' => $users]);
    }

    public function store(StoreProductRequest $request)
    {

        $data = $request->all();

        try {
            DB::beginTransaction();

            $product = Product::create($data);


            DB::commit();

            return $this->successResponse(null ,  __("message.created",['model' => __('Warehouse')]) ,201);
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json(['message' => 'Failed to create Product', 'error' => $e], 500);
        }
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
