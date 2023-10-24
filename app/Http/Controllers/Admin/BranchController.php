<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\MainController;
use App\Models\Hr\Branch;

class BranchController extends MainController
{
    public function __construct() {
        parent::__construct() ;
        $this->class = 'branch' ;
        $this->table = 'branches' ;
        $this->middleware('auth');
        $this->middleware('permission:branches.view', ['only' => ['index', 'show']]);
        $this->middleware('permission:branches.create', ['only' => ['create', 'store']]);
        $this->middleware('permission:branches.edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:branches.delete', ['only' => ['destroy']]);
    }

    public function index() {
        return view('admin.Branches.index')->with(['tree' => $this->tree]);
    }

    public function create() {
        $tree = array_merge($this->tree, [route('admin.branches.index') => 'branches']);
        return view('admin.Branches.edit')->with(['tree' => $tree]);
    }

    public function show($id){
        $tree = array_merge($this->tree, [route('admin.branches.index') => 'branches']);
        $branch = Branch::find($id);
//        dd(User::find(3)->can('update', Branch::find($id)));
        $this->authorize('view', $branch);
//        dd(auth()->user()->can('view',$branch));
        return view('admin.Branches.show')->with(['tree' => $tree]);
    }

    public function edit(int $id) {
        $tree = array_merge($this->tree, [route('admin.branches.index') => 'branches']);

        return view('admin.Branches.edit',compact('id'))->with(['tree' => $tree]);
    }



    public function destroy() {
        return view('admin.Branches.index');
    }


}
