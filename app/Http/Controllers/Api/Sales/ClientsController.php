<?php

namespace App\Http\Controllers\Api\Sales;

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Controller;
use App\Http\Requests\ListRequest;
use App\Http\Requests\Sales\StoreVendorRequest;
use App\Http\Requests\System\StoreContact;
use App\Http\Resources\NameResource;
use App\Http\Resources\Sales\ShowClientResource;
use App\Http\Resources\Sales\ClientsResource;
use App\Models\Accounting\Account;
use App\Models\Accounting\Currency;
use App\Models\Hr\Employee;
use App\Models\Sales\Client;
use App\Models\System\Contact;
use App\Models\System\Country;
use App\Models\System\Location;
use App\Models\System\State;
use App\Services\Sales\ClientService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClientsController extends ApiController
{
    public function __construct()
    {
        parent::__construct();
        $this->class = "clients";
        $this->table = "clients";
        $this->middleware('auth');
        $this->middleware('permission:sales.clients.view', ['only' => ['index', 'show']]);
        $this->middleware('permission:sales.clients.create', ['only' => ['create', 'store']]);
        $this->middleware('permission:sales.clients.edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:sales.clients.delete', ['only' => ['destroy']]);

        $this->service = new ClientService();
    }

    public function list(ListRequest $request)
    {
        if (auth('api')->user()->cannot('sales.clients.index')) {
            return $this->deniedResponse(null, null, 403);
        }
        if ($request->has("keywords")) {
            $clients = $this->service->search($request->get("keywords"))->limit(5)->get();
        }else{
            $clients = $this->service->all(['id','name', 'code']);
        }
        return $this->successResponse(['clients' => NameResource::collection($clients)]);
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
                    'contactable_type' => 'App\Models\Sales\Vendor',
                ]);
            }
            Location::create([
                'address' => $validated['address'],
                'city' => $validated['city'],
                'state_id' => $validated['state_id'],
                'postal_code' => $validated['postal_code'],
                'locationable_id' => $vendor->id,
                'locationable_type' =>' App\Models\Sales\Vendor',
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
        $vendorData = new ShowClientResource(Client::with('firstContact','firstAddress','account')->findOrFail($id));
        return $this->successResponse($vendorData);
    }

}
