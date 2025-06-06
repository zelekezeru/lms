<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;

class RegistrarPortalController extends Controller
{
    public function index()
    {
        $user = new UserResource(
            request()->user()
        );

        return inertia('Registrar/Index');
    }
}
