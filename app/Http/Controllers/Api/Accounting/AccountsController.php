<?php

namespace App\Http\Controllers\Api\Accounting;

use App\Http\Controllers\Controller;
use App\Models\Accounting\AccCategory;
use Illuminate\Http\Request;

class AccountsController extends Controller
{
    public function tree()
    {
        $tree = AccCategory::account()->tree()->with('accounts')->get()->toTree();
//        $tree = Category::with('accounts' )->get();
        return $this->successResponse($tree);
    }
}
