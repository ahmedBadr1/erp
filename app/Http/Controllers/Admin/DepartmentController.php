<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\MainController;
use App\Models\Hr\Department;
use App\Models\Hr\Management;
use Illuminate\Http\Request;

class DepartmentController extends MainController
{
    public function __construct()
    {
        parent::__construct() ;
        $this->class = 'department' ;
        $this->table = 'departments' ;
        $this->middleware('auth');
        $this->middleware('permission:departments.view', ['only' => ['index', 'show']]);
        $this->middleware('permission:departments.create', ['only' => ['create', 'store']]);
        $this->middleware('permission:departments.edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:departments.delete', ['only' => ['destroy']]);
    }

    public function index(Request $request) {
        return view('admin.departments.index')->with(['management_id' => $request->management_id,'tree'=>$this->tree]);
    }

    public function create(Request $request) {
        $tree = $this->tree;
//        $tree = array_merge($tree, [route('admin.branches.index') => 'branches']);
//        $management = Management::whereId($request->management_id)->value('name') ;
//        if($management){
//            $tree = array_merge($tree, [route('admin.managements.edit',$request->management_id) => 'management']);
//        }
        $tree = array_merge($tree, [route('admin.departments.index') => 'departments']);

        return view('admin.departments.create')->with(['management_id' =>$request->management_id,'tree'=>$tree]);
    }


    public function edit($department) {
        $tree = array_merge($this->tree, [route('admin.departments.index') => 'departments']);

        $management_id = Department::whereId($department)->first()->management_id;
        return view('admin.departments.edit')->with(
           [
             'department_id' => $department,
             'management_id' => $management_id,
               'tree' => $tree
           ]
        );
    }
}
