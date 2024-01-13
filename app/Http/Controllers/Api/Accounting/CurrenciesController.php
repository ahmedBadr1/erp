<?php

namespace App\Http\Controllers\Api\Accounting;

use App\Filters\ByCode;
use App\Filters\ByName;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Accounting\UpdateCurrencyRequest;
use App\Http\Requests\ListRequest;
use App\Http\Resources\Accounting\CurrencyResource;
use App\Models\Accounting\Account;
use App\Models\Accounting\Currency;
use App\Services\Accounting\CurrencyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Pipeline;

class CurrenciesController extends ApiController
{
    public function __construct()
    {
        parent::__construct();
        $this->class = "currencies";
        $this->table = "currencies";
        $this->middleware('auth');
        $this->middleware('permission:accounting.currencies.view', ['only' => ['index', 'show']]);
        $this->middleware('permission:accounting.currencies.create', ['only' => ['create', 'store']]);
        $this->middleware('permission:accounting.currencies.edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:accounting.currencies.delete', ['only' => ['destroy']]);
        $this->service = new CurrencyService();
    }

    public function index(){
       $currencies = CurrencyResource::collection(Currency::with('gainAccount','lossAccount')->get());
       return $this->successResponse($currencies);
    }

    public function list(ListRequest $request)
    {
        return  CurrencyResource::collection($this->service->search($request->get('keywords'))->get());
    }

    public function currencies(ListRequest $request)
    {
        return $this->successResponse(Pipeline::send(Currency::search($request->get('keywords'))->active())
            ->through([ByName::class, ByCode::class])
            ->thenReturn()
            ->orderBy($request->get('orderBy') ?? $this->orderBy, ($request->get('orderDesc') ?? $this->orderDesc) ? 'desc' : 'asc')
            ->limit($request->get('limit') ?? $this->limit)->pluck('name', 'id')->toArray());
    }

    public function show(\Illuminate\Support\Facades\Request $request, $id)
    {
        return $this->successResponse(new CurrencyResource(Currency::active()->whereId($id)->firstOrFail()));
    }

    public function create(){
        $currencies = CurrencyResource::collection(Currency::with('gainAccount','lossAccount')->get());
        $accounts = Account::active()->get('name','id','code');
        return $this->successResponse(['accounts'=>$accounts,'currencies'=>$currencies]);
    }
     public function store(Request $request)
        {
//            return $this->successResponse($request->all());
            $data = $request->validate([
                'name' => 'required|string',
                'code' => 'required|string',
                'symbol' => 'required|string',
                'ex_rate' => 'nullable|numeric',
                'sub_unit' => 'nullable|numeric',
                'gain_account' => 'nullable|exists:accounts,id',
                'loss_account' => 'nullable|exists:accounts,id',
            ]);

            try {
                DB::beginTransaction();

                Currency::create([
                    ...$data,
                    'last_rate' => $data['ex_rate'] ? now() : null ,
                ]);

                DB::commit();

                return $this->successResponse(null,__('message.created',['model'=>__('Currency')]),201);
            } catch (\Exception $e) {
                DB::rollback();
    return  $this->errorResponse($e->getMessage());
            }
        }

        public function update(UpdateCurrencyRequest $request,int $id)
        {
            $data = $request->validated();
//            return $data ;
            $currency = Currency::findOrFail($id);
            if (isset($data['ex_rate']) && $data['ex_rate'] > 0){
                $data["last_rate"] = now();
            }
            $currency->update($data);
            $currencies = CurrencyResource::collection(Currency::with('gainAccount','lossAccount')->get());
//            sleep(3);
            return $this->successResponse(['currencies'=>$currencies],__('message.updated',['model'=>__('Currency')]),201);
        }

        public function destroy(Currency $currency)
        {
            if ($currency->id === 1 || Account::where("currency_id",$currency->id)->exists()){

               return $this->errorResponse('Can\'t Delete this Currency, Try Deactivate it');
            }
            $currency->delete();

            return $this->successResponse(null,__('message.deleted',['model'=>__('Currency')]),201);

        }

}
