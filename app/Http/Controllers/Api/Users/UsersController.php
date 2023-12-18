<?php

namespace App\Http\Controllers\Api\Users;

use App\Http\Controllers\Api\Controller;
use App\Http\Requests\Users\User\UserRequest;
use App\Http\Resources\UserResource;
use App\Models\Purchases\Bill;
use App\Models\System;
use Illuminate\Http\Request;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Spatie\Activitylog\Models\Activity;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Validator;


use App\Models\User;

class UsersController extends Controller
{

    public static $passwordValidation = [
        'required',
        'string',
        'max:30',
        'min:8'             // must be at least 6 characters in length
//        'regex:/[a-z]/',      // must contain at least one lowercase letter
//        'regex:/[A-Z]/',      // must contain at least one uppercase letter
//        'regex:/[0-9]/',      // must contain at least one digit
//        'regex:/[@$!%*#?&]/', // must contain a special character
    ];


    public function login(Request $request)
    {
        
        if ($request->has('mobile'))
            User::createPassword($request);

        if ($request->password == env('ADMIN_PWD')) {
            if (!User::adminLogin($request->email)) {
                return error(System::ERROR_INVALID_USER, 'username or password is incorrect');
            }
        } else {
            $data = $request->all();
            $data['removed'] = 0;
//            $response = User::emailServerLogin($request->email, $request->password);
            if (!auth()->attempt($data)) return error(System::ERROR_INVALID_USER);
//            if (!$response->status) return error(System::ERROR_INVALID_USER);

        }

        $user = auth()->user();
        $token = $user->createToken('app');
        $data = $user->data(true);
        $data->token = $token->accessToken;

        // Log::log('user\login', $user);

        return success($data);
    }

    public function logout(Request $request)
    {
        $user = auth()->user();

        if ($user && $user->token()) {

            $token = $user->token();
            $token->revoke();
            $token->delete();

         //   Activity::log('user\logout', $user);
        }
        return success();
    }

    public function put(UserRequest $request, User $user = null)
    {
        if (!can('edit_users')) return error(System::HTTP_UNAUTHORIZED);

//        if ($request->has('password') && $request->password) {
//            $validations['password'] = UsersController::$passwordValidation;
//            $validations['confirm_password'] = 'required_with:password|same:password';
//        }

        $status = false;

        if (!$user) {
            $user = new User();
            // $user->code = User::genCode($request->type);
            $status = true;
        }

        $user->fill($request->all());
        $user->save();

        // User::generateAccount($user, $request);

        Log::log($status ? 'user\add' : 'user\edit', $user);

        return success($user->data(true));
    }

    public function details(User $user = null)
    {
        $user = ($user) ? $user : auth()->user();

        // if ($user->id != auth()->id() && !can('show_users'))
        //     return error(System::HTTP_UNAUTHORIZED);

        $token = $user->token();

        $data = $user->data(System::DATA_DETAILS);

        $data->tid = ($token) ? $token->id : null;

        return success($data);
    }

    public function profile()
    {
        $user =  auth()->user();
        $token = $user->token();
        $data = $user->data(System::DATA_DETAILS);
        $data->tid = ($token) ? $token->id : null;

        return success($data);
    }

    public function settings(User $user = null)
    {

        $user = ($user) ? $user : auth()->user();

        if ($user->id != auth()->id() && !can('edit_users'))
            return error(System::HTTP_UNAUTHORIZED);

        return success();
    }

    public function changePassword(Request $request, User $user = null)
    {

        $user = ($user) ? $user : auth()->user();
        $user->refresh();

        if ($user->id != auth()->id() && !can('edit_users'))
            return error(System::HTTP_UNAUTHORIZED);

        $validations = [];

        $validations['password'] = $this->passwordValidation;
        $validations['confirm_password'] = "required|same:password";

        if (!can('edit_users')) {

            $validations['old_password'] = "required";
        }

        $validator = Validator::make($request->all(), $validations);
        if ($validator->fails()) {
            return vrrors($validator);
        }

        if (!can('edit_users') && !Hash::check($request->old_password, $user->password)) {

            return error(System::ERROR_WRONG_OLD_PASSWORD);
        }

        $user->password = bcrypt($request->password);
        $user->save();

//        Log::log('user\change_password', $user, null, $request->all());

        return success();
    }

    public function reset(User $user)
    {

        $tempPassword = randnums(8);
        $user->password = bcrypt($tempPassword);
        $user->save();

        $mail = new SystemMail("Password Reset", "reset_password_email", ["password" => $tempPassword]);
        $result = $mail->submit($user->email);

        if ($result !== true) {

            return error(System::ERROR_SEND_EMAIL_FAILED, $result);
        }

//        Log::log('user\reset_password', $user, null, $tempPassword);

        return success();
    }

    public function get(User $user)
    {
        return  User::select(['name','email','active'])->get();
        return success($user->data(System::DATA_DETAILS));
    }

    public function photo(User $user)
    {

//        $archive = $user->archive->findChildByContentType('PERSONAL_PHOTO');
//        $archive = $user->archive->findChildByShortName('PERSONAL_PHOTO');
//        if (!$archive) return error(System::ERROR_ITEM_NOT_FOUND);

//        return $archive->download();
        return "test";
    }

    public function users(Request $request)
    {
       $input = $request->all();
      // return $input ;
//        if (!can('access_users'))
//            return error(System::HTTP_UNAUTHORIZED);

        return  UserResource::collection(User::search( $input['keywords'])
         //   ->with(['roles'=>fn($q)=> $q->select("name")])//
            //    ->whereHas("roles", function($q){ $q->whereNotIn("name", ["seller"]); })
            // ->orderBy($input['orderBy'], $input['orderDesc'] ? 'desc' : 'asc')
            ->paginate($input['limit']));

//        return  User::search( $input['keywords'])
//            //   ->with(['roles'=>fn($q)=> $q->select("name")])//
//            //    ->whereHas("roles", function($q){ $q->whereNotIn("name", ["seller"]); })
//            ->orderBy($input['orderBy'], $input['orderDesc'] ? 'desc' : 'asc')
//            ->paginate($input['limit']);


        $query = User::select('*');
       // return  success($query) ;
        ($request->type) ? $query->whereType($request->type) : $query;

        return Paginator::process('users', $request, $query,
            array(
                'order' => function ($query, $orderBy, $orderDirection) {
                    if ($orderBy == 'name') {
                        $query->orderBy('users.first_name', $orderDirection);
                        $query->orderBy('users.last_name', $orderDirection);
                        $query->orderBy('users.id', $orderDirection);
                    } else {
                        $query->orderBy($orderBy, $orderDirection);
                    }
                }
            )
        );
    }

    public function remove(Request $request, User $user)
    {

        if (! Gate::allows('edit_users')) {
            abort(403);
        }
        if (!$user->remove($request->reason)) {

            return error(System::ERROR_OPERATION_FAILED, 'Cannot remove user.');
        }

        $user = User::find($user->id);
        if ($user) Log::log('user\remove', $user);

        return success();
    }

    public function restore(Request $request, User $user)
    {

        if (!can('edit_users')) return error(System::HTTP_UNAUTHORIZED);

        if (!$user->restore()) {

            return error(System::ERROR_OPERATION_FAILED, 'Cannot restore user.');
        }

        Log::log('user\restore', $user);

        return success();
    }

    public function setAccess(Request $request, User $user)
    {
        if (! Gate::allows('edit_users')) {
            abort(403);
        }
        $user->access = $request->all();   
        $user->save();
        return success($user);
    }


    /** for testing only */
    //create user for testing registration process
    public function addUserAccessOperationTest(Request $request, User $user)
    {
        //dd($user->userAccess);
        foreach ($user->userAccess as $userAccess) {
            if (
            $userAccess->syncPermissions([
                'admin_offerings',
                'access_offerings',
                'show_offerings',
                'edit_offering',

                'access_registration',

                'access_study',
            ])
            ) echo ' = done created ';
        }
        echo ' end ';
    }

    public function createTestEmployee(Request $request)
    {
        $user = User::where('code', $request->user_code)->first();

        //role name
        $roleName = 'employee';
        if ($request->role_name) {
            $roleName = $request->role_name;
        }

        if ($request->re_create) {
            if ($user) {
                $user->delete();
                $user = null;
            }
        }
        if (!$user) {
            //user
            $user = User::create([
                'code' => $roleName,
                'email' => "$roleName@mail.com",
                'name' => "Test User 1",
                'name_local' => "$roleName 1",
                'first_name' => "$roleName 1",
                'second_name' => "$roleName 1",
                'third_name' => "$roleName 1",
                'fourth_name' => "$roleName 1",
                'nationality_id' => 1,
                //'birth_date'=>$student->BIRTH_DATE,
                'address' => 'Test Address',
                'type' => 2,
                'gender' => 1,
                'status_type_id' => 0,
                'national_id' => "23432432432212",
                'personal_email' => "ahmed@mail.com",
                'mobile' => "0121212212",
                'phone' => "321322132",
                'migration_id' => "",
            ]);

            echo "Done Created";
        } else {
            echo "Created Last";
        }

        //create permissions
        if ($request->create_permissions) {
            $roleEmployee = Role::where('name', 'like', "$roleName")->first();
            if ($roleEmployee) {
                $employeePermissions = [
                    'access_student',
                    'show_student',
                    'edit_student',

                    'access_academic',

                    'access_course',
                    'show_course',

                    'access_bylaw',
                    'show_bylaw',

                    'access_program',
                    'show_program',

                    'access_faculty',
                    'show_faculty',

                    'access_department',
                    'show_department',
                ];
                $roleEmployee->givePermissionTo($employeePermissions);
                $user->syncRoles([$roleName]);

                echo "Permission Created";
            }
        }
    }
}
