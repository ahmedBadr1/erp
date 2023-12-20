<?php

namespace App\Http\Controllers\Api\Purchases;

use App\Http\Controllers\Controller;
use App\Http\Requests\ListRequest;
use App\Http\Requests\Purchases\StoreVendorRequest;
use App\Http\Requests\System\StoreContact;
use App\Http\Resources\Hr\EmployeesResource;
use App\Http\Resources\Purchases\ShowVendorResource;
use App\Http\Resources\Purchases\VendorsResource;
use App\Models\Accounting\Account;
use App\Models\Accounting\AccCategory;
use App\Models\Accounting\Currency;
use App\Models\Hr\Employee;
use App\Models\Purchases\Payment;
use App\Models\Purchases\Vendor;
use App\Models\System\Contact;
use App\Models\System\Country;
use App\Models\System\Location;
use App\Models\System\State;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VendorsController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:purchases');
    }

    public function list(ListRequest $request)
    {
        $input = $request->all();
        //
        return  VendorsResource::collection( Vendor::search($input['keywords'])
//            ->with('locations','employee')
//            ->orderBy($input['orderBy'], $input['orderDesc'] ? 'desc' : 'asc')
            ->paginate($input['limit']));
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
                'opening_balance_date' => $validated['opening_balance_date'],
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
