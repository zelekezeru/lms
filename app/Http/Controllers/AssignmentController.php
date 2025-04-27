<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Track;
use App\Models\Section;
use App\Models\Instructor;
use App\Models\Program;
use App\Models\Student;
use Inertia\Inertia;

class AssignmentController extends Controller
{
    // convention for methods is as follows
    /**
     * every post method that actually assigns the lists of model entities from request to a model is  start with "assign" 
     * then follows the entities(targets) that are going to be followed by a Owner of targets

     * so a name like assignCoursesToSection -> accepts an array of ids of courses to be assigned to a section which should be accepted by laravel's route model binding as argument

     * and a name like assignInstructorToCourseSection -> relates a single instructor to a CourseSection model later to be filtered inside the method

     */

    public function assignCoursesToSections(Request $request, Section $section)
    {
        $section->courses()->sync($request['courses']);

        return redirect()->route('sections.show', $section->id)->with('success', 'Courses Assigned successfully.');
    }
    
    public function assignCoursesToInstructor(Request $request, Instructor $instructor)
    {
        $instructor->courses()->sync($request['courses']);
        
        return redirect()->route('instructors.show', $instructor->id)->with('success', 'Courses Assigned successfully.');
    }
    
    public function assignInstructorToCourseSection(Request $request, Section $section, Course $course)
    {
        $courseSectionAssignment = $section->courseSectionAssignments()->where('course_id', $course->id);
        
        $courseSectionAssignment->update([
            'instructor_id' => $request->instructor_id
        ]);
        
        return redirect()->route('sections.show', $section->id)->with('success', 'Instructor Assigned successfully.');
    }

    public function assignCoursesToTracks(Request $request, Track $track)
    {
        $track->courses()->sync($request['courses']);
    
        return redirect()->route('tracks.show', $track->id)->with('success', 'Courses Assigned successfully.');
    }
}
