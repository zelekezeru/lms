<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Instructor;
use App\Models\Program;
use App\Models\Section;
use App\Models\Student;
use App\Models\StudyMode;
use App\Models\Track;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $section->courses()->sync($request['courses']);

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

    // this method is used to assign courses to a program
    public function assignInstructorToCourseSection(Request $request, Section $section, Course $course)
    {
        $courseSectionAssignment = $section->courseSectionAssignments()->where('course_id', $course->id);

        $courseSectionAssignment->update([
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
        }
        else {
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
}
