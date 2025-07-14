<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FinancePortalController extends Controller
{
    public function index()
    {
        dd(request()->user()->getAllPermissions());
        return inertia('FinancePortal/Index');
    }
}
