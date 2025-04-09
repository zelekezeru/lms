<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Section;
use App\Models\Instructor;
use App\Models\Student;

class AssignmentController extends Controller
{
    //Assigning courses to sections, Instructors to sections,  Instructors to courses, Students to sections, Students to courses


    //Assignning courses to sections

    public function create_courses_sections()
    {
        return inertia('Assignments/CoursesSections', [
            'courses' => Course::all(),
            'sections' => Section::all(),
        ]);
    }

    public function assign_courses_sections(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'section_id' => 'required|exists:sections,id',
        ]);

        $course = Course::find($request->course_id);
        $section = Section::find($request->section_id);

        $course->sections()->attach($section);

        return redirect()->route('courses.index')->with('success', 'Course assigned to section successfully.');
    }
    
    public function remove_courses_sections(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'section_id' => 'required|exists:sections,id',
        ]);

        $course = Course::find($request->course_id);
        $section = Section::find($request->section_id);

        $course->sections()->detach($section);

        return redirect()->route('courses.index')->with('success', 'Course removed from section successfully.');
    }

    //Assigning instructors to sections
    public function create_instructors_sections()
    {
        return inertia('Assignments/InstructorsSections', [
            'instructors' => Instructor::all(),
            'sections' => Section::all(),
        ]);
    }
    public function assign_instructors_sections(Request $request)
    {
        $request->validate([
            'instructor_id' => 'required|exists:instructors,id',
            'section_id' => 'required|exists:sections,id',
        ]);

        $instructor = Instructor::find($request->instructor_id);
        $section = Section::find($request->section_id);

        $instructor->sections()->attach($section);

        return redirect()->route('instructors.index')->with('success', 'Instructor assigned to section successfully.');
    }
    public function remove_instructors_sections(Request $request)
    {
        $request->validate([
            'instructor_id' => 'required|exists:instructors,id',
            'section_id' => 'required|exists:sections,id',
        ]);

        $instructor = Instructor::find($request->instructor_id);
        $section = Section::find($request->section_id);

        $instructor->sections()->detach($section);

        return redirect()->route('instructors.index')->with('success', 'Instructor removed from section successfully.');
    }

    //Assigning instructors to courses
    public function create_instructors_courses()
    {
        return inertia('Assignments/InstructorsCourses', [
            'instructors' => Instructor::all(),
            'courses' => Course::all(),
        ]);
    }
    public function assign_instructors_courses(Request $request)
    {
        $request->validate([
            'instructor_id' => 'required|exists:instructors,id',
            'course_id' => 'required|exists:courses,id',
        ]);

        $instructor = Instructor::find($request->instructor_id);
        $course = Course::find($request->course_id);

        $instructor->courses()->attach($course);

        return redirect()->route('instructors.index')->with('success', 'Instructor assigned to course successfully.');
    }
    public function remove_instructors_courses(Request $request)
    {
        $request->validate([
            'instructor_id' => 'required|exists:instructors,id',
            'course_id' => 'required|exists:courses,id',
        ]);

        $instructor = Instructor::find($request->instructor_id);
        $course = Course::find($request->course_id);

        $instructor->courses()->detach($course);

        return redirect()->route('instructors.index')->with('success', 'Instructor removed from course successfully.');
    }


    //Assigning students to sections
    public function create_students_sections()
    {
        return inertia('Assignments/StudentsSections', [
            'students' => Student::all(),
            'sections' => Section::all(),
        ]);
    }
    public function assign_students_sections(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'section_id' => 'required|exists:sections,id',
        ]);

        $student = Student::find($request->student_id);
        $section = Section::find($request->section_id);

        $student->sections()->attach($section);

        return redirect()->route('students.index')->with('success', 'Student assigned to section successfully.');
    }
    public function remove_students_sections(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'section_id' => 'required|exists:sections,id',
        ]);

        $student = Student::find($request->student_id);
        $section = Section::find($request->section_id);

        $student->sections()->detach($section);

        return redirect()->route('students.index')->with('success', 'Student removed from section successfully.');
    }

    //Assigning students to courses
    public function create_students_courses()
    {
        return inertia('Assignments/StudentsCourses', [
            'students' => Student::all(),
            'courses' => Course::all(),
        ]);
    }
    public function assign_students_courses(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'course_id' => 'required|exists:courses,id',
        ]);

        $student = Student::find($request->student_id);
        $course = Course::find($request->course_id);

        $student->courses()->attach($course);

        return redirect()->route('students.index')->with('success', 'Student assigned to course successfully.');
    }
    public function remove_students_courses(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'course_id' => 'required|exists:courses,id',
        ]);

        $student = Student::find($request->student_id);
        $course = Course::find($request->course_id);

        $student->courses()->detach($course);

        return redirect()->route('students.index')->with('success', 'Student removed from course successfully.');
    }
}
