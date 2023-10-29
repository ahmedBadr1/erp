<?php

namespace App\Http\Controllers\Admin\Crm;

use App\Http\Controllers\Controller;
use App\Http\Controllers\MainController;
use App\Models\Accounting\Category;
use App\Models\Crm\Client;
use App\Models\System\Status;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ClientsController extends MainController
{
    public function __construct()
    {
        parent::__construct();
        $this->class = "client";
        $this->table = "clients";
        $this->middleware('auth');
        $this->middleware('permission:clients.view', ['only' => ['index', 'show']]);
        $this->middleware('permission:clients.create', ['only' => ['create', 'store']]);
        $this->middleware('permission:clients.edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:clients.delete', ['only' => ['destroy']]);
    }
    public function index(){
        $tree = $this->tree;
        return view('admin.clients.index', compact('tree'));
    }

    public function create()
    {
        $tree = array_merge($this->tree, [route('admin.clients.index') => 'clients']);
        return view('admin.clients.create', compact('tree'));
    }

    public function edit(string $id)
    {
        $tree = array_merge($this->tree, [route('admin.clients.index') => 'clients']);
        return view('admin.clients.create', compact('tree','id'));
    }
}
