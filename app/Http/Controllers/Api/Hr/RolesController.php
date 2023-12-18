<?php

namespace App\Http\Controllers\Api\Hr;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesController extends Controller
{
    public function __construct()
    {
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
        $roles = Role::orderBy('id','DESC')->paginate(10);

        return success($roles);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $permissions = Permission::getPermissions();
        return success($permissions);
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
        $this->validate($request,[
            'name'=>'required|unique:roles,name',
        ]);
        $input = $request->all();

        $role =Role::findOrCreate($request->input('name'));
        $role->syncPermissions($request->input('permissions'));
        return success($role);
    }

    /**
     * Display the specified resource.
     *
     * @param int|null $id
     *
     */
    public function show(Request $request, int $id= null)
    {
        $role = Role::findById($id,'web');
        $rolePermissions = Permission::whereHas('roles', fn($q) => $q->where('id',$id))->get();
        $res = [$role ,$rolePermissions ];
        return success($res);
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
        return success($res);
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

        return success($role);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {
        DB::table('roles')->where('id',$id)->delete();
        return success('','','deleted Successfully');
    }

    public function permissions(){
        $permissions = Permission::getPermissions();
        //$roles = Role::
        return success($permissions);
    }
    public function permissionsCreate(Request $request){
        $this->validate($request,[
            'name'=>'required|unique:permissions,name',
        ]);
        $input = $request->all();

        Permission::create(['name' => $input['name']]);
        return success($permission,'','Permission Created Successfully');
    }
    public function permissionsDelete(int $id)
    {
        $permission = Permission::findById($id);
        $permission->delete();
        return success('','','Permission Deleted Successfully');
    }
}
