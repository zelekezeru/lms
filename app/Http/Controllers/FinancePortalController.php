<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FinancePortalController extends Controller
{
    public function index()
    {
        return inertia('FinancePortal/Index');
    }
}
