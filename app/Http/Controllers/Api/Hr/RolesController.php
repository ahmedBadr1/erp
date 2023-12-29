<?php

namespace App\Http\Controllers\Api\Hr;

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Controller;
use App\Http\Resources\RoleResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesController extends ApiController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('auth:api');
        $this->middleware('permission:hr');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $roles = Role::withCount('permissions')->orderBy('id','DESC')->paginate(10);

        return $this->successResponse(RoleResource::collection($roles));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
      $permissions = Permission::select('name',"id")->get();
        $groupedPermissions = [];

        foreach ($permissions as $permission) {
            $parts = explode('.', $permission->name);
            $currentGroup = &$groupedPermissions;

            // Exclude the last part (e.g., "index")
            $numParts = count($parts) - 1;

            foreach ($parts as $key => $part) {
                if ($key === $numParts) {
                    // Exclude the last part
                    break;
                }

                if (!isset($currentGroup[$part])) {
                    $currentGroup[$part] = [];
                }

                $currentGroup = &$currentGroup[$part];
            }

            $currentGroup[] = [
                'id' => $permission->id,
                'name' => $part,
            ];
        }

        if ($request->has("name")){
            $role = Role::with('permissions')->where('name',$request->get('name'))->first();
        }
        return $this->successResponse(['permissions'=>$groupedPermissions,'role'=>isset($role) ? new RoleResource($role) :null ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        //dd($request);
        if (auth('api')->user()->cannot('roles.create')) {
            return $this->errorResponse('Unauthorized, you don\'t have access.');
        }

        $this->validate($request,[
            'name'=>'required|unique:roles,name',
            'permissions'=>'required|array',
            'permissions.*'=>'required|exists:permissions,id',
        ]);
        $input = $request->all();

        $role =Role::findOrCreate($request->input('name'));
        $role->syncPermissions($request->input('permissions'));
        return $this->successResponse($role);
    }

    /**
     * Display the specified resource.
     *
     * @param int|null $id
     *
     */
    public function show(Request $request, int $id= null)
    {
        $role = Role::with(['permissions'=>fn($q)=>$q->select('id','name')])->withCount('permissions')->where('id',$id)->where('guard_name','web')->first();
        return $this->successResponse(new RoleResource ($role));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request,$id)
    {
        //
        $role = Role::findOrFail($id);
        $permissions = Permission::with('roles')->get();

        //  $rolePermissions =DB::table("role_has_permissions")->where('role_has_permissions.role_id',$id)->pluck('role_has_permissions.permission_id','role_has_permissions.permission_id')->all();
        $rolePermissions = Permission::whereHas('roles', fn($q) => $q->where('id',$id))->pluck('id')->toArray();
        $res = [$role , $permissions , $rolePermissions];
        return $this->successResponse($res);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     *
     */
    public function update(Request $request, $id)
    {
        //
        $this->validate($request,[
            'name'=>['required', Rule::unique('roles')->ignore($id),]
        ]);
        $role = Role::find($id);

        $role->name =$request->input('name');
        $role->save();
        $role->syncPermissions($request->input('permissions'));

        return $this->successResponse($role);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$name)
    {
        $role = Role::findByName($name,'web');
        if ($role->permissions()->exists()){
            return $this->errorResponse('Role Still Has Permissions',200);
        }
        if ($role->users()->exists()){
            return $this->errorResponse('Role Still Has Users',200);
        }

        $role->delete();
        return $this->successResponse(null,'deleted Successfully');
    }

    public function permissions(){
        $permissions = Permission::select('name',"id")->get();
        return $this->successResponse($permissions);
    }
    public function permissionsCreate(Request $request){
        $this->validate($request,[
            'name'=>'required|unique:permissions,name',
        ]);
        $input = $request->all();

        Permission::create(['name' => $input['name']]);
        return $this->successResponse(null,'Permission Created Successfully');
    }
    public function permissionsDelete(int $id)
    {
        $permission = Permission::findById($id);
        $permission->delete();
        return $this->successResponse(null,'Permission Deleted Successfully');
    }
}
