<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ListRequest;
use App\Http\Resources\Dashboard\NotificationCollection;
use App\Http\Resources\Dashboard\NotificationResource;
use App\Http\Resources\System\UserProfileResource;
use App\Notifications\MainNotification;
use App\Services\Accounting\TransactionService;
use App\Services\Inventory\InvTransactionService;
use App\Services\Purchases\BillService;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use  \App\Models\System\Notification;

/**
 * @OA\Put(
 *     path="/users/{id}",
 *     summary="Updates a user",
 *     @OA\Parameter(
 *         description="Parameter with mutliple examples",
 *         in="path",
 *         name="id",
 *         required=true,
 *         @OA\Schema(type="string"),
 *         @OA\Examples(example="int", value="1", summary="An int value."),
 *         @OA\Examples(example="uuid", value="0006faf6-7a61-426c-9034-579f2cfcfa83", summary="An UUID value."),
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="OK"
 *     )
 * )
 */
class DashboardController extends ApiController
{

    public function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
    }

    public function index()
    {
        $user = \auth()->user();
        return $this->successResponse($user, '', 'dashboard goes ere');
    }

    public function notifications(ListRequest $request)
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
        $data = [];
        $data['message'] = 'Welcome to Our app , Hope you enjoy it ;)'  ;// .$i  ;
        $data['url'] = '/';
        $user->notify(new MainNotification($data));
//        sleep(1);
//        }

        return $this->successResponse(NotificationResource::collection($user->unreadNotifications()->limit(5)->get()));
    }

    public function count()
    {
        $user = \auth('api')->user();
        return $this->successResponse(['count' => $user->unreadNotifications()->count()]);
    }

    public function profile()
    {
        return $this->successResponse(new UserProfileResource(auth('api')->user()));
    }

    public function profileUpdate(Request $request)
    {
        //  dd($request->all());
        $user = Auth::user();

        $this->validate($request, [
            'first_name' => 'nullable|string',
            'last_name' => 'nullable|string',
            'bio' => 'nullable|string',
            'phone' => 'nullable|numeric',
            'urls' => 'nullable|array',
            'urls.*' => 'nullable|string',
//            'state' => 'required',
            'image' => 'nullable|image',
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $user->image = uploadFile($image,'users',$user->id,'image',true);
            $user->save();
        }

        $input = $request->all();

        if (isset($input['first_name']) ) {
            $user->name = ["first"=> $input['first_name'] ?? null ,"last"=>$input['last_name']];
        }

        if (isset($input['bio'])) {
            $user->profile->bio = $input['bio'];
        }
        if (isset($input['phone'])) {
            $user->phone = $input['phone'];
        }
//        if (isset($input['area'])) {
//            $user->profile->area = $input['area'];
//        }
        if (isset($input['urls'])) {
            $user->profile->urls = $input['urls'];
        }

        $user->push();

        return $this->successResponse(new UserProfileResource(auth('api')->user()),'Profile Updated Successfully');
    }

    public function logout(Request $request)
    {
        // $request->user()->token()->revoke();

        $user = auth()->user();
        if ($user && $user->token()) {
            $token = $user->token();
            $token->revoke();
            $token->delete();
            //   Activity::log('user\logout', $user);
        }
        return $this->successResponse(['message' => 'logged out !']);
    }

    public function search(ListRequest $request)
    {
        $search = $request->get('keywords') ;
        $transactions = (new TransactionService())->search($search)->limit(2)->get();
        $bills = (new BillService())->search($search)->limit(2)->get();
        $invTransactions = (new InvTransactionService())->search($search)->limit(2)->get();

        return $this->successResponse([
            'transactions'=>$transactions,
            'bills' => $bills,
            'invTransactions' => $invTransactions,
        ]);
    }

}
