<?php

namespace App\Http\Controllers\Api\System;

use App\Http\Controllers\Api\Controller;
use App\Models\Common\JobAndCrafts;
use App\Models\Common\PreUniversityCertificateGroup;
use App\Models\Grades\GradeCategory;
use App\Models\Grades\GradeItem;
use App\Models\Payments\PaymentProvider;
use App\Models\Programs\Bylaw;
use App\Models\Programs\Course;
use App\Models\Programs\Faculty;
use App\Models\Programs\Level;
use App\Models\Programs\Mark;
use App\Models\Programs\MarkCategory;
use App\Models\Programs\Program;
use App\Models\Services\Applicant;
use App\Models\Services\Certificate;
use App\Models\Services\Service;
use App\Models\Study\Offering;
use App\Models\Study\OfferingInstructor;
use App\Models\Study\Registration;
use App\Models\Study\StageCode;
use App\Models\Study\StageGroup;
use App\Models\Study\Term;
use App\Models\System\Setting;
use App\Models\System\System;
use App\Models\Users\Country;
use App\Models\Users\Department;
use App\Models\Users\Instructor;
use App\Models\Users\InstructorRank;
use App\Models\Users\PreStudyType;
use App\Models\Users\Student;
use App\Models\Users\User;
use App\Models\Users\UserAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Models\Common\Building;

class LookupController extends Controller
{

    public function index(Request $request)
    {
        $lookups = [];
        foreach ($request->lookups as $lookup) {
            $lookup = (object)$lookup;
            if (isset($lookup->table) && $lookup->table) {
                $table = DB::table("$lookup->table");
                $table = isset($lookup->with) && $lookup->with ? $table->with($lookup->with) : $table;
                $table = isset($lookup->culomns) && $lookup->culomns ? $table->select("$lookup->culomns") : $table->select('id', 'name', 'name_local');
                $table = isset($lookup->all) && $lookup->all ? $table->whereRemoved(0) : $table;
                $table = System::accessFilter($table, $lookup->table);
                $table = isset($lookup->query) && $lookup->query ? $table->whereRaw("$lookup->query") : $table;
                if (isset($lookup->keywords) && $lookup->keywords) {
                    $keywords = mb_ereg_replace(" ", "%", getFTS($lookup->keywords));
                    $table->whereRaw("COALESCE(search_text,'') like '%$keywords%'");
                }
                $table = isset($lookup->orderBy) && $lookup->orderBy ? $table->orderBy("$lookup->orderBy") : $table->orderBy('name')->orderBy('name_local');
                $lookups["$lookup->table"] = $table->cursor();

            }

        }
        return success($lookups);
    }


}
