<?php

namespace App\Http\Controllers\Api\Purchases;

use App\Http\Controllers\Controller;
use App\Http\Requests\ListRequest;
use App\Http\Requests\Purchases\BillRequest;
use App\Http\Resources\Inventory\ProductResource;
use App\Http\Resources\Purchases\BillsResource;
use App\Http\Resources\Purchases\VendorsResource;
use App\Models\Inventory\Item;
use App\Models\Inventory\Product;
use App\Models\Purchases\Bill;
use App\Models\Purchases\Payment;
use App\Models\Purchases\Vendor;
use App\Models\System\Status;
use App\Models\System\Tax;
use App\Services\CalculationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use ZipStream\Exception;

class BillsController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:purchases');
    }

    public function list(ListRequest $request)
    {
        $input = $request->all();
        //
        return  BillsResource::collection( Bill::search($input['keywords'])
//            ->orderBy($input['orderBy'], $input['orderDesc'] ? 'desc' : 'asc')
            ->paginate($input['limit']));
    }

    public function create()
    {
        $statuses = Status::where('type', 'paid')->get(['id','name']);
        $taxes = Tax::where('active', true)->get(['id','name','rate']);
//        $billNumber = Bill::latest()->first()->number;
//   59 + 1 = 61
        // 60
    return success(['statuses' => $statuses ,'taxes' => $taxes]);
    }

    public function searchVendor(ListRequest $request): \Illuminate\Http\JsonResponse
    {
        $vendors = VendorsResource::collection(Vendor::search($request->keywords)->take($request->limit)->get());
        return success(['vendors' => $vendors]);
    }

    public function searchProduct(ListRequest $request): \Illuminate\Http\JsonResponse
    {
        $products= ProductResource::collection(product::search($request->keywords)->take($request->limit)->get());
        return success(['products' => $products]);
    }

    public function store(BillRequest $request)
    {
        // return $request;
        $validated = $request->all();
       $data = [
           'billed_at' => $validated['billed_at'],
           'due_at' => $validated['due_at'],
           'status_id' => $validated['status_id'],
           'code' => $validated['code'] ,
           'number' => $validated['number'] ?? null,
           'notes' => $validated['notes'] ?? null,
           'vendor_id' => $validated['vendor_id'],
           'tax_id' => $validated['tax_id'],
           'paid' => $validated['paid'] ,
        //    'sub_total' => $this->subTotal,
           'discount' => $validated['discount'],    
           'tax_total' => $validated['taxTotal'],
        //    'total' => $this->total,
           'user_id' => \auth()->id(),
       ];
        try {
            DB::beginTransaction();

            // if ($validated['bill_id']) {
            //     $bill = Bill::findOrFail($validated['bill_id']);
            //     $bill->update($validated);
            //     foreach ($bill->items as $item) {
            //         $item->delete();
            //     }
            // } else {
            //     $bill = Bill::create([$validated,
            //         'user_id' => auth()->id(),//$data['user_id']
            //     ]);
            // }
            // return $validated['status_id'];
            $bill = Bill::create($data);
            // return $bill;
            $subTotal = 0 ;
            $total = 0 ;
            foreach ($validated['billItems'] as $k => $itm) {
                Item::create([
                    'name' => $itm['name'],
                    'description' => $itm['description'] ?? null,
                    'quantity' => floatval($itm['quantity']),
                    'price' => floatval($itm['price']),
                    'bill_id' => $bill->id,
                    'user_id' => auth()->id(),
                    'product_id' =>  $itm['product_id']
                ]);
                $subTotal += floatval($itm['quantity']) * floatval($itm['price']) ;
//                $product =  Product::find($itm['product_id']);
//                CalculationService::calProduct($product);
            }
            if (floatval($subTotal) !== floatval($validated['sub_total'])){
                // return $subTotal .'--' . $validated['sub_total'];
                return response()->json(['message' => 'Total is not balanced'], 500);
            }
            $total = $subTotal + $validated['tax_total'] - $validated['discount'];
            
            $bill->subTotal = $subTotal;
            $bill->total = $total;
            $bill->save();
            
            
            if ($bill->status->name != 'unpaid'){
                $amount= 0;
                if ($bill->status->name == 'paid'){
                    $amount = $total ;
                }elseif($bill->status->name == 'partial'){
                    $amount = $validated['paid'];
                }
                Payment::create([
                    'paid_at' => $bill->billed_at,
                    'amount' => $amount,
                    'bill_id' => $bill->id,
                    'vendor_id' => $bill->vendor_id,
                ]);
            }
            

            DB::commit();
            return response()->json(['message' => 'Bill created successfully'], 201);
        } catch (\Exception $e) {
            DB::rollback();
            return $e;
            return response()->json(['message' => 'Failed to create or update Bill','error'=>$e], 500);
        }
    }
}
