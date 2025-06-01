<?php

namespace App\Http\Controllers;

use App\Http\Requests\CoordinatorStoreRequest;
use App\Http\Requests\CoordinatorUpdateRequest;
use App\Http\Resources\CoordinatorResource;
use App\Http\Resources\UserResource;
use App\Models\Coordinator;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Auth\RegisteredUserController;
use Carbon\Carbon;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

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
        
        $fullNameParts = explode(' ', $fields['name']);
        $firstName = $fullNameParts[0] ?? '';
        $middleName = $fullNameParts[1] ?? '';
        $lastName = $fullNameParts[2] ?? '';

        $email = strtolower(Str::slug($firstName) . '.' .  Str::slug($middleName)) . '@sits.edu.et';

        // 👤 Generate custom user_uuid
        $coordinatorsCount = str_pad(Coordinator::count() + 1, 4, '0', STR_PAD_LEFT);
        
        // This year last two digits
        $academicYear = substr(date('Y'), -2); // Get last two digits of the current year
        
        $userUuid = 'SITS-CO-' . str_pad($coordinatorsCount, 4, '0', STR_PAD_LEFT);
        
        $default_password = strtolower($firstName) . '@' . substr($fields['phone'], -4) ; // Default password for new users
        
        $data = [
            'name' => $fields['name'],
            'tenant_id' => 1, // Assuming tenant_id is static for now
            'user_uuid' => $userUuid,
            'email' => $email,
            'phone' => $fields['phone'],
            'profile_img' => $request->hasFile('profile_img')
                ? $request->file('profile_img')->store('profile_images')
                : null,
            'password' => Hash::make($default_password),
            'default_password' => $default_password,
        ];
        
        $user = User::create($data);

        $coordinator = Coordinator::create([
            'user_id' => $user->id,
            'center_id' => $fields['center_id'],
        ]);

        $user->assignRole('COORDINATOR');

        $center = $coordinator->center;

        return redirect()->route('centers.show', $coordinator->center)->with('success', 'Coordinator created successfully.');
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
