<?php

namespace App\Http\Controllers\Api\Hr;

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInveitationRequest;
use App\Http\Resources\System\InvitationResource;
use App\Http\Resources\UserResource;
use App\Jobs\SendEmailJob;
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
use App\Models\System\Role;


class UsersController extends ApiController
{

    public function __construct()
    {
        parent::__construct();
        $this->class = "user";
        $this->table = "users";
        $this->middleware('auth:api');
        $this->middleware('permission:users.view', ['only' => ['index', 'show']]);
        $this->middleware('permission:users.create', ['only' => ['create', 'store']]);
        $this->middleware('permission:users.edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:users.delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @param
     * @return View
     */
    public function index( )
    {
        if (auth('api')->user()->cannot('users.index')) {
            return $this->deniedResponse(null,null,403);
        }
//        return $this->deniedResponse([],[],401);
        $users = User::with('roles')
            ->latest()
            ->get();
//            ->paginate(10);
        return $this->successResponse(UserResource::collection($users));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        $request->all();
        if ($request->has("id")){
            $user = User::find($request->get('id'));
        }
        $roles = Role::whereNotIn('name',['Feedback'])->pluck('name')->toArray();
        return $this->successResponse(['roles'=>$roles,'user'=>isset($user) ? new UserResource($user) : null]);
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
            'username'=>'required|unique:users,username',
            'email'=> 'required|email|unique:users,email',
//            'role_id'=> 'required|numeric|exists:roles,id',
            'role'=>'required|in:' . implode(',', $roles),
            'password'=> 'required|confirmed',
            'active' => "nullable|boolean"

        ]);
        $input = $request->all();
        $input['password'] = Hash::make($input['password']);
        DB::beginTransaction();
        try {
            $user = User::create($input);
            $user->assignRole(Role::findByName($input['role'],'web'));
        }catch (e){
            return $this->errorResponse(e);
            DB::rollBack();
        }
        DB::commit();
        return $this->successResponse(null,'User Created Successfully');
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

        return $this->successResponse(['user' =>new UserResource( $user)]);
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

        return $this->successResponse(['user' => $user,'userRole'=>$userRole,'roles' => $roles ]);
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
        $roles = Role::whereNotIn('name',['Feedback'])->pluck('name')->toArray();

        $this->validate($request,[
            'name'=>'required',
            'username'=>'required|unique:users,username,'.$id,
            'email'=> 'required|email|unique:users,email,'.$id,
//            'role_id'=> 'required|numeric|exists:roles,id',
            'role'=>'required|in:' . implode(',', $roles),
//            'password'=> 'required|confirmed',
            'active' => "nullable|boolean"
        ]);
        $input = $request->all();


        DB::beginTransaction();
        try {
            $user =  User::findOrFail($id);
            $user->update($input);
            $user->roles()->detach();
            $user->assignRole(Role::findByName($input['role'],'web'));
        }catch (e){
            return $this->errorResponse(e);
            DB::rollBack();
        }
        DB::commit();
        return $this->successResponse(null,'User Updated Successfully');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     */
    public function destroy($id)
    {
        if ($id == auth('api')->id()){
            return $this->errorResponse('you cannt delete yourself',200);
        }

        $user = User::findOrFail($id);
        if ($user->active){
            return $this->errorResponse('you cannt delete Active User',200);
        }
        $user->delete();
        return $this->successResponse('success','User Deleted Successfully');
    }

    public function invite(StoreInveitationRequest $request)
    {
        $role = Role::findByName( $request->get('role'),'web')?->id ;
        $data = [
            'email' => $request->get('email'),
            'sent_by' => auth('api')->id(),
            'role_id' => $role ?? null,
            'token' => $this->generateInvitationToken($request->get('email')),
            'expire_at' => now()->addHours(24)
        ];

        //        activity()
//            ->causedBy(auth()->user())
//            ->performedOn(email)
//            ->event('invited')
//            ->log('The user has invited by '.);

        $invitation = Invitation::create($data);
        $data['link'] = $invitation->getLink();
        $data['user'] = auth('api')->user()->fullName;
        try{
            dispatch(new SendEmailJob($data,'InvitationMail'));
//            Mail::to($invitation->email)->send(new InvitationMail(auth('api')->user()->name, $link));
        }catch (\Exception $exception){
            return $this->errorResponse('Failed to send Mail',200);
        }
        return $this->successResponse( null,'Invitation Sent Successfully');
    }

    private function generateInvitationToken($email)
    {
        return md5(rand(0, 9) . $email . time());
    }

    public function invitations(Request $request)//: \Illuminate\Http\JsonResponse
    {
//    return $this->successResponse(!empty($request->get('registered')) );
        $invitations = Invitation::with(['sender'=> fn($q) => $q->select('id','name'),'role'=> fn($q) => $q->select('id','name')])
            ->when($request->has('expired') && empty($request->get('expired')),fn($q)=>$q->where('expire_at', '>',now()))
            ->when($request->has('expired') && !empty($request->get('expired')),fn($q)=>$q->where('expire_at', '<',now()))
            ->when($request->has('registered') && empty($request->get('registered')),fn($q)=>$q->where('registered_at', null))
            ->when($request->has('registered') && !empty($request->get('registered')),fn($q)=>$q->whereNotNull('registered_at'))
            ->latest()->get();

        return $this->successResponse(InvitationResource::collection($invitations));
    }
    public function deleteInvitation(Request $request,$id)
    {

        if (auth('api')->user()->cannot('invitations.delete')) {
            return $this->errorResponse('Unauthorized, you don\'t have access.');
        }


        $invitation = Invitation::findOrFail($id);
        if ($invitation->registered_at){
            return $this->errorResponse('Invitation Already Used',200);
        }
        $invitation->forceDelete();
        return $this->successResponse('success','Invitation Deleted Successfully');
    }
}

