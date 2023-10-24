<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\MainController;
use Illuminate\Http\Request;

class ManagementController extends MainController
{
    public function __construct()
    {
        parent::__construct() ;
        $this->class = 'management' ;
        $this->table = 'managements' ;
        $this->middleware('auth');
        $this->middleware('permission:managements.view', ['only' => ['index', 'show']]);
        $this->middleware('permission:managements.create', ['only' => ['create', 'store']]);
        $this->middleware('permission:managements.edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:managements.delete', ['only' => ['destroy']]);
    }
    public function index(Request $request) {

        return view('admin.managements.index')->with(['branch_id'=>$request->branch_id,'tree'=>$this->tree]);
    }



    public function create(Request $request) {
        $tree = array_merge($this->tree, [route('admin.managements.index') => 'managements']);

        return view('admin.managements.create')->with(['branch_id' => $request->branch_id,'tree'=>$tree]);
    }

    public function edit($management) {
        $tree = array_merge($this->tree, [route('admin.managements.index') => 'managements']);

        return view('admin.managements.edit')->with(['management'=>$management,'tree'=>$tree]);
    }
}
