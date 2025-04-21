<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Department;
use App\Models\Section;
use App\Models\Instructor;
use App\Models\Program;
use App\Models\Student;
use Inertia\Inertia;

class AssignmentController extends Controller
{
    //Assigning courses to sections, instructors, and students

    //Assignning Courses to Program

    public function program_courses(Program $program)
    {
        $courses = Course::get();

        // Fetch existing attached courses
        $attachedcourses = $program->courses()->get()->pluck('id');

        return inertia('Assignments/ProgramCourses', compact('program', 'courses', 'attachedcourses'));
    
    }    

    public function attach_program_courses(Request $request, $programId)
    {
        $program = Program::findOrFail($programId);
        
        $program->courses()->sync($request['courses']);

        return redirect()->route('programs.show', $program)->with('success', 'Courses Assigned successfully.');
    }

    public function attach_section_courses(Request $request, Section $section)
    {
        $section->courses()->sync($request['courses']);

        return redirect()->route('sections.show', $section->id)->with('success', 'Courses Assigned successfully.');
    }
    public function attach_instructor_courses(Request $request, Instructor $instructor)
    {
        $instructor->courses()->sync($request['courses']);

        return redirect()->route('instructors.show', $instructor->id)->with('success', 'Courses Assigned successfully.');
    }

    public function attach_section_course_instructor(Request $request, Section $section)
    {
        $courseSectionAssignment = $section->courseSectionAssignments()->where('course_id', $request->course_id);

        $courseSectionAssignment->update([
            'instructor_id' => $request->instructor_id
        ]);

        return redirect()->route('sections.show', $section->id)->with('success', 'Instructor Assigned successfully.');
    }


    //Assigning Departments to Course
    public function course_departments($course)
    {
        $departments = Department::get();

        // Fetch existing attached departments
        $attachedDepartments = $course->departments()->get()->pluck('id');

        return Inertia::render('Assignments/CourseDepartments', compact('course', 'departments', 'attachedDepartments'));
    }

    public function attach_section_instructors(Request $request, $sectionId)
    {
        $section = Section::findOrFail($sectionId);
        
        $section->instructors()->sync($request['instructors']);

        return redirect()->route('sections.show', $section)->with('success', 'Instructors Assigned successfully.');
    }

    //Assign Students to Section
    public function section_students($section)
    {
        $students = Student::get();

        // Fetch existing attached students
        $attachedStudents = $section->students()->get()->pluck('id');

        return Inertia::render('Assignments/SectionStudents', compact('section', 'students', 'attachedStudents'));
    }

    public function attach_section_students(Request $request, $sectionId)
    {
        $section = Section::findOrFail($sectionId);
        
        $section->students()->sync($request['students']);

        return redirect()->route('sections.show', $section)->with('success', 'Students Assigned successfully.');
    }

    public function detach_section_students(Request $request, $sectionId, $studentId)
    {
        $section = Section::findOrFail($sectionId);

        $section->students()->detach($studentId);

        return redirect()->route('sections.show', $section)->with('success', 'Student Detached successfully.');
    }

    //Assigning Instructors to Course
    public function course_instructors($course)
    {
        $instructors = Instructor::get();

        // Fetch existing attached instructors
        $attachedInstructors = $course->instructors()->get()->pluck('id');

        return Inertia::render('Assignments/CourseInstructors', compact('course', 'instructors', 'attachedInstructors'));
    }
    public function attach_course_instructors(Request $request, $courseId)
    {
        $course = Course::findOrFail($courseId);
        
        $course->instructors()->sync($request['instructors']);

        return redirect()->route('courses.show', $course)->with('success', 'Instructors Assigned successfully.');
    }
    public function detach_course_instructors(Request $request, $courseId, $instructorId)
    {
        $course = Course::findOrFail($courseId);

        $course->instructors()->detach($instructorId);

        return redirect()->route('courses.show', $course)->with('success', 'Instructor Detached successfully.');
    }
    //Assigning Students to Course
    public function course_students($course)
    {
        $students = Student::get();

        // Fetch existing attached students
        $attachedStudents = $course->students()->get()->pluck('id');

        return Inertia::render('Assignments/CourseStudents', compact('course', 'students', 'attachedStudents'));
    }
    public function attach_course_students(Request $request, $courseId)
    {
        $course = Course::findOrFail($courseId);
        
        $course->students()->sync($request['students']);

        return redirect()->route('courses.show', $course)->with('success', 'Students Assigned successfully.');
    }
    public function detach_course_students(Request $request, $courseId, $studentId)
    {
        $course = Course::findOrFail($courseId);

        $course->students()->detach($studentId);

        return redirect()->route('courses.show', $course)->with('success', 'Student Detached successfully.');
    }


}
