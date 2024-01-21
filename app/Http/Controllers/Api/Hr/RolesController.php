<?php

namespace App\Http\Controllers\Api\Hr;

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Controller;
use App\Http\Requests\Hr\StoreRoleRequest;
use App\Http\Resources\RoleResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;
use App\Models\System\Role;

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

            $currentGroup[$part] = [
                'id' => $permission->id,
                'name' => $part,
            ];
        }

        if ($request->has("name")){
            $role = Role::with('permissions')->where('slug',$request->get('name'))->select('id','name')->first();
            $rolePermissions = $role->permissions->pluck('id')->toArray();
        }
        return $this->successResponse(['permissions'=>$groupedPermissions,'role'=>isset($role) ? $role :null ,'rolePermissions'=>$rolePermissions ?? null ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreRoleRequest $request)
    {
        //dd($request);
        if (auth('api')->user()->cannot('roles.create')) {
            return $this->errorResponse('Unauthorized, you don\'t have access.');
        }


        $permissions = collect($request->validated('permissions'))->where('checked',true)->pluck('id')->toArray() ;

        $role =Role::findOrCreate($request->get('name'),'web');
        $role->syncPermissions($permissions);

        return $this->successResponse($role,__('message.created',['model'=>__('Role')]));
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
    public function update(StoreRoleRequest $request, $slug)
    {

        $role = Role::findBySlug($slug);
        $role->name = $request->input('name');
        $role->slug = Str::slug($request->input('name'));
        $role->save();

        $permissions = collect($request->validated('permissions'))->where('checked',true)->pluck('id')->toArray() ;
        $role->syncPermissions($permissions);

        return $this->successResponse($role);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$slug)
    {
        $role = Role::findBySlug($slug,'web');
        if ($role->users()->exists()){
            return $this->errorResponse('Role Still Has Users',200);
        }

        $role->delete();
        return $this->successResponse(null,__('message.deleted',['model'=>__('Role')]));
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
