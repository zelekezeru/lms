<?php

namespace App\Http\Controllers;

use App\Http\Requests\CenterStoreRequest;
use App\Http\Requests\CenterUpdateRequest;
use App\Http\Resources\CenterResource;
use App\Http\Resources\CoordinatorResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\StudentResource;
use App\Models\Center;
use App\Models\CenterCourse;
use App\Models\Course;
use App\Models\Program;
use App\Models\Section;
use App\Models\User;
use App\Models\Student;
use App\Models\StudyMode;
use App\Models\Track;
use Illuminate\Http\Request;

class CenterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Center::with('coordinator');

        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhereHas('coordinator', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $centers = CenterResource::collection(
            $query->with(['coordinator.user', 'students'])
                ->orderBy('name')->paginate(30)->withQueryString()
        );

        return inertia('Centers/Index', [
            'centers' => $centers,
        ]);
    }

    public function show(Center $center)
    {
        $center = new CenterResource($center);

        $students = StudentResource::collection(
            $center->students()
                ->with(['user', 'program', 'section'])
                ->orderBy('first_name')
                ->orderBy('middle_name')
                ->paginate(50)
        );
        
        // Get courses from students who have grades in those courses
        $courses = CenterCourse::where('center_id', $center->id)
            ->with('course')
            ->get()
            ->pluck('course')
            ->unique('id')
            ->values()
            ->map(function ($course) use ($center) {
            // Count grades for this course from all students in the center
            $gradeCount = $center->students
                ->flatMap(function ($student) use ($course) {
                return $student->grades->where('course_id', $course->id);
                })
                ->count();
            $course->grade_count = $gradeCount;
            return $course;
            });
        
        $allCourses = $center->students
            ->flatMap(function ($student) {
            return $student->track ? $student->track->courses : collect();
            })
            ->unique('id')
            ->values();

        // Courses available for result import at this center: prefer the courses
        // assigned to the center, otherwise fall back to the courses in the
        // center students' curriculum tracks.
        $importableCourses = $courses->isNotEmpty() ? $courses->values() : $allCourses;

        if ($center->coordinator) {
            $coordinator = new CoordinatorResource($center->coordinator->load('user'));
        } else {
            $coordinator = null;
        }

        return inertia('Centers/Show', [
            'center' => $center,
            'coordinator' => $coordinator,
            'students' => $students,
            'courses' => $courses,
            'allCourses' => $allCourses,
            'importableCourses' => $importableCourses,
        ]);
    }

    public function create()
    {
        return inertia('Centers/Create', [
            'users' => UserResource::collection(User::all()),
        ]);
    }

    public function store(CenterStoreRequest $request)
    {
        $fields = $request->validated();
        
        // Generate Center Code
        $lastCenter = Center::orderBy('created_at', 'desc')->first();
        if ($lastCenter) {
            $lastCenterId = $lastCenter->id;
        } else {
            $lastCenterId = 0;
        }

        $fields['code'] = 'SITS-C-' . str_pad($lastCenterId + 1, 3, '0', STR_PAD_LEFT);

        $center = Center::updateOrCreate($fields);

        return redirect()->route('centers.show', $center)->with('success', 'Center created successfully.');
    }

    public function edit(Center $center)
    {
        return inertia('Centers/Edit', [
            'center' => new CenterResource($center),
            'users' => UserResource::collection(User::all()),
        ]);
    }

    public function update(CenterUpdateRequest $request, Center $center)
    {
        $fields = $request->validated();

        $center->update($fields);

        return redirect()->route('centers.show', $center)->with('success', 'Center updated successfully.');
    }

    public function destroy(Center $center)
    {
        // Check if the center has associated students
        if ($center->students()->count() > 0) {
            return redirect()->route('centers.index')->with('error', 'Cannot delete center with associated students.');
        } elseif ($center->coordinator) {
            // Check if the center has an associated coordinator
            return redirect()->route('centers.index')->with('error', 'Cannot delete center with an associated coordinator.');
        } else {
            // No students and no coordinator at this point — safe to delete.
            $center->delete();

            return redirect()->route('centers.index')->with('success', 'Center deleted successfully.');
        }
    }

    public function distanceHome()
    {
        $centers = CenterResource::collection(
            Center::with(['coordinator.user', 'students'])
                ->orderBy('name')
                ->get()
        );

        $totalStudents = $centers->flatMap(function ($center) {
            return $center->students;
        })->unique('id')->count();

        $totalCoordinators = $centers->filter(function ($center) {
            return $center->coordinator !== null;
        })->count();

        return inertia('Centers/DistanceHome', [
            'centers' => $centers,
            'totalStudents' => $totalStudents,
            'totalCoordinators' => $totalCoordinators,
            // Id of the DISTANCE study mode, so the dashboard can deep-link to the
            // filtered student directory (students.index?study_mode=<id>).
            'distanceStudyModeId' => StudyMode::where('name', 'DISTANCE')->value('id'),
        ]);
    }
}
