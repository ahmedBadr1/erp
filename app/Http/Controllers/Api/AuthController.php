<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserRolesResource;
use App\Jobs\SendEmailJob;
use App\Models\System\Invitation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class AuthController extends ApiController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('guest')->except('logout');
    }

    public function login(Request $request)
    {
        $valid = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);
        $user = \App\Models\User::where('email', $valid['email'])->first();

        if (!$user || !Hash::check($valid['password'], $user->password)) {
            return $this->errorResponse('wrong email or password', 409);
        }

        if (!$user->active) {
            return $this->errorResponse('Your Account Is Suspended, Contact Admins For More', 409);
        }

        return $this->successResponse(['user' => new UserRolesResource($user), 'token' => $user->createToken('website')->accessToken]);
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'username' => 'required|string|max:199|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|confirmed'
        ]);

        $invitation = Invitation::whereNull('registered_at')->where('expire_at', '>', now())->where('email', $data['email'])->first();

        if (!$invitation) {
            return $this->errorResponse('Invalid Invitation Link', 404);
        }

//        return 'goo' ;

        $user = \App\Models\User::create([
            'username' => $data['username'],
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password'])
        ]);
        $invitation->registered_at = now();
        $invitation->save();

        if ($invitation->role_id) {
            $role = Role::findById($invitation->role_id);
            $user->assignRole($role);
        }

        return $this->successResponse([], 'Account Created Successfully');
        $token = $user->createToken('app');

        return $this->successResponse(['user' => new UserRolesResource($user), 'token' => $token->accessToken]);
    }

    public function check(Request $request)
    {
        $request->validate([
            'invitation_token' => 'required|string'
        ]);

        $invitation = Invitation::where('token',  $request->get('invitation_token'))->first();
        if (!$invitation) {
            return $this->errorResponse('Invalid Invitation Link', 404);
        }
        if (Carbon::parse($invitation->expire_at) < Carbon::now()) {
            return $this->errorResponse('Expired Invitation, Ask Admins To Resend', 404);
        }
        $exist = User::where('email', $invitation->email)->first();

        if ($invitation->registered_at || $exist) {
            return $this->errorResponse('Invitation link already used', 404);
        }

        return $this->successResponse(['email' => $invitation->email]);
    }

    /**
     * Sign out (logout).
     *
     */
    public function logout()
    {
        try {
            $user = Auth::guard('api')->user();
            // Revoke current user token
            $user
                ->tokens()
                ->where("id", $user->currentAccessToken()->id)
                ->delete();

            return response()->json(["status" => true]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 401);
        }
    }

    public function forget(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        $user = User::where('email',$request->only('email'))->firstOrFail();

        $token = Str::random(64);

        $latestReset =  DB::table('password_reset_tokens')->where('email',$request->email)->exists();

        if ($latestReset) {
            return $this->errorResponse('You Already Have Sent A Request', 404);
        }

//        if (Carbon::parse($latestReset->created_at) > Carbon::now()->addHours(1) ) {
//            return $this->errorResponse('You Must Wait An Hour To Accept ', 404);
//        }


        DB::table('password_reset_tokens')->insert([
            'email' => $request->email,
            'token' => $token,
            'created_at' => Carbon::now()
        ]);

        dispatch(new SendEmailJob(['email'=>$request->email,'link' => $this->getLink($token)],'ResetPasswordMail'));

        return $this->successResponse(null, 'Reset Link Sent Successfully');
    }

    private function getLink($token) {
        return urldecode( env('FRONT_APP_URL').'reset/' .$token);
    }


    public function checkPassword(Request $request)
    {
        $request->validate([
            'reset_token' => 'required|string'
        ]);
        $token = $request->get('reset_token');

        $reset = DB::table('password_reset_tokens')->where('token', $token)->first();
        if (!$reset) {
            return $this->errorResponse('Invalid Reset Password Link', 404);
        }
        $exist = User::where('email', $reset->email)->first();

        if (!$exist) {
            return $this->errorResponse('Invalid Reset Password Link', 404);
        }
//        return  $this->successResponse(Carbon::parse($reset->created_at) < (now()->addHours(1)) ) ;
        if (Carbon::parse($reset->created_at)->addHours(1) < Carbon::now()) {
            return $this->errorResponse('Expired Link, Please Send Another Request', 404);
        }

        return $this->successResponse(['email' => $reset->email]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'token' => 'required|string|exists:password_reset_tokens,token',
            'password' => 'required|string|confirmed'
        ]);

        $updatePassword = DB::table('password_reset_tokens')
            ->where([
                'email' => $request->email,
                'token' => $request->token
            ])
            ->first();

        if(!$updatePassword){
            return $this->errorResponse('Invalid Reset Password Link', 404);
        }
        $user = User::where('email',$request->get('email'))->first();

        if (!$user){
            return $this->errorResponse('No User With This Email', 200);
        }
        $user->password = Hash::make($request->get('password')) ;
        $user->save();
        DB::table('password_reset_tokens')
            ->where([
                'email' => $request->email,
                'token' => $request->token
            ])->delete();
        return $this->successResponse(null, 'Password Reset Successfully');
    }

    public function docs()
    {
        $links = ['auth', 'roles', 'users', 'profile', 'dashboard'];
        $routes = (object)[];
        $routes->login = (object)[
            'name' => '/login',
            'methods' => (object)['post'],
            'function' => 'Api/AuthController@login',
            'params' => (object)[
                'email' => 'required|email',
                'password' => 'required'
            ],
            'response' => 201
        ];
        $routes->logout = (object)[
            'name' => '/users/logout',
            'methods' => (object)['post'],
            'function' => 'Api/DashboardController@logout',
            'params' => (object)[
                '-' => '-'
            ],
            'response' => 201
        ];
        $routes->profile = (object)[
            'name' => '/profile',
            'methods' => (object)['post'],
            'function' => 'Api/DashboardController@profile',
            'params' => (object)[
                'name' => 'required|string',
                'bio' => 'nullable|string',
                'email' => 'required|email',
                'phone' => 'required|numeric',
                'address' => 'nullable|string',
                'area' => 'nullable|string',
                'state' => 'required',
                'profile_photo' => 'nullable|image',
                'url' => 'nullable|string',
            ],
            'response' => 200
        ];
        $routes->docs = (object)[
            'name' => '/docs',
            'methods' => (object)['get'],
            'function' => 'Api/AuthController@docs',
            'params' => (object)[

            ],
            'response' => 200
        ];
        $routes->invite = (object)[
            'name' => '/invite',
            'methods' => (object)['post'],
            'function' => 'Api\Hr\UsersController@process',
            'params' => (object)[
                'email' => 'required|email|unique:invitations|unique:users'
            ],
            'response' => 200
        ];
        $routes->reg = (object)[
            'name' => '/reg',
            'methods' => (object)['post'],
            'function' => 'Api\AuthController@reg',
            'params' => (object)[
                'invitation_token' => 'required'
            ],
            'response' => 200
        ];
        $routes->register = (object)[
            'name' => '/register',
            'methods' => (object)['post'],
            'function' => 'Api\AuthController@register',
            'params' => (object)[
                'name' => 'required|string',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|confirmed',
                'password_confirmation' => 'required|string|asPassword'
            ],
            'response' => 201
        ];
        $routes->notifications = (object)[
            'name' => '/notifications',
            'methods' => (object)['post'],
            'function' => 'Api\DashboardController@notifications',
            'params' => (object)[
            ],
            'response' => 200
        ];
        $routes->unreadNotifications = (object)[
            'name' => '/unread-notifications',
            'methods' => (object)['post'],
            'function' => 'Api\DashboardController@unreadNotifications',
            'params' => (object)[
            ],
            'response' => 200
        ];
        $routes->notificationsRead = (object)[
            'name' => '/notifications/read',
            'methods' => (object)['post'],
            'function' => 'Api\DashboardController@markAsRead',
            'params' => (object)[
            ],
            'response' => 200
        ];

        return view('api/docs', compact('links', 'routes'));
    }
}
