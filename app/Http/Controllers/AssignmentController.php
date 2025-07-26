<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseOffering;
use App\Models\Instructor;
use App\Models\Program;
use App\Models\Section;
use App\Models\Student;
use App\Models\StudyMode;
use App\Models\Track;
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
        // Load all sections by year_id
        $sections = $track->sections()->pluck('id', 'year_id'); // ['year_id' => section_id]

        // Load only necessary fields to reduce memory usage
        $students = Student::select('id', 'year_id', 'section_id')
            ->where('track_id', $track->id)
            ->get();

        $updates = [];

        foreach ($students as $student) {
            $yearId = $student->year_id;
            $targetSectionId = $sections[$yearId] ?? null;

            if (!$targetSectionId) {
                continue;
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
            Log::error("Error sorting students: " . $e->getMessage());
            return redirect()->back()->withErrors('Sorting failed: ' . $e->getMessage());
        }
    }
}
