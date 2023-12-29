<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Mail\VerificationMail;
use App\Models\System\Invitation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class LoginController extends Controller
{
    use AuthenticatesUsers,CanResetPassword;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */

    protected $redirectTo = '/admin';
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        //$admin_panel = DB::table('settings')->where('key', 'admin_url')->value('value');
        // if($admin_panel == '' || $admin_panel == NULL){
        $admin_panel = 'admin';
        // }
        $this->redirectTo = '/'.$admin_panel;
        $this->middleware('guest', ['except' => 'logout']);
    }

    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    public function redirectTo ()
    {
        return route('admin.dashboard');
    }

    public function reg(Request $request,$token = null)
    {
        if (!$token)  return abort(404,'invitation token is required');
        $invitation = Invitation::where('invitation_token', $token)->first();
        if (!$invitation){
             abort(404,'invalid invitation token');
        }
        $exist = User::where('email', $invitation->email)->first();

        if ($invitation->registerd_at || $exist){
            return abort(404,'invitation link already used');
        }

        if ($invitation->expire_at < now()){
            return abort(403,'Expired Invitation');
        }

//        return  abort(403,'will create later');
//        return redirect()->route('admin.showRegisterForm',['email'=>$invitation->email]);
        return view('pages.auth.register')->with('email',$invitation->email);
    }

    public function register(Request $request)
    {
//        dd($request->all());
        $data = $request->validate([
            'name' => 'required|string',
            'username' => 'required|string|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|confirmed'
        ]);

        $invitation = Invitation::where('email',$data['email'])->where('expire_at','>',now())->first();
        if (!$invitation){
            return redirect()->back()->withErrors(['email'=>'Email Has been Changed']);
        }

        $user =  \App\Models\User::create([
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'email_verified_at' => now()
        ]);

        if ($invitation->role_id){
            $role = Role::findById($invitation->role_id);
            $user->assignRole($role);
        }

        return redirect()->route('admin.login');
    }
}
