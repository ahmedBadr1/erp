<?php

namespace App\Http\Controllers\Api\Hr;

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInveitationRequest;
use App\Http\Resources\UserResource;
use App\Mail\InvitationMail;
use App\Models\System\Invitation;
use App\Models\User;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;


class UsersController extends ApiController
{
    use SoftDeletes;

    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:hr');
        //     $this->middleware('permission:invite');
    }

    /**
     * Display a listing of the resource.
     *
     * @param
     * @return View
     */
    public function index( )
    {
        $users = User::with(['roles'=>fn($q)=>$q->select('id','name')])
            ->orderBy('id','DESC')
            ->paginate(10);
        return $this->successResponse(UserResource::collection($users));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create()
    {
        $roles = Role::whereNotIn('name',['Feedback'])->pluck('name','name');
        return success($roles);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $roles = Role::whereNotIn('name',['Feedback'])->pluck('name')->toArray();

        $this->validate($request,[
            'name'=>'required',
            'email'=> 'required|email|unique:users,email',
            'role'=>'required|in:' . implode(',', $roles),
            'password'=> 'required',

        ]);
        $input = $request->all();
        $input['password'] = Hash::make($input['password']);
        $user = User::create($input);
        $user->assignRole($request->input('role'));
//        Toastr::SUCCESS('User Created Successfully');
//        Toastr::info('New Employee Created also');
        return success(['message'=>'User Created Successfully']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id)
    {
        $user = User::findOrFail($id);

//        if(! $user->hasAnyRole(Role::all())) {
//            $user->assignRole('customer');
//        }

        $userRole = DB::table('roles')->where('id',$user->role)->get();

        $roles = $user->getRoleNames();

        return success(['user' => $user,'userRole'=>$userRole,'roles' => $roles ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(Request $request)
    {
        //
        $user = User::findOrFail($request['id']);
        $roles = Role::pluck('name')->all();
        $userRole = $user->roles->pluck('name')->all();

        return success(['user' => $user,'userRole'=>$userRole,'roles' => $roles ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request,[
            'name'=>'required',
            'email'=> 'required|email',
            'role'=>'required'
        ]);
        $input = $request->all();
        $user =  User::findOrFail($id);
        $user->update($input);

        DB::table('model_has_roles')->where('model_id',$id)->delete();
        //   $roleId = DB::table('roles')->where('name',$request->input('role'))->value('id');

        //    DB::table('users')->where('id',$id)->update(['role'=>$roleId]);
        $user->assignRole($request->input('role'));
        return redirect()->route('hr.users.index')->with('success','User Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return RedirectResponse
     */
    public function destroy($id): RedirectResponse
    {
        User::findOrFail($id)->trash();
        return redirect()->route('hr.users.index')->with('success','User Deleted Successfully');
    }

    public function process(StoreInveitationRequest $request)
    {
        //   dd($request->all());
        $invitation = new Invitation($request->all());
        $invitation->generateInvitationToken();
        $invitation->sent_by = auth()->id();
        $invitation->save();
        $link = $invitation->getLink();
        try{
            Mail::to($invitation->email)->send(new InvitationMail(auth()->user()->name, $link));
        }catch (\Exception $exception){
            return error(200,'Faild to send Mail');
        }
        return success($invitation);
    }

    public function invitations(): \Illuminate\Http\JsonResponse
    {
//        activity()
//            ->causedBy(auth()->user())
//            ->performedOn(email)
//            ->event('invited')
//            ->log('The user has invited by '.);
        $invitations = Invitation::with(['sender'=> fn($q) => $q->select('id','name')])->orderBy('created_at', 'desc')->get();

        return success($invitations);
    }
}
