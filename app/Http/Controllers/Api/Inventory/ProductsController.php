<?php

namespace App\Http\Controllers\Api\Inventory;

use App\Enums\ProductECodeEnum;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Inventory\SearchProductRequest;
use App\Http\Requests\Inventory\StoreProductRequest;
use App\Http\Requests\ListRequest;
use App\Http\Resources\Inventory\ProductCategoryResource;
use App\Http\Resources\Inventory\ProductCollection;
use App\Http\Resources\Inventory\ProductResource;
use App\Http\Resources\Inventory\ProductSearchResource;
use App\Models\Inventory\Product;
use App\Services\Accounting\AccountService;
use App\Services\Inventory\BrandService;
use App\Services\Inventory\ProductCategoryService;
use App\Services\Inventory\ProductService;
use App\Services\Inventory\UnitGroupService;
use App\Services\Inventory\UnitService;
use App\Services\Inventory\WarehouseService;
use App\Services\System\TagService;
use App\Services\System\TaxService;
use App\Services\UserService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class ProductsController extends ApiController
{
    public function __construct()
    {
        parent::__construct();
        $this->class = "products";
        $this->table = "products";
        $this->middleware('auth');
        $this->middleware('permission:inventory.products.view', ['only' => ['index', 'show']]);
        $this->middleware('permission:inventory.products.create', ['only' => ['create', 'store']]);
        $this->middleware('permission:inventory.products.edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:inventory.products.delete', ['only' => ['destroy']]);
        $this->service = new ProductService();
    }

    public function download(Request $request)
    {
        return $this->service->export();
    }

    public function index(ListRequest $request)
    {
        if (auth('api')->user()->cannot('inventory.products.index')) {
            return $this->deniedResponse(null, null, 403);
        }
//        return $this->successResponse(Carbon::parse($request->get('end_date')))  ;
        $query = $this->service->search($request->get('keywords'))
            ->with('unit')
            ->withCount('items')
            ->withSum('items as items_quantity', 'quantity')
            ->withSum('items as items_price', 'price')
//            ->withSum(['items as expired_items_quantity' => function ($query) {
//                $query->where('expire_at','>=', now());}],'quantity')
//            ->withSum(['items as expired_items_price' => function ($query) {
//                $query->where('expire_at','>=', now());}],'price')
            ->when($request->get('start_date'), function ($query) use ($request) {
                $query->where('created_at', '>=', Carbon::parse($request->get('start_date')));
            })
            ->when($request->get('end_date'), function ($query) use ($request) {
                $query->where('created_at', '<=', Carbon::parse($request->get('end_date')));
            })
            ->orderBy($request->get('orderBy') ?? $this->orderBy, $request->get('orderDesc') ?? $this->orderDesc);
        if ($request->get("export") == 'excel') {
            $products = $query->get();
            return $this->service->export($products);
        }
        $products = $query->paginate($request->get('per_page') ?? $this->limit, null, null, $request->get('current_page') ?? 1);

//        return new ProductCollection($products) ;
        return $this->resourceResponse(new ProductCollection($products));
    }

    public function list(SearchProductRequest $request)
    {
        if (auth('api')->user()->cannot('purchases.suppliers.index')) {
            return $this->deniedResponse(null, null, 403);
        }
        if ($request->has("keywords")) {
            $query = Product::query()->has('stocks')
                ->whereHas('stocks', function ($q) {
                    $q->where('balance', '!=', 0);
                })->when($request->get('warehouse_id'), fn($qu) => $qu->withSum(['stocks as warehouse_balance' => fn($q) => $q->where('warehouse_id', $request->get('warehouse_id'))], 'balance'))
                ->withSum('stocks as stocks_balance', 'balance');
            $products = $this->service->search($request->get("keywords"), $query)
                ->limit(10)->get();
        } else {
            $products = $this->service->all(['id', 'name', 'tax_id']);
        }
//        return   $this->successResponse($products);
        return $this->successResponse(['products' => ProductSearchResource::collection($products)]);
    }

    public function tree(Request $request)
    {
        if (auth('api')->user()->cannot('accounting.accounts.index')) {
            return $this->deniedResponse(null, null, 403);
        }
        $tree = (new ProductCategoryService())->tree(null, ['products'], ['children']);
        return $this->successResponse(ProductCategoryResource::collection($tree));
    }

    public function create()
    {
        $categories = (new ProductCategoryService())->all(['name', 'id']);
        $warehouses = (new WarehouseService())->all(['name', 'id']);
        $singleUnits = (new UnitService())->all(['name', 'id'], true);
        $unitGroups = (new UnitGroupService())->all(['name', 'id']);
        $taxes = (new TaxService())->all(['id', 'name', 'rate']);
        $brands = (new BrandService())->all(['id', 'name']);
//        $suppliers = (new SupplierService())->all(['id','name']);
        $suppliers = (new AccountService())->all(['id', 'name'], 'AP');

        $tags = (new TagService())->all(['name', 'id'], 'account');
        $users = (new UserService())->all(['id', 'username'], 'employee');

        return $this->successResponse([
            'tags' => $tags,
            'categories' => $categories,
            'warehouses' => $warehouses,
            'singleUnits' => $singleUnits,
            'unitGroups' => $unitGroups,
            'brands' => $brands,
            'taxes' => $taxes,
            'suppliers' => $suppliers,
            'users' => $users,
            'ECodes' => ProductECodeEnum::toArray(),
        ]);
    }

    public function store(StoreProductRequest $request)
    {
        $res = $this->service->store($request->validated());
        if ($res !== true) {
            return $this->errorResponse($res, 409);
        }

        return $this->successResponse(null, __('message.created', ['model' => __('Product')]));
    }

    public function show(Request $request, $id)
    {

        $product = Product::with(['items', 'category', 'unit', 'taxes', 'brand', 'warehouse', 'supplier', 'responsible'])
            ->withSum(['items as items_sum' => function ($query) {
                $query->where('expire_at', '>=', now());
            }], 'quantity')
            ->withSum(['items as expired_items_sum' => function ($query) {
                $query->where('expire_at', '<', now());
            }], 'quantity')
            ->where('id', $id)
            ->firstOrFail();

        return $this->successResponse(new ProductResource($product));
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

            return $this->successResponse(null, __("message.updated", ['model' => __('Product')]), 201);
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json(['message' => 'Failed to Update Product', 'error' => $e], 422);
        }
    }

    public function destroy(Request $request, Product $product)
    {
        Gate::authorize('delete', $product);
        if ($product->items()->exists()) {
            return $this->errorResponse(__('message.still-has', ['model' => __('Product'), 'relation' => __('Items')]), 422);
        }
        $product->delete();
        return $this->successResponse(null, __("message.deleted", ['model' => __('Product')]));
    }

}
