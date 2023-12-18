<?php

namespace App\Http\Controllers\Api\System;

use App\Http\Controllers\Api\Controller;
use Illuminate\Http\Request;
use Validator;
use DB;

use App\Models\System\System;
use App\Models\System\Log;
use App\Models\System\Paginator;

class LogsController extends Controller
{

    public function logs(Request $request)
    {

        if (!can('access_logs')) return error(System::HTTP_UNAUTHORIZED);

        $query = Log::select('logs.*');

        if ($request->user_id) {

            $query->where('logs.user_id', $request->user_id);
        }

        return Paginator::process('logs', $request, $query, [

            'default_order_by' => 'id',
            'default_order_direction' => 'DESC',
            'ignore_removed' => true
        ]);
    }

    public function get(Request $request, Log $log)
    {

        if (!can('access_logs')) return error(System::HTTP_UNAUTHORIZED);

        return success($log->data(System::DATA_DETAILS));
    }
}
