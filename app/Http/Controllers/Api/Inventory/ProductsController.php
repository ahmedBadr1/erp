<?php

namespace App\Http\Controllers\Api\Inventory;

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\StoreProductRequest;
use App\Http\Requests\ListRequest;
use App\Http\Resources\Inventory\ProductCollection;
use App\Http\Resources\Inventory\ProductResource;
use App\Models\Accounting\Account;
use App\Models\Accounting\Entry;
use App\Models\Accounting\Transaction;
use App\Models\Employee\Employee;
use App\Models\Inventory\Brand;
use App\Models\Inventory\InvCategory;
use App\Models\Inventory\Product;
use App\Models\Inventory\Unit;
use App\Models\Inventory\Warehouse;
use App\Models\Purchases\Supplier;
use App\Models\System\Tax;
use App\Models\User;
use App\Services\Inventory\ProductService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

    public function index(ListRequest $request){
        if (auth('api')->user()->cannot('inventory.products.index')) {
            return $this->deniedResponse(null, null, 403);
        }
//        return $this->successResponse(Carbon::parse($request->get('end_date')))  ;
        $query = $this->service->search($request->get('keywords'))
            ->with('unit')
            ->withCount('items')
            ->when($request->get('start_date'), function ($query) use ($request) {
                $query->where('created_at', '>=', Carbon::parse($request->get('start_date')));
            })
            ->when($request->get('end_date'), function ($query) use ($request) {
                $query->where('created_at', '<=', Carbon::parse($request->get('end_date')));
            })
            ->orderBy($request->get('orderBy') ?? $this->orderBy,$request->get('orderDesc') ?? $this->orderDesc);
            if ($request->get("export") == 'excel'){
                $products= $query->get() ;
                return $this->service->export($products);
            }
            $products= $query->paginate($request->get('per_page') ?? $this->limit,null,null,$request->get('current_page')?? 1);

//        return new ProductCollection($products) ;
       return $this->resourceResponse(new ProductCollection($products));
    }

    public function list(Request $request)
    {
        if (auth('api')->user()->cannot('inventory.products.index')) {
            return $this->deniedResponse();
        }
        $products = Product::active()->pluck('name', 'id')->toArray();
        return $this->successResponse(['products' => $products]);
    }

    public function create(){
        $categories = InvCategory::pluck('name','id')->toArray();
        $warehouses = Warehouse::pluck('name','id')->toArray();
        $units = Unit::pluck('name','id')->toArray();
        $brands = Brand::pluck('name','id')->toArray();
        $taxes = Tax::select('id','name','rate')->get();
        $suppliers = Supplier::pluck('name','id')->toArray();
        $users = User::whereHas('roles',fn($q)=>$q->where('name','employee'))->pluck('name','id')->toArray();

        return $this->successResponse(['categories'=>$categories,'warehouses'=>$warehouses,'units'=>$units,'brands'=>$brands,'taxes'=>$taxes,'suppliers'=>$suppliers,'users'=>$users]);
    }
     public function store(StoreProductRequest $request)
        {

            $data = $request->all();

            try {
                DB::beginTransaction();

                $product = Product::create($data);


                DB::commit();

                return $this->successResponse(null ,  __("message.created",['model' => __('Product')]) ,201);
            } catch (\Exception $e) {
                DB::rollback();

                return response()->json(['message' => 'Failed to create Product','error'=>$e], 500);
            }
        }

    public function show(Request $request, $id)
    {

        $product = Product::with(['items', 'category','unit','taxes','brand','warehouse','supplier','responsible'])
            ->withSum(['items as items_sum' => function ($query) {
                $query->where('expire_at', '>=',now());
            }], 'quantity')
            ->withSum(['items as expired_items_sum' => function ($query) {
                $query->where('expire_at', '<',now());
            }], 'quantity')
            ->where('id', $id)
            ->firstOrFail();

        return $this->successResponse(new ProductResource($product));
    }

    public function update(StoreProductRequest $request,$id)
    {

        $data = $request->all();
//        return $data ;
        $product = Product::findOrFail($id);
        try {
            DB::beginTransaction();

            $product->update($data);

            DB::commit();

            return $this->successResponse(null ,  __("message.updated",['model' => __('Product')]) ,201);
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json(['message' => 'Failed to Update Product','error'=>$e], 422);
        }
    }

    public function destroy(Request $request,Product $product)
    {
        Gate::authorize('delete',$product);
        if ($product->items()->exists()){
            return $this->errorResponse(__('message.still-has', ['model' => __('Product'), 'relation' => __('Items')]),422);
        }
        $product->delete();
        return $this->successResponse(null, __("message.deleted",['model' => __('Product')]));
    }

}