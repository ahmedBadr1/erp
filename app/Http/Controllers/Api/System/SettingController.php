<?php

namespace App\Http\Controllers\Api\System;

use App\Http\Controllers\Api\Controller;
use App\Models\Programs\Bylaw;
use App\Models\Programs\Faculty;
use App\Models\Programs\Program;
use App\Models\Study\Term;
use App\Models\System\Log;
use App\Models\System\Paginator;
use App\Models\System\Setting;
use App\Models\System\System;
use Illuminate\Http\Request;

class SettingController extends Controller
{

    public function settings(Request $request)
    {

        $query = Setting::select('*');

        $query = ($request->faculty_id) ? $query->whereFacultyId($request->faculty_id) : $query;
        $query = ($request->bylaw_id) ? $query->whereBylawId($request->bylaw_id) : $query;
        $query = ($request->program_id) ? $query->whereProgramId($request->program_id) : $query;
        $query = ($request->term_id) ? $query->whereTermId($request->term_id) : $query;
        $query = ($request->name) ? $query->where('name', $request->name) : $query;
        $lookup = [];
        if ($request->no_lookup != true) {
            $lookup = [
                "faculties" => Faculty::whereRemoved(0)->select('name as faculty_name', 'name_local as faculty_name_local', 'id')->get(),
                "bylaws" => Bylaw::whereRemovedAndFacultyId(0, $request->faculty_id)->select('name as bylaw_name', 'name_local as bylaw_name_local', 'id')->get(),
                "programs" => Program::whereRemovedAndBylawId(0, $request->bylaw_id)->select('name as name', 'name_local as name_local', 'id')->get(),
                "terms" => Term::whereRemoved(0)->select('name', 'name_local', 'id')->orderBy('year', 'DESC')->get(),
            ];
        }

        return Paginator::process('settings', $request, $query, ['lookup' => $lookup, 'ignore_removed' => true, 'ignore_text_search' => true]);
    }

    public function put(Request $request, Setting $setting = null)
    {

        $new = false;

        $exist = Setting::whereProgramIdAndFacultyIdAndTermIdAndName($request->program_id, $request->faculty_id, $request->term_id, $request->name);
        $exist = $setting? $exist->where('id', '!=', $setting->id)->first() : $exist->first();


        if ($exist) {
            return error(System::ERROR_ITEM_ALREADY_EXISTS);
        }

        if (!$setting) {

            $new = true;
            $setting = new Setting();
        }

        $setting->fill($request->all());
        $setting->save();

        Log::log(($new) ? 'setting\add' : 'setting\edit', $setting);

        return success($setting->data(System::DATA_BRIEF));
    }

    public function get(Request $request, Setting $setting, $details = System::DATA_DETAILS)
    {
        return success($setting->data($details));
    }

    public function remove(Request $request, Setting $setting)
    {
        $setting->delete();
        Log::log('setting\remove', $setting);
        return success(true);
    }

}
