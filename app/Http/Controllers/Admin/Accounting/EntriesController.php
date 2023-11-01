<?php

namespace App\Http\Controllers\Admin\Accounting;

use App\Http\Controllers\Controller;
use App\Http\Controllers\MainController;
use App\Models\Accounting\Entry;
use Illuminate\Http\Request;

class EntriesController extends MainController
{
    public function __construct()
    {
        parent::__construct();
        $this->class = "entries";
        $this->table = "entries";
        $this->middleware('auth');
        $this->middleware('permission:accounting.entries.view', ['only' => ['index', 'show']]);
        $this->middleware('permission:accounting.entries.create', ['only' => ['create', 'store']]);
        $this->middleware('permission:accounting.entries.edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:accounting.entries.delete', ['only' => ['destroy']]);
    }
    public function index(){
        $tree = $this->tree;
//        $entries = Entry::all()->get();
        return view('admin.accounting.entries.index', compact('tree'));
    }

    public function create()
    {
        $tree = array_merge($this->tree, [route('admin.accounting.entries.index') => 'entries']);
        return view('admin.accounting.entries.create', compact('tree'));
    }

    public function edit(string $id)
    {
        $tree = array_merge($this->tree, [route('admin.accounting.entries.index') => 'entries']);
        return view('admin.accounting.entries.create', compact('tree','id'));
    }

}
