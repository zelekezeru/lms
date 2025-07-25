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
    // convention for methods is as follows
    /**
     * every post method that actually assigns the lists of model entities from request to a model is  start with "assign"
     * then follows the entities(targets) that are going to be followed by a Owner of targets

     * so a name like assignCoursesToSection -> accepts an array of ids of courses to be assigned to a section which should be accepted by laravel's route model binding as argument

     * and a name like assignInstructorToCourseSection -> relates a single instructor to a CourseSection model later to be filtered inside the method
     */

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
        $coursesWithStatus = collect($request['courses'])
            ->mapWithKeys(function ($courseId) use ($student) {
                return [$courseId => ['status' => 'Enrolled', 'section_id' => $student->section_id]];
            })
            ->toArray();

        $student->courses()->sync($coursesWithStatus);

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

    // sortStudentsToSections route("student-section.sort", { track: props.track.id }),
    public function sortStudentsToSections(Track $track)
    {
        // 1. Eager load related models to avoid N+1 problem for students' year and section
        // 2. Fetch students who either have no section_id OR whose section_id needs to be potentially updated
        //    (depending on your exact logic for re-sorting vs. initial assignment)
        $students = $track->students()->with('year')->get();

        // Fetch all relevant sections for this track in one go
        // This avoids N+1 queries for sections inside the loop.
        $sections = Section::where('track_id', $track->id)->get()->keyBy('year_id');

        DB::beginTransaction(); // Start a transaction for multiple updates

        try {
            foreach ($students as $student) {
                // Corrected condition: Check if section_id is null OR if you intend to re-sort
                // If you only want to assign students who DON'T have a section_id yet:
                // if ($student->section_id === null) {

                // If you want to ensure they are in the CORRECT section for their year/track,
                // regardless if they already have a section_id:
                // (No outer if needed, the logic inside handles it)

                $targetSection = $sections->get($student->year_id);

                if (!$targetSection) {
                    // Handle case where no section is found for the student's year within this track
                    // You might log this, skip the student, or throw an exception
                    Log::warning("No matching section found for student ID: {$student->id} (Year ID: {$student->year_id}, Track ID: {$track->id}). Skipping student.");
                    continue; // Skip to the next student
                }

                // Check if the student is already in the correct section to avoid unnecessary updates
                if ($student->section_id !== $targetSection->id) {
                    $student->update(['section_id' => $targetSection->id]);
                }
            }

            DB::commit(); // Commit the transaction if all updates are successful

            return redirect()->back()->with('success', 'Students sorted into sections successfully.');

        } catch (Exception $e) {
            DB::rollBack(); // Rollback if any error occurs
            // Log the error for debugging
            Log::error("Error sorting students into sections: " . $e->getMessage());
            return redirect()->back()->withErrors('Failed to sort students into sections: ' . $e->getMessage());
        }
    }
}