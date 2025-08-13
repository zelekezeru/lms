<?php

namespace App\Http\Controllers;

use App\Models\Center;
use App\Models\CenterCourse;
use App\Models\Course;
use App\Models\CourseOffering;
use App\Models\Instructor;
use App\Models\Program;
use App\Models\Section;
use App\Models\Student;
use App\Models\StudyMode;
use App\Models\Track;
use App\Models\Year;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class AssignmentController extends Controller
{
    // this method is used to assign courses to a section
    public function assignCoursesToSections(Request $request, Section $section)
    {
        foreach ($request['courses'] as $courseId) {
            CourseOffering::updateOrCreate([
                'course_id' => $courseId,
                'section_id' => $section->id,
            ]);
        }

        return redirect()->route('sections.show', $section->id)->with('success', 'Courses Assigned successfully.');
    }

    // this method is used to assign courses to a program
    public function assignCoursesToInstructor(Request $request, Instructor $instructor)
    {
        $instructor->courses()->sync($request['courses']);

        return redirect()->route('instructors.show', $instructor->id)->with('success', 'Courses Assigned successfully.');
    }

    // this method is used to assign courses to a program
    public function assignInstructorsToCourse(Request $request, Course $course)
    {
        $course->instructors()->sync($request['instructors']);

        return redirect()->route('courses.show', $course->id)->with('success', 'Instructors Assigned successfully.');
    }

    // this method is used to assign courses to a courseSection
    public function assignInstructorToCourseSection(Request $request, Section $section, Course $course)
    {
        $courseOffering = $section->courseOfferings()->where('course_id', $course->id)->with('instructor.classSchedules');

        // Remove or comment out this block if 'instructor_id' does not exist in class_schedules table
        $classSchedules = $section->classSchedules()->where('course_id', $course->id)->update([
            'instructor_id' => $request->instructor_id,
        ]);

        $courseOffering->update([
            'instructor_id' => $request->instructor_id,
        ]);

        return redirect()->route('sections.show', $section->id)->with('success', 'Instructor Assigned successfully.');
    }

    // this method is used to assign courses to a program
    public function assignCoursesToTracks(Request $request, Track $track)
    {
        $track->courses()->sync($request['courses']);

        return redirect()->route('tracks.show', $track->id)->with('success', 'Courses Assigned successfully.');
    }

    public function assignCoursesToStudents(Request $request, Student $student)
    {
        $student->status()->update([
            'is_enrolled' => true,
            'enrolled_by_name' => Auth::user()->name,
            'enrolled_at' => now(),
        ]);

        return redirect()
            ->route('students.show', $student->id)
            ->with('success', 'Courses assigned successfully.');
    }

    // this method is used to assign student to a section
    public function assignStudentsToSection(Request $request)
    {
        $student = Student::where('id', $request['student_id'])->first();

        $student->update([
            'section_id' => $request['section_id'],
        ]);

        session()->flash('message', 'Student has been successfully assigned to the section.');

        return redirect()->route('students.show', $student->id)->with('success', 'Student assigned to section successfully.');
    }

    // this method is used to assign student to a section
    public function assignStudentToSection(Request $request)
    {
        $student = Student::where('id', $request['student_id'])->first();

        $student->update([
            'section_id' => $request['section_id'],
        ]);

        return redirect()->back();
    }

    public function assignStudyModeToProgram(Request $request)
    {
        $fields = $request->validate([
            'program_id' => 'required|exists:programs,id',
            'study_mode_id' => 'required|exists:study_modes,id',
            'duration' => 'required|integer|min:1|max:10',
        ]);

        $program = Program::findOrFail($fields['program_id']);

        $studyMode = StudyMode::findOrFail($fields['study_mode_id']);

        // Assign the study mode to the program
        // dd($program->studyModes()->where('study_mode_id', $fields['study_mode_id'])->exists());
        if ($program->studyModes()->where('study_mode_id', $fields['study_mode_id'])->exists()) {
            return redirect()
                ->route('programs.show', $program->id)
                ->with('error', 'Study Mode already assigned to this program.');
        } else {
            $program->studyModes()->attach($studyMode, [
                'duration' => $fields['duration'],
            ]);
        }

        $program->studyModes()->syncWithoutDetaching([
            $fields['study_mode_id'] => ['duration' => $fields['duration']],
        ]);

        return redirect()
            ->route('programs.show', $program->id)
            ->with('success', 'Study Mode assigned successfully.');
    }

    public function updateSectionCourse(Request $request, Section $section)
    {
        $validated = $request->validate([
            'course_id' => ['required', 'exists:courses,id'],
            'year' => ['required', 'integer', 'min:1'],
            'semester' => ['required', 'integer', 'min:1'],
        ]);

        $courseOffering = $section->courseOfferings()->where('course_id', $validated['course_id'])->first();

        if (! $courseOffering) {
            return back()->withErrors(['course_id' => 'Course not found in this section.']);
        }

        $courseOffering->year_level = $validated['year'];
        $courseOffering->semester_level = $validated['semester'];
        $courseOffering->save();

        return back()->with('success', 'Course updated successfully.');
    }
    /**
     * Show the form for creating a new resource.
     */
    public function sortStudentsToSections(Track $track)
    {
        // Load all sections grouped by study modes... because students should not be assigned to a section with different study mode as theirs
        // this results in structure like...

        $sections = $track->sections()->get()->groupBy('study_mode_id');

        // Load only necessary fields to reduce memory usage
        $students = Student::select('id', 'year_id', 'section_id', 'study_mode_id', 'track_id')
            ->where('track_id', $track->id)
            ->get();

        $updates = [];

        foreach ($students as $student) {
            $yearId = $student->year_id;

            // target section is a section that belongs to the same studymode and year as the student
            $targetSection = $sections->get($student->study_mode_id)->first(function ($section) use ($yearId) ‹
                return $section->year_id == $yearId;
            });

            // if there is no target section found create it
            if ($targetSection) {
                $targetSectionId = $targetSection->id;
            } else {

                $year = Year::where('id', $student->year_id)->first();

                $semester = Semester::where('year_id', $year->id)->first();

                if (! $year) {
                    $year = Year::create([
                        'name' => 'Year ' . now()->year,
                        'status' => 'active',
                    ]);
                    $semester = Semester::create([
                        'name' => '1st of ' . $year->name,
                        'status' => 'active',
                        'level' => 1,
                        'year_id' => $year->id,
                        'start_date' => now(),
                        'end_date' => now()->addMonths(4),
                    ]);
                }

                $yearSuffix = substr($year->name, -2);
                $section_id = 'SC' . '-' . $yearSuffix . '-' . str_pad(Section::where('year_id', $year->id)->count() + 1, 2, '0', STR_PAD_LEFT);

                // If the section already exists, use it
                $section = Section::where('name', $year->name . ' - ' . $track->name . ' Section-1')
                    ->where('code', $section_id)
                    ->where('program_id', $track->program_id)
                    ->where('track_id', $track->id)
                    ->first();
                
                if (!$section) {
                    $section = Section::updateOrCreate([
                        'name' => $year->name . ' - ' . $track->name . ' Section-1',
                        'code' => $section_id,
                        'program_id' => $track->program_id,
                        'track_id' => $track->id,
                    'study_mode_id' => $student->study_mode_id,
                    'year_id' => $year->id,
                    'semester_id' => $semester->id,
                ]);
                }

                $track->sections()->save($section);

                $targetSectionId = $section->id;
                $sections[$yearId] = $targetSectionId;
            }

            // Only update if section_id is different
            if ($student->section_id !== $targetSectionId) {
                $updates[] = [
                    'id' => $student->id,
                    'section_id' => $targetSectionId,
                ];
            }
        }

        DB::beginTransaction();

        // We need to make sure that this barely happens since it will ruin the data record if students section is sorted unintentionally
        try {
            // Perform batch updates
            foreach ($updates as $update) {
                Student::where('id', $update['id'])
                    ->update(['section_id' => $update['section_id']]);
            }

            DB::commit();

            return redirect()->back()->with('success', 'Students sorted successfully into sections.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors('Sorting failed: ' . $e->getMessage());
        }
    }

    // Assign Track Courses to Section
    public function assignTrackCoursesToSection(Section $section)
    {
        $track = Track::where('id', $section->track_id)->first();

        $fields = [
            'track_id' => $track->id,
            'study_mode_id' => $section->study_mode_id,
        ];
        // Assign the track to the section

        $trackCourses = $track->courses()->with(['curricula' => function ($q) use ($fields) {
            return $q->where('track_id', $fields['track_id'])->where('study_mode_id', $fields['study_mode_id']);
        }])->get();

        foreach ($trackCourses as $trackCourse) {
            $curriculum = $trackCourse->curricula->first();
            if ($curriculum !== null) {
                // If course Offering exists register the new one and if it is removed from the new curriculum remove it from the section course offerings
                $courseOffering = CourseOffering::updateOrCreate(
                    [
                        'course_id' => $trackCourse->id,
                        'section_id' => $section->id,
                    ],
                    [
                        'year_level' => $curriculum->year_level ?? null,
                        'semester_level' => $curriculum->semester_level ?? null,
                    ]
                );
                
            }
        }

        return redirect()->route('sections.show', $section->id)->with('success', 'Track assigned to section successfully.');
    }

    // assignTrackCoursesToSections
    public function assignTrackCoursesToSections(Track $track)
    {
        $sections = $track->sections()->get();

        if ($sections->isEmpty()) {
            return redirect()->route('tracks.show', $track->id)->with('error', 'No sections found for this track.');
        }

        foreach ($sections as $section) {
            $this->assignTrackCoursesToSection($section);
        }

        return redirect()->route('tracks.show', $track->id)->with('success', 'Courses assigned to sections successfully.');
    }

    public function assignCoursesToCenter(Request $request, $center)
    {
        $validated = $request->validate([
            'courses' => 'required|array',
            'courses.*' => 'exists:courses,id',
        ]);

        $centerCourses = [];

        // Remove all previous course assignments for the center
        CenterCourse::where('center_id', $center)->delete();

        foreach ($validated['courses'] as $courseId) {
            $centerCourses[] = CenterCourse::updateOrCreate(
                [
                    'center_id' => $center,
                    'course_id' => $courseId,
                ]
            );
        }

        return redirect()->route('centers.show', $center)->with('success', 'Courses assigned to center successfully.');
    }
}

