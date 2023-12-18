<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

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
        return success($user,'','dashboard goes ere');
    }

    public function notifications()
    {
     //   $notifications = \auth()->user()->notifications();
        $user =  \auth()->user();
        $notifications = $user->notifications;
        return success($notifications);
    }

    public function markAsRead()
    {
        $user =  \auth()->user();
        $user->unreadNotifications->markAsRead();
        $notifications = $user->notifications;
        return success($notifications);
    }

    public function unreadNotifications()
    {
        $user =  \auth()->user();
        $notifications = $user->unreadNotifications();
        return success($notifications);
    }

    public function profile()
    {
        $profile = auth()->user()->profile;
        return success($profile);
    }

    public function profileUpdate(Request $request)
    {
        //  dd($request->all());
        $user = Auth::user();

        $this->validate($request,[
            'name'=>'required|string',
            'bio'=> 'nullable|string',
            'email'=>'required|email',
            'phone'=>'required|numeric',
            'address'=> 'nullable|string',
            'area'=> 'nullable|string',
            'state'=> 'required',
            'profile_photo'=> 'nullable|image',
            'url'=> 'nullable|string',
        ]);

        $input = $request->all();

        $path = 'uploads/profiles/photos';
        if(!File::isDirectory($path)){
            File::makeDirectory($path, 0777, true, true);
        }

        if(! isset($input['profile_photo'])){
            $photoPath  = $user->profile->photo;
        }else {
            if(File::exists(storage_path().'/app/public/'.$user->profile->profile_photo)){
                //dd('found');
                File::delete(storage_path().'/app/public/'.$user->profile->profile_photo);
            }
            $photoPath =  $input['profile_photo']->store($path,'public');
            $user->profile->profile_photo = $photoPath;

        }
        //    dd($photoPath);
        if($input['bio']){
            $user->profile->bio = $input['bio'];
        }
        if($input['address']){
            $user->profile->address = $input['address'];
        }
        if($input['area']){
            $user->profile->area = $input['area'];
        }
        if($input['url']){
            $user->profile->url = $input['url'];
        }

        $user->push();

        return success($user->profile);

    }

    public function logout(Request $request )
    {
       // $request->user()->token()->revoke();

        $user = auth()->user();
        if ($user && $user->token()) {
            $token = $user->token();
            $token->revoke();
            $token->delete();
            //   Activity::log('user\logout', $user);
        }
        return success(['message'=>'logged out !']);
    }

}
