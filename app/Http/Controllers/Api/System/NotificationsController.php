<?php

namespace App\Http\Controllers\Api\System;

use App\Http\Controllers\Api\Controller;
use Illuminate\Http\Request;
use Validator;
use DB;

use App\Models\System\System;
use App\Models\System\Notification;
use App\Models\System\Paginator;

class NotificationsController extends Controller
{

    public function notifications(Request $request)
    {

        $query = Notification::select('notifications.*');

        $query->where('notifications.user_id', auth()->id());

        return Paginator::process('notifications', $request, $query, [

            'default_order_by' => 'id',
            'default_order_direction' => 'DESC',
            'transform_result' => function ($result, $query) {
                $result->meta->unseen_count = $query->where('seen', 0)->count();
                return $result;
            }
        ]);
    }

    public function remove(Request $request, Notification $notification)
    {

        $notification = requestModel($notification, Notification::class);

        if ($notification->user_id != auth()->id()) return error(System::HTTP_UNAUTHORIZED);

        $notification->delete();

        return success();
    }

    public function removeAll(Request $request)
    {

        Notification::where('user_id', auth()->id())->delete();

        return success();
    }

    public function see(Request $request, Notification $notification)
    {

        $notification = requestModel($notification, Notification::class);

        if (!$notification->seen &&
            $notification->user_id == auth()->id()) {
            $notification->seen = true;
            $notification->save();
        }

        return success();
    }
}
