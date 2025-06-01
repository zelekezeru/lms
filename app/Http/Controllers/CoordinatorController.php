<?php

namespace App\Http\Controllers;

use App\Http\Requests\CoordinatorStoreRequest;
use App\Http\Requests\CoordinatorUpdateRequest;
use App\Http\Resources\CoordinatorResource;
use App\Http\Resources\UserResource;
use App\Models\Coordinator;
use App\Models\User;
use Illuminate\Http\Request;

class CoordinatorController extends Controller
{
    public function index(Request $request)
    {
        $query = Coordinator::with('user');

        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $coordinators = CoordinatorResource::collection($query->paginate(15));

        return inertia('Coordinators/Index', [
            'coordinators' => $coordinators,
        ]);
    }

    public function show(Coordinator $coordinator)
    {
        return inertia('Coordinators/Show', [
            'coordinator' => new CoordinatorResource($coordinator->load('user')),
            'users' => UserResource::collection(User::all()),
        ]);
    }

    public function create()
    {
        return inertia('Coordinators/Create', [
            'users' => UserResource::collection(User::all()),
        ]);
    }

    public function store(CoordinatorStoreRequest $request)
    {
        $fields = $request->validated();

        $user = User::find($fields['user_id']);
        $user->assignRole('COORDINATOR');

        $coordinator = Coordinator::create($fields);

        return redirect()->route('coordinators.show', $coordinator)->with('success', 'Coordinator created successfully.');
    }

    public function edit(Coordinator $coordinator)
    {
        return inertia('Coordinators/Edit', [
            'coordinator' => new CoordinatorResource($coordinator),
            'users' => UserResource::collection(User::all()),
        ]);
    }

    public function update(CoordinatorUpdateRequest $request, Coordinator $coordinator)
    {
        $fields = $request->validated();

        $coordinator->update($fields);

        $user = User::find($fields['user_id']);
        if ($user && !$user->hasRole('COORDINATOR')) {
            $user->assignRole('COORDINATOR');
        }

        return redirect()->route('coordinators.show', $coordinator)->with('success', 'Coordinator updated successfully.');
    }

    public function destroy(Coordinator $coordinator)
    {
        $coordinator->delete();

        return redirect()->route('coordinators.index')->with('success', 'Coordinator deleted successfully.');
    }
}
