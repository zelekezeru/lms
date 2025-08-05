<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\Section;
use App\Models\User;

class InstructorPortalPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct() {}

    public function canViewCourseDetails(Course $course): bool
    {
        $instructor = request()->user()->instructor;
        if (! $instructor) return false;

        return $course->instructors->contains(request()->user()->instructor->id);
    }

    public function canManageCourseSectionPair(User $user, Section $section, Course $course): bool
    {
        $instructor = $user->instructor;
        if (! $instructor) return false;

        return $instructor->courseOfferings()
            ->where('course_id', $course->id)
            ->where('section_id', $section->id)
            ->exists();
    }
}
