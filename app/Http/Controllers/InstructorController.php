<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Requests\InstructorStoreRequest;
use App\Http\Requests\InstructorUpdateRequest;
use App\Http\Resources\CourseResource;
use App\Http\Resources\InstructorResource;
use App\Models\Course;
use App\Models\Instructor;
use App\Models\Track;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;

class InstructorController extends Controller
{
    public function index(Request $request)
    {
        $query = Instructor::with('user');

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;

            // Adjusted for correct column names
            $query->where('specialization', 'LIKE', "%{$search}%")
                ->orWhere('employment_type', 'LIKE', "%{$search}%");
        }

        // Move orderBy('name') before paginate()
        $instructors = InstructorResource::collection(
            $query->join('users', 'instructors.user_id', '=', 'users.id')
                ->orderBy('users.name')
                ->select('instructors.*')
                ->paginate(15)
                ->withQueryString()
        );

        return inertia('Instructors/Index', [
            'instructors' => $instructors,
            'search' => $request->search,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Instructor $instructor)
    {
        $instructor = new InstructorResource($instructor->load([
            'user',
            'courses',
            'courseSectionAssignments.section.courseSectionAssignments' => function ($q) use ($instructor) {
                $q->where('instructor_id', $instructor->id);
            },
        ]));

        $courses = CourseResource::collection(Course::withExists(['instructors as related_to_instructor' => function ($query) use ($instructor) {
            return $query->where('instructors.id', $instructor->id);
        }])->orderBy('name')->orderByDesc('related_to_instructor', 'name')->get());

        return Inertia::render('Instructors/Show', [
            'instructor' => $instructor,
            'courses' => $courses,
        ]);
    }

    public function create(): Response
    {
        $instructors = InstructorResource::collection(Instructor::all());
        $roles = Role::all();
        $courses = CourseResource::collection(Course::all());

        return Inertia::render('Instructors/Create', [
            'tracks' => Track::all(['id', 'name']),
            'roles' => $roles,
            'courses' => $courses,
        ]);
    }

    public function store(InstructorStoreRequest $request)
    {
        $fields = $request->validated();

        $courses = $fields['courses'] ?? [];
        $profileImg = $fields['profile_img'] ?? null;

        // Profile   of Instructor
        if ($profileImg) {
            $profile_path = $profileImg->store('profile-images', 'public');
        } else {
            $profile_path = null;
        }
        
        // user default password
        $firstName = explode(' ', $fields['name'])[0]; // Get the first name from the full name
        
        $user_phone = substr($fields['contact_phone'], -4);

        $default_password = strtolower($firstName).'@'.$user_phone; // Default password for new users
        
        // Merge the default password into the request
        $request->merge([
            'password' => $default_password,
            'default_password' => $default_password, // needed for 'confirmed' rule
            'profile_img' => $profile_path,
        ]);

        $instructor = Instructor::create([
            'name' => $fields['name'],

            // User id temporary
            'user_id' => 3,
            'specialization' => $fields['specialization'],
            'employment_type' => $fields['employment_type'],
            'bio' => $fields['bio'],
            'status' => $fields['status'],
            'hire_date' => $fields['hire_date'],
        ]);

        // Assign the created instructor to the courses
        $instructor->courses()->sync($courses);

        $registeredUserController = new RegisteredUserController;

        $user = $registeredUserController->store($request, 'INSTRUCTOR', 'User', $instructor);

        return redirect(route('instructors.show', $instructor))->with('success', 'Instructor created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Instructor $instructor)
    {
        $roles = Role::all();
        $courses = CourseResource::collection(Course::withExists(['instructors as related_to_instructor' => function ($query) use ($instructor) {
            return $query->where('instructors.id', $instructor->id);
        }])->orderByDesc('related_to_instructor', 'name')->get());

        return inertia('Instructors/Edit', [
            'instructor' => new InstructorResource($instructor->load('user', 'track', 'courses')),
            'tracks' => Track::all(['id', 'name']),
            'roles' => $roles,
            'courses' => $courses,
        ]);
    }

    public function update(InstructorUpdateRequest $request, Instructor $instructor)
    {
        $fields = $request->validated();
        $courses = $fields['courses'] ?? [];
        $profileImg = $fields['profile_img'] ?? null;
        $user = $instructor->user;

        // Handle profile image update
        if ($profileImg) {
            if ($user->profile_img) {
                Storage::disk('public')->delete($user->profile_img);
            }
            $profile_path = $profileImg->store('profile-images', 'public');
        }

        // Update user details
        $user->update([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'profile_img' => $profile_path ?? $user->profile_img,
            'phone' => $fields['contact_phone'],
        ]);

        // Update instructor details
        $instructor->update([
            'specialization' => $fields['specialization'],
            'employment_type' => $fields['employment_type'],
            'hire_date' => $fields['hire_date'],
            'status' => $fields['status'],
            'bio' => $fields['bio'],
        ]);

        $instructor->courses()->sync($courses);
        // Update roles if provided
        if (! empty($fields['role_name'])) {
            $user->syncRoles([$fields['role_name']]);
        }

        return redirect(route('instructors.show', $instructor))->with('success', 'Instructor updated successfully.');
    }

    public function destroy(Instructor $instructor)
    {
        $user = $instructor->user;
        if ($user->profile_img) {
            Storage::disk('public')->delete($user->profile_img);
        }

        $instructor->delete();

        $user->delete();

        return to_route('instructors.index', $instructor)->with('success', 'Instructor deleted successfully.');
    }
}
