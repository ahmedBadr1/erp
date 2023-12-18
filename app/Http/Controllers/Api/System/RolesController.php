<?php

namespace App\Http\Controllers\Api\System;

use App\Http\Controllers\Api\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;


use Spatie\Activitylog\Models\Activity;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolesController extends Controller {

    public function __construct()
    {
//        $this->middleware('permission:access_roles');
    }

    private static function permissionsGroups() {

        $permissions = Permission::select('id', 'name')->orderBy('name')->pluck('name', 'id')->toArray();

        $permissionsGroups = [];
        foreach ($permissions as $id => $name) {
            $friendly_name = ucwords(str_replace('_', ' ', $name));
            $sections = explode(' ', $friendly_name);
            $group_name = end($sections);
            $permissionsGroups[$group_name][$id] = $friendly_name;
        }
        $permissionsGroups = collect($permissionsGroups)->sortByDesc(function ($group) {
            return count($group);
        });

        return $permissionsGroups;
    }

    public function roles(Request $request) {

//        if (!can('access_roles')) return error(403);

        $roles = Role::select('id', 'name')->orderBy('name')->pluck('name', 'id')->toArray();

        $permissions = Permission::select('id', 'name')->orderBy('name')->pluck('name', 'id')->toArray();

        $permissionsGroups = self::permissionsGroups();

        return success(['roles' => $roles, 'permissions' => $permissions, 'permissions_groups' => $permissionsGroups]);
    }

    public function get(Request $request, Role $role) {

//        if (!can('show_roles')) return error(403);

        $permissions = $role->permissions()->pluck('name', 'id')->toArray();

        return success(['permissions' => $permissions]);
    }

    public function put(Request $request, Role $role = null) {

        if (! Gate::allows('edit_roles')) {
            abort(403);
        }

        $validator = Validator::make($request->all(), [
            "name" => "required|unique:roles" . (($role) ? ",id,$role->id" : ""),
        ]);

        if ($validator->fails())
            return vrrors($validator);

        $old = ($role) ? $role->getAttributes() : null;

        if(!$role) $role = new Role();

        $role->name = $request->name;
        $role->guard_name = 'api';
        $role->save();
        $role->refresh();
        $role->syncPermissions($request->permissions);
      //   Log::log(($old) ? 'role\edit' : 'role\add', $role, $old);

        Artisan::call('cache:clear');
        return success($role->getAttributes());
    }

    public function delete(Request $request, Role $role) {

        if (! Gate::allows('edit_roles')) {
            abort(403);
        }

        if ($role->users()->count() > 0) return error(1005);

        $old = ($role) ? $role->getAttributes() : null;

        $role->delete();

        Artisan::call('cache:clear');

      //  Activity::log('role/delete', $role, $old);

        return success();
    }

    public function user(Request $request, User $user) {

        if (! Gate::allows('show_roles')) {
            abort(403);
        }
        $roles = $user->roles()->pluck('name', 'id')->toArray();
        $rolePermissions = $user->getPermissionsViaRoles()->pluck('name', 'id')->toArray();
        $directPermissions = $user->getDirectPermissions()->pluck('name', 'id')->toArray();

//        //todo for new user access permissions
//        $user->setUserAccessAllPermissions();//to set by query
//        $userAccessPermissions = $user->getUserAccessAllPermissions();

        return success(['roles' => $roles, 'role_permissions' => $rolePermissions, 'direct_permissions' => $directPermissions]);
    }

    public function sync(Request $request, User $user) {

        if (! Gate::allows('edit_roles')) {
            abort(403);
        }
        $user->syncPermissions($request->permissions);
        $user->syncRoles($request->roles);

        Artisan::call('cache:clear');

      //  Log::log('role/sync', $user);

        return success();
    }
}
