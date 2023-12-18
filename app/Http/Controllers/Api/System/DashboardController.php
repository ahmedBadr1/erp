<?php

namespace App\Http\Controllers\Api\System;

use App\Http\Controllers\Api\Controller;
use App\Models\Payments\Payment;
use App\Models\Programs\Bylaw;
use App\Models\Programs\Course;
use App\Models\Programs\Faculty;
use App\Models\Programs\Program;
use App\Models\System\Log;
use App\Models\System\System;
use App\Models\Users\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{

    public function index(Request $request)
    {
        if (!can('admin_system')) return error(System::HTTP_UNAUTHORIZED);

        $users_count = User::select(DB::raw('count(type) as count'), 'type')->groupBy('type')->get()->transform(function ($item) {
            $item->type = User::decodeType($item->type) ? User::decodeType($item->type)->name . "s" : '';
            return $item;
        });

        $users_data = [];
        foreach ($users_count as $item) {
            $users_data[$item->type] = $item->count;
        }

        $statistic = [
            'faculties_count' => Faculty::whereRemoved(0)->count(),
            'bylaws_count' => Bylaw::whereRemoved(0)->count(),
            'courses_count' => Course::whereRemoved(0)->count(),
            'programs_count' => Program::whereRemoved(0)->count(),
            'users_count' => $users_data,
        ];

        return success($statistic);

    }

    public function faculty(Request $request)
    {

        if (!can('admin_system')) return error(System::HTTP_UNAUTHORIZED);

        $data = (object)[];
        $data->studnets_per_faculty = Faculty::select('code as name')->withCount('students')->whereRemoved(0)->orderBy('students_count', 'DESC')->limit(15)->get();
        $data->instructors_per_faculty = Faculty::select('code as name' , 'id')->whereRemoved(0)->limit(15)->get()->transform(function ($item) {
            $item->instructors_count = $item->instructors()->count();
            return $item;
        });
        $data->programs_per_faculty = Faculty::select('code as name')->withCount('programs')->whereRemoved(0)->orderBy('programs_count', 'DESC')->limit(15)->get();
        $data->courses_per_faculty = Faculty::select('code as name')->withCount('courses')->whereRemoved(0)->orderBy('courses_count', 'DESC')->limit(15)->get();

        return success($data);
    }

    public function payment(Request $request)
    {

        if (!can('admin_system')) return error(System::HTTP_UNAUTHORIZED);

        $data = (object)[];

        $query = Payment::selectRaw('round(sum(paid)) as total ,  YEAR(payments.created_at) as label')
            ->groupBy('label')->orderBy('label')->limit(30);

        $data->payments_per_month = $query->get()->toArray();

        return success($data);
    }

    public function activities()
    {

        if (!can('admin_system')) return error(System::HTTP_UNAUTHORIZED);

        $data = (object)[];

        $query = Log::selectRaw('count(*) as count, DATE_FORMAT(logs.created_at, "%Y-%m-%d") as label')
            ->leftJoin('users', 'users.id', 'logs.user_id')
            ->orderBy('label')->groupBy('label');

        $data->activities_per_month = $query->get()->toArray();

        return success($data);
    }

}
