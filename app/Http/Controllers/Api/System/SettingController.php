<?php

namespace App\Http\Controllers\Api\System;

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\Controller;
use App\Http\Requests\ListRequest;
use App\Http\Requests\Setting\UpdateSettingRequest;
use App\Http\Resources\System\SettingResource;
use App\Models\Programs\Bylaw;
use App\Models\Programs\Faculty;
use App\Models\Programs\Program;
use App\Models\Study\Term;
use App\Models\System\Log;
use App\Models\System\Paginator;
use App\Models\System\Setting;
use App\Models\System\System;
use Hamcrest\Core\Set;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class SettingController extends ApiController
{

    public function __construct()
    {
        parent::__construct();
        $this->class = "setting";
        $this->table = "settings";
        $this->middleware('auth');
        $this->middleware('permission:setting.view', ['only' => ['index', 'show']]);
        $this->middleware('permission:setting.create', ['only' => ['create', 'store']]);
        $this->middleware('permission:setting.edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:setting.delete', ['only' => ['destroy']]);


//        $this->service = new SettingService();
    }


    public function index(ListRequest $request)
    {

        $query = Setting::query();

        $query = ($request->group) ? $query->where('group',$request->group) : $query;
        $query = ($request->type) ? $query->where('type',$request->type) : $query;
        $query = ($request->key) ? $query->where('key', $request->key) : $query;

        return $this->successResponse(SettingResource::collection($query->get()));
    }

    public function show(Request $request, $id)
    {
        $setting = Setting::whereId($id)->orWhere('key',$id)->firstOrFail();
        Gate::authorize('show',$setting);
        return $this->successResponse(new SettingResource($setting));
    }

    public function update(UpdateSettingRequest $request,  $id =null )
    {
        if (isset($id)){
            $setting = Setting::whereId($id)->orWhere('key',$id)->firstOrFail();
            $oldValue = $setting->value;
            $setting->update(['value'=> $request->get('value')]);
            return $this->successResponse(null,'Setting Updated Successfully');
        }
//        return  $request->get('settings') ;
//        $set = [] ;
        foreach ($request->get('settings') as $key => $value){
//            $set[$key] = $value ;
            Setting::where('key',$key)->update(['value'=>$value]);
        }
//        return $set;

//        activity()
//            ->performedOn($setting)
//            ->causedBy(auth()->user())
//            ->withProperties(['value' => $request->get('value')])
//            ->log( auth()->user()->username .' Change ' . $setting->key. ' Setting From '. $oldValue .' To '. $setting->value);
        return $this->successResponse(null,'Settings Updated Successfully');
    }



    public function remove(Request $request, Setting $setting)
    {
        $setting->delete();
        Log::log('setting\remove', $setting);
        return $this->successResponse(true);
    }

}
