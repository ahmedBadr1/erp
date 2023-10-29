<?php

namespace App\Http\Controllers\Admin\Sales;

use App\Http\Controllers\Controller;
use App\Http\Controllers\MainController;
use Illuminate\Http\Request;

class ReportsController extends MainController
{
    public function __construct()
    {
        parent::__construct();
        $this->class = "reports";
        $this->middleware('auth');
        $this->middleware('permission:sales.reports', ['only' => ['index', 'show']]);
    }

    public function index(){
        $tree = $this->tree;
        return view('admin.sales.reports', compact('tree'));
    }


}
