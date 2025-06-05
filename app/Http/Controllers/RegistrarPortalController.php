<?php

namespace App\Http\Controllers;

use App\Http\Resources\EmployeeResource;
use Illuminate\Http\Request;

class RegistrarPortalController extends Controller
{
    public function index()
    {
        $employee = new EmployeeResource(
            request()->user()->employee
        );
    }
}
