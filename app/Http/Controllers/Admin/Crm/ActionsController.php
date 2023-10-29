<?php

namespace App\Http\Controllers\Admin\Crm;

use App\Http\Controllers\Controller;
use App\Http\Controllers\MainController;
use App\Models\Crm\Action;
use Illuminate\Http\Request;

class ActionsController extends MainController
{
    public function __construct()
    {
        parent::__construct();
        $this->class = "action";
        $this->table = "actions";
        $this->middleware('auth');
        $this->middleware('permission:clients.actions.view', ['only' => ['index', 'show']]);
        $this->middleware('permission:clients.actions.create', ['only' => ['create', 'store']]);
        $this->middleware('permission:clients.actions.edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:clients.actions.delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       $actions = Action::all();
       return view('admin.clients.actions.index',compact('actions'));
    }

}
