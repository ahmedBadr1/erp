<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\System\Invitation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends ApiController
{
    public function __construct()
    {
        parent::__construct();
         $this->middleware('guest');
    }

    public function login(Request $request)
    {
        $valid = $request->validate(
            [
             'email' => 'required|email',
             'password' => 'required|string'
            ]
        );
        $user = \App\Models\User::where('email', $valid['email'])->first();

        if (!$user || !Hash::check($valid['password'], $user->password)) {
            return $this->successResponse('wrong email or password');
        }
        $token = $user->createToken('app') ;

        return $this->successResponse(['user'=>new UserResource($user),'token'=> $token->accessToken]);
    }

    public function register(Request $request)
    {
        $data = $request->validate([
                'name' => 'required|string',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|confirmed'
            ]);

       $user =  \App\Models\User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password'])
        ]);

        $token = $user->createToken('app') ;

        return $this->successResponse(['user'=>new UserResource($user),'token'=> $token->accessToken]);
    }

    public function reg(Request $request)
    {
        $token = $request->get('invitation_token');
        if (!$token)  return error(404,'invitation token is required');
        $invitation = Invitation::where('invitation_token', $token)->first();
        if (!$invitation){
            return  $this->errorResponse('invalid invitation token',404);
        }
        $exist = User::where('email', $invitation->email)->first();

        if ($invitation->registerd_at || $exist){
            return $this->errorResponse('invitation link already used',404);
        }
        $email = $invitation->email;
        //   dd($email);
        return $this->successResponse(['email' => $email]);
    }

    public function docs ()
    {
        $links = ['auth','roles','users','profile','dashboard'];
        $routes = (object) [];
        $routes->login = (object) [
            'name' =>  '/login',
            'methods' =>   (object) ['post'],
            'function' =>  'Api/AuthController@login',
            'params' =>   (object) [
                'email' => 'required|email',
                'password' => 'required'
            ],
            'response' => 201
        ];
        $routes->logout = (object) [
            'name' =>  '/users/logout',
            'methods' =>   (object) ['post'],
            'function' =>  'Api/DashboardController@logout',
            'params' =>   (object) [
           '-'=>'-'
            ],
            'response' => 201
        ];
        $routes->profile = (object) [
            'name' =>  '/profile',
            'methods' =>   (object) ['post'],
            'function' =>  'Api/DashboardController@profile',
            'params' =>   (object) [
                'name'=>'required|string',
                'bio'=> 'nullable|string',
                'email'=>'required|email',
                'phone'=>'required|numeric',
                'address'=> 'nullable|string',
                'area'=> 'nullable|string',
                'state'=> 'required',
                'profile_photo'=> 'nullable|image',
                'url'=> 'nullable|string',
            ],
            'response' => 200
        ];
        $routes->docs = (object) [
            'name' =>  '/docs',
            'methods' =>   (object) ['get'],
            'function' =>  'Api/AuthController@docs',
            'params' =>   (object) [

            ],
            'response' => 200
        ];
        $routes->invite = (object) [
            'name' =>  '/invite',
            'methods' =>   (object) ['post'],
            'function' =>  'Api\Hr\UsersController@process',
            'params' =>   (object) [
                'email' => 'required|email|unique:invitations|unique:users'
            ],
            'response' => 200
        ];
        $routes->reg = (object) [
            'name' =>  '/reg',
            'methods' =>   (object) ['post'],
            'function' =>  'Api\AuthController@reg',
            'params' =>   (object) [
                'invitation_token' => 'required'
            ],
            'response' => 200
        ];
        $routes->register = (object) [
            'name' =>  '/register',
            'methods' =>   (object) ['post'],
            'function' =>  'Api\AuthController@register',
            'params' =>   (object) [
                'name' => 'required|string',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|confirmed',
                'password_confirmation' => 'required|string|asPassword'
            ],
            'response' => 201
        ];
        $routes->notifications = (object) [
            'name' =>  '/notifications',
            'methods' =>   (object) ['post'],
            'function' =>  'Api\DashboardController@notifications',
            'params' =>   (object) [
            ],
            'response' => 200
        ];
        $routes->unreadNotifications = (object) [
            'name' =>  '/unread-notifications',
            'methods' =>   (object) ['post'],
            'function' =>  'Api\DashboardController@unreadNotifications',
            'params' =>   (object) [
            ],
            'response' => 200
        ];
        $routes->notificationsRead = (object) [
            'name' =>  '/notifications/read',
            'methods' =>   (object) ['post'],
            'function' =>  'Api\DashboardController@markAsRead',
            'params' =>   (object) [
            ],
            'response' => 200
        ];

        return view('api/docs', compact('links','routes'));
    }
}
