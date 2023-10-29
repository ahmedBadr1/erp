<?php

namespace App\Http\Controllers\Admin\Purchases;

use App\Http\Controllers\Controller;
use App\Http\Controllers\MainController;
use App\Models\Purchases\Bill;
use Illuminate\Http\Request;

class ReportsController extends MainController
{
    public function __construct()
    {
        parent::__construct();
        $this->class = "reports";
        $this->middleware('auth');
        $this->middleware('permission:purchases.reports', ['only' => ['index', 'show']]);
    }

    public function index(){
        $tree = $this->tree;
        return view('admin.purchases.reports', compact('tree'));
    }


}
