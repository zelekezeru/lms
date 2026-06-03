<?php

namespace App\Http\Controllers;

use App\Http\Resources\ClassScheduleResource;
use App\Http\Resources\ClassSessionResource;
use App\Http\Resources\EnrollmentResource;
use App\Http\Resources\GradeResource;
use App\Http\Resources\SemesterResource;
use App\Http\Resources\StudentResource;
use App\Http\Resources\WeightResource;
use App\Models\Enrollment;
use App\Models\Semester;
use App\Models\StudyMode;
use App\Models\Weight;
use Inertia\Inertia;

class StudentPortalController extends Controller
{
    public function index()
    {
        $student = new StudentResource(request()->user()->student->load(
            [
                'user',
                'program',
                'track',
                'section',
                'grades.course',
                'grades.semester',
                'enrollments' => function ($q) {
                    $q->where('status', 'enrolled')->where('academic_status', 'in_progress');
                },
                'enrollments.courseOffering',
                'enrollments.courseOffering.section.track',
                'enrollments.courseOffering.section.studyMode',
                'payments.paymentMethod',
                'payments.paymentType',
                'payments.semester'
            ]
        ));

        // dd($student->grades[0]->course);
        return inertia('StudentPortal/Dashboard', [
            'student' => $student,
        ]);
    }

    public function enrollments()
    {
        $student = new StudentResource(request()->user()->student->load([
            'enrollments',
            'enrollments.courseOffering',
            'grades',
        ]));

        return inertia('StudentPortal/Enrollments/Index', [
            'student' => $student,
        ]);
    }

    // Transcript of student
    public function transcripts()
        {
            // 1. Eager Load all necessary student attributes for the transcript header and calculations.
            // Added 'year', 'semester', 'studyMode', 'user', and 'enrollments' based on the reference method.
            $studentModel = request()->user()->student->load([
                'user', // Needed for student's personal info
                'program',
                'track',
                'year', // Student's start year
                'semester', // Student's current semester (optional but good for context)
                'section',
                'studyMode', // Crucial for activeSemester logic and transcript header
                'results',
                'grades', // Loaded separately below, but good for completeness
            ]);
            
            // Check if studentModel exists before proceeding
            if (!$studentModel) {
                // Handle case where the user is not associated with a student model (e.g., return 404 or redirect)
                return abort(404, 'Student record not found.');
            }

            $student = new StudentResource($studentModel);

            // Fetch active semester and year/semester levels
            $activeSemester = $studentModel->studyMode ? $studentModel->studyMode->activeSemester() : null;
            $yearLevel = ($activeSemester && $studentModel->year)
                ? intval($activeSemester->year->name) - intval($studentModel->year->name) + 1
                : null;
            $semesterLevel = ($studentModel->section && $studentModel->section->semester)
                ? $studentModel->section->semester->level
                : null;

            // The logic block for filtering studyModes/courseOfferings is complex
            // and generally not needed for a simple *transcript view* which only displays past grades.
            // It's likely related to course registration or current semester info, which is irrelevant for a full transcript.
            // Keeping it minimal as per the original, but often this block is omitted in a dedicated 'transcripts' method.
            $studyModes = StudyMode::with([
                'sections.courseOfferings',
                'sections.studyMode',
                'sections.track',
                'sections.program'
            ])->get();

            $studyModes->each(function ($studyMode) use ($yearLevel, $activeSemester) {
                $studyModeActiveSemester = $studyMode->activeSemester();
                if (!$studyModeActiveSemester || !$yearLevel) {
                    return;
                }
                $studyMode->sections->each(function ($section) use ($yearLevel, $studyModeActiveSemester) {
                    $filteredCourseOfferings = $section->courseOfferings->filter(function ($courseOffering) use ($yearLevel, $studyModeActiveSemester) {
                        return $courseOffering->year_level == $yearLevel
                            && $courseOffering->semester_level == $studyModeActiveSemester->level;
                    });
                    $section->setRelation('courseOfferings', $filteredCourseOfferings);
                });
            });

            // 2. Fetch all semesters with their grades
            // The previous query was correct, but we ensure all necessary grade relations are included for the Vue component.
            $semesters = $studentModel->semesters()
                ->with([
                    'year',
                    // Eager load grades for the specific student, and its related course
                    'grades' => fn($q) => $q->where('student_id', $studentModel->id)->with(['course', 'section', 'semester']),
                ])
                // The Vue component relies on this specific sort order for cumulative GPA calculation
                ->orderBy('year_id', 'asc') // Sort by year first (assuming year_id dictates chronological order)
                ->orderBy('name', 'asc') // Then by semester name ("First Semester" vs "Second Semester")
                ->get();

            // The 'grades' collection below is redundant if 'semesters' is used, but kept for consistency
            // with the calling Vue component's possible expectation.
            $grades = GradeResource::collection(
                $studentModel->grades()->with(['course', 'section', 'semester'])->get()
            );

            return inertia('StudentPortal/Tabs/ShowTranscript', [
                'student' => $student,
                'grades' => $grades,
                // The Vue component expects raw semester objects or a simple collection resource for easy iteration
                'semesters' => $semesters,
                
                // These are often not necessary for a transcript but kept for completeness
                'studyModes' => $studyModes, 
                'activeSemester' => $activeSemester,
                'yearLevel' => $yearLevel,
                'semesterLevel' => $semesterLevel,
            ]);
        }

    public function enrollmentDetail(Enrollment $enrollment)
    {
        $student = new StudentResource(request()->user()->student->load(
            'program',
            'track',
            'section.track',
            'section.program',
            'results'
        ));
        $enrollment->load('student', 'courseOffering.course', 'courseOffering.section.track.program', 'courseOffering.section.studyMode');

        $course = $enrollment->courseOffering->course;
        $section = $enrollment->courseOffering->section;

        $weights = WeightResource::collection(Weight::where('section_id', $section->id)->where('course_id', $course->id)->with('results', function ($q) use ($student) {
            $q->where('student_id', $student->id);
        })->get());

        $activeSemester = $enrollment->semester()->with('year')->first();

        $classSchedules = ClassScheduleResource::collection(
            $enrollment->courseOffering->classSchedules()
                ->where('semester_id', $activeSemester->id)
                ->with('room', 'courseOffering.section.studyMode', 'courseOffering.course', 'courseOffering.instructor')
                ->get()
        );
        $classSessions = ClassSessionResource::collection(
            $enrollment->courseOffering->classSessions()
                ->where('semester_id', $activeSemester->id)
                ->with('room', 'courseOffering.course', 'courseOffering.instructor')
                ->get()
        );


        return Inertia::render('StudentPortal/Enrollments/Show', [
            'enrollment' => new EnrollmentResource($enrollment),
            'student' => $student,
            'weights' => $weights,
            'classSchedules' => $classSchedules,
            'classSessions' => $classSessions,
            'activeSemester' => $activeSemester,
        ]);
    }

    public function classSchedules()
    {
        $studentModel = request()->user()->student->load([
            'program',
            'track',
            'section',
            'enrollments' => function ($q) {
                $q->where('status', 'enrolled')->where('academic_status', 'in_progress');
            },
            'enrollments.courseOffering.classSchedules',
        ]);

        $student = new StudentResource($studentModel);

        $classSchedules = ClassScheduleResource::collection($studentModel->classSchedules());

        return inertia('StudentPortal/ClassSchedules', [
            'student' => $student,
            'classSchedules' => $classSchedules,
        ]);
    }

    public function classSessions()
    {
        $studentModel = request()->user()->student->load([
            'program',
            'track',
            'section',
            'enrollments' => function ($q) {
                $q->where('status', 'enrolled')->where('academic_status', 'in_progress');
            },
            'enrollments.courseOffering.classSessions',
        ]);

        $student = new StudentResource($studentModel);

        $classSessions = ClassSessionResource::collection($studentModel->classSessions());


        return inertia('StudentPortal/ClassSessions', [
            'student' => $student,
            'classSessions' => $classSessions,
        ]);
    }

    public function profile()
    {
        $student = new StudentResource(request()->user()->student->load('program', 'track', 'section.year', 'user'));

        return inertia('StudentPortal/Profile', [
            'student' => $student,
        ]);
    }

    public function result()
    {

        $student = new StudentResource(request()->user()->student->load([
            'program',
            'track',
            'section',
            'grades.course',
            'grades.semester',
            'results.weight.course'
        ]));

        return inertia('StudentPortal/StudentResults', [
            'student' => $student,
        ]);
    }

    public function payment()
    {

        $student = new StudentResource(request()->user()->student->load([
            'program',
            'track',
            'section',
            'payments.paymentMethod',
            'payments.paymentType',
            'payments.semester'
        ]));

        return inertia('StudentPortal/Payment', [
            'student' => $student,
        ]);
    }

    public function editProfile()
    {
        $student = new StudentResource(request()->user()->student->load('program', 'track', 'section'));

        return inertia('profile/StudentEdit', [
            'student' => $student,
        ]);
    }

    // Results and grades of student
    public function grades()
    {
        $student = new StudentResource(request()->user()->student->load('program', 'track', 'section'));

        $semester = Semester::where('status', 'Active')->first();

        $grades = $student->grades()->with(['result', 'semester', 'course', 'complaints'])->get();

        $results = $student->results()->with('weight', 'grade', 'semester', 'weight.course')->get();

        return inertia('StudentPortal/ShowAssessment', [
            'student' => $student,
            'grades' => $grades,
            'semester' => new SemesterResource($semester),
            'results' => $results,
        ]);
    }
}
