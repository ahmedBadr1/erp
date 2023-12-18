<?php

namespace App\Http\Controllers\Admin\Accounting;

use App\Http\Controllers\Controller;
use App\Http\Controllers\MainController;
use App\Models\Accounting\AccCategory;
use Illuminate\Http\Request;

class TransfersController extends MainController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
        $this->middleware('permission:accounting.posting', ['only' => ['posting']]);
        $this->middleware('permission:accounting.unposting', ['only' => ['unposting']]);
    }
    public function posting(){
        $tree = $this->tree ;
        return view('admin.accounting.posting', compact('tree'));
    }

    public function unposting()
    {
        $tree = $this->tree ;
        return view('admin.accounting.posting', compact('tree'));
    }

    public function edit(string $id)
    {
        $tree = array_merge($this->tree, [route('admin.accounting.accounts.index') => 'accounts']);
        return view('admin.accounting.accounts.create', compact('tree','id'));
    }
}
