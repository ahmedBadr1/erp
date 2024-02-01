<?php

namespace App\Http\Controllers\Api\Purchases;

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Controller;
use App\Http\Requests\ListRequest;
use App\Http\Requests\Purchases\StoreVendorRequest;
use App\Http\Requests\System\StoreContact;
use App\Http\Resources\NameResource;
use App\Http\Resources\Purchases\ShowVendorResource;
use App\Http\Resources\Purchases\SuppliersResource;
use App\Models\Accounting\Account;
use App\Models\Accounting\Currency;
use App\Models\Hr\Employee;
use App\Models\Purchases\Payment;
use App\Models\Purchases\Supplier;
use App\Models\System\Contact;
use App\Models\System\Country;
use App\Models\System\Location;
use App\Models\System\State;
use App\Services\Purchases\SupplierService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SuppliersController extends ApiController
{
    public function __construct()
    {
        parent::__construct();
        $this->class = "suppliers";
        $this->table = "suppliers";
        $this->middleware('auth');
        $this->middleware('permission:purchases.suppliers.view', ['only' => ['index', 'show']]);
        $this->middleware('permission:purchases.suppliers.create', ['only' => ['create', 'store']]);
        $this->middleware('permission:purchases.suppliers.edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:purchases.suppliers.delete', ['only' => ['destroy']]);

        $this->service = new SupplierService();
    }

    public function list(ListRequest $request)
    {
        if (auth('api')->user()->cannot('purchases.suppliers.index')) {
            return $this->deniedResponse(null, null, 403);
        }
        if ($request->has("keywords")) {
            $suppliers = $this->service->search($request->get("keywords"))->limit(5)->get();
        }else{
            $suppliers = $this->service->all(['id','name', 'code']);
        }
        return $this->successResponse(['suppliers' => NameResource::collection($suppliers)]);
    }

    public function create(){
        $countries = Country::where('name','egypt')->get();
//        $categories= Category::product()->get();
        $currencies = Currency::all();
        $employees = Employee::active()->get(['id','name']);
        $methods = Payment::$METHODS;
        return $this->successResponse(['countries' => $countries , 'currencies'=>$currencies,'employees'=>$employees,'methods'=>$methods]);
    }

    public function getStates(Request $request){
        $states = State::where('country_id', $request->country_id )->get();
//        $states = State::whereHas('country', fn($q)=>$q->where('name',$request->country)->get();
        return $this->successResponse(['states' => $states]);
    }

    public function store(StoreVendorRequest $request,Vendor $vendor = null){

        $validated =  $request->all();
        try {
            DB::beginTransaction();

            $data =  [
                'business_name' => $validated['business_name'],
                'name' => $validated['name'],
                'code' => $validated['code'],
                'phone' => $validated['phone'],
                'telephone' => $validated['telephone'],
                'email' => $validated['email'],
                'website' => $validated['website'],
                'warranty' => $validated['warranty'],
                'registration_number' => $validated['registration_number'],
                'tax_number' => $validated['tax_number'],
                'credit_limit' => $validated['credit_limit'],
                'payment_method' => $validated['payment_method'],
                'responsible_id' => $validated['responsible_id'],
            ];

//            $vendor = Vendor::updateOrCreate(
//                ['id',$validated['id']],
//
//            );

            if ($vendor) {
                $vendor->update($data);
            } else {
                $vendor = Vendor::create($data);
            }

            foreach ($validated['contacts'] as $contact) {
                Contact::create([
                    'name' => $contact['name'],
                    'email' => $contact['email'],
                    'phone' => $contact['phone'],
                    'telephone' => $contact['telephone'],
                    'contactable_id' => $vendor->id,
                    'contactable_type' => 'App\Models\Purchases\Vendor',
                ]);
            }
            Location::create([
                'address' => $validated['address'],
                'city' => $validated['city'],
                'state_id' => $validated['state_id'],
                'postal_code' => $validated['postal_code'],
                'locationable_id' => $vendor->id,
                'locationable_type' =>' App\Models\Purchases\Vendor',
            ]);

           $account = Account::create([
               'name' => $validated['account_name'] ?? $validated['name'],
               'type' => 'credit',
               'currency_id' => $validated['currency_id'],
               'category_id' => 2,
                'opening_balance' => $validated['opening_balance'],
                'opening_date' => $validated['opening_date'],
           ]);
           $vendor->account_id = $account->id;
            $vendor->save();
            DB::commit();
            return $this->successResponse(null,null,'Vendor created successfully');
        } catch (\Exception $e) {
            DB::rollback();
//            return $e ;
            return response()->json(['message' => 'Failed to create or update Vendor','error'=>$e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        $vendorData = new ShowVendorResource(Vendor::with('contacts','locations','account')->findOrFail($id));
        return $this->successResponse($vendorData);
    }

    public function storeContact(StoreContact $request): \Illuminate\Http\JsonResponse
    {
        Contact::created($request->all());
        return $this->successResponse(null,null,'Contact Created Successfully');
    }
}
