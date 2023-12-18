<?php

namespace App\Http\Controllers\Api\Purchases;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PaymentsController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:purchases');
    }
}
