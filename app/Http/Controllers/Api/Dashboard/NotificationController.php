<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Controller;
use App\Http\Requests\ListRequest;
use App\Http\Resources\Dashboard\NotificationCollection;
use App\Http\Resources\Dashboard\NotificationResource;
use App\Models\System\Notification;
use App\Notifications\MainNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends ApiController
{
    public function index(ListRequest $request): NotificationCollection
    {
        $notifications = Notification::query()
            ->where("notifiable_type", 'user')
            ->where("notifiable_id", Auth::guard('api')->id())
            ->when($request->get('start_date'), function ($query) use ($request) {
                $query->where('created_at', '>=', $request->get('start_date'));
            })
            ->when($request->get('end_date'), function ($query) use ($request) {
                $query->where('created_at', '<=', $request->get('start_date'));
            })
            ->when($request->get('keywords'), function ($query) use ($request) {
                $query->where('data', 'like', '%' . $request->get('keywords') . '%');
            })
            ->latest()
            ->paginate($request->get('per_page') ?? $this->limit,['*'],'page',$request->get('current_page'));
//        auth('api')->user()->unreadNotifications->markAsRead();

        return  new NotificationCollection($notifications);
    }

    public function markAllAsRead()
    {
        $user = \auth('api')->user();
        $user->unreadNotifications->markAsRead();
        return $this->successResponse('success');
    }

    public function markAsRead(Request $request,$id)
    {
        try {
            $notification = Notification::where('notifiable_id',auth('api')->id())->where("id",$id)->update(['read_at'=>now()]);
        }catch (\Exception $e){
            return $this->errorResponse($e->getMessage());
        }
        return $this->successResponse('success');
    }

    public function unreadNotifications()
    {
        $user = \auth('api')->user();
//        for ($i = 0; $i < 100; $i++) {
//        $data = [];
//        $data['message'] = 'Welcome to Our app , Hope you enjoy it ;)'  ;// .$i  ;
//        $data['url'] = '/';
//        $user->notify(new MainNotification($data));
//        sleep(1);
//        }

        return $this->successResponse(NotificationResource::collection($user->unreadNotifications()->limit(5)->get()));
    }

    public function count()
    {
        $user = \auth('api')->user();
        return $this->successResponse(['count' => $user->unreadNotifications()->count()]);
    }

}
