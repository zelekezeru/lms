<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\Section;
use App\Http\Resources\SectionResource;
use App\Models\Student;
use App\Http\Resources\StudentResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\DepartmentResource;
use App\Http\Resources\ProgramResource;

class ProfileController extends Controller
{
    // Student Verification

    

    //Student goto Profile
    public function profile(Student $student)
    {   
        $sections = SectionResource::collection(Section::where('department_id', $student->department_id)->get());
        
        return inertia('Students/Profile', [
            'student' => new StudentResource($student),
            'user' => new UserResource($student->user),
            'sections' => $sections,
            'department' => new DepartmentResource($student->department),
            'program' => new ProgramResource($student->program),
        ]);

    }
    //Student Profile Image and Status
    public function updateProfile(Request $request, Student $student)
    {
        // Validate the request
        $fields = $request->validate([
            'profile_img' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'payment_status' => 'nullable|boolean',
            'enroll' => 'nullable|boolean',
            'document_submitted' => 'nullable|boolean',
            'section_id' => 'nullable|exists:sections,id',
        ]);

        if ($request->hasFile('profile_img')) {
            $imagePath = $request->file('profile_img')->store('profile_images', 'public');
            $student->user->update(['profile_img' => $imagePath]);
        }

        // Enroll Student to Section Courses
        if ($request->input('section_id')) {
            $section = Section::find($request->input('section_id'));

            if ($section) {
                // Detach from previous sections
                $student->sections()->sync([$section->id]); // sync replaces all existing with this one
            
                // Attach section courses to student, avoid duplicates
                foreach ($section->courses as $course) {
                    if (!$student->courses->contains($course->id)) {
                        $student->courses()->attach($course->id);
                    }
                }
            }
            
        }

        // Payment Status Handling
        if ($request->input('payment_status') == '1') {
            $student['is_verified']   = 1;
            $student['is_approved']   = 1;
            $student['is_active']     = 1;
            $student['is_enrolled']   = 1; // initial value, may get overwritten below
            $student['approved_by']   = Auth::id();
            $student['approved_at']   = now(); // use Carbon object, not string
        } else {
            $student['is_verified']   = 0;
            $student['is_approved']   = 0;
            $student['is_active']     = 0;
            $student['is_enrolled']   = 0;
        }

        // Document Submission Handling
        if ($request->input('document_submitted') == '1') {
            $student['is_completed']  = 1;
            $student['completed_by']  = Auth::id();
            $student['completed_at']  = now();
        } else {
            $student['is_completed']  = 0;
        }

        // Enroll Override (takes precedence if present)
        if ($request->has('enroll')) {
            $student['is_enrolled'] = $request->input('enroll') == '1' ? 1 : 0;
        }
        if ($request->has('section_id')) {
            $student['section_id'] = $request->input('section_id');
        }
        
        $student->update([
            'is_verified' => $student->is_verified,
            'is_approved' => $student->is_approved,
            'approved_by' => $student->approved_by,
            'approved_at' => $student->approved_at,
            'is_active' => $student->is_active,
            'is_enrolled' => $student->is_enrolled,
            'is_completed' => $student->is_completed,
            'completed_by' => $student->completed_by,
            'completed_at' => $student->completed_at,
        ]);
        
        return redirect()->route('students.show', $student)->with('success', 'Profile image updated successfully.');
    }
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
