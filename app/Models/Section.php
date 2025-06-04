<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Section extends Model
{
    protected $guarded = [];

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function track()
    {
        return $this->belongsTo(Track::class);
    }

    public function studyMode()
    {
        return $this->belongsTo(StudyMode::class);
    }

    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class)->withPivot('instructor_id');
    }

    public function studentsByCourse($courseId)
    {
        return Student::whereHas('courses', function ($query) use ($courseId) {
            $query->where('course_id', $courseId)
                ->whereNot('course_student.status', 'Dropped')
                ->where('section_id', $this->id);
        })->orderBy('first_name')
            ->orderBy('middle_name')
            ->orderBy('last_name')
            ->get();
    }

    public function classSchedules()
    {
        return $this->hasMany(ClassSchedule::class)->where('semester_id', Semester::getActiveSemester()->id);
    }

    public function classSessions()
    {
        return $this->hasMany(ClassSession::class)->where('semester_id', Semester::getActiveSemester()->id);
    }

    public function instructors(): BelongsToMany
    {
        return $this->belongsToMany(Instructor::class);
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function year()
    {
        return $this->belongsTo(Year::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function results()
    {
        return $this->hasMany(Result::class);
    }

    public function weights()
    {
        return $this->hasMany(Weight::class);
    }

    public function grades()
    {
        return $this->hasMany(Grade::class);
    }

    public function courseOfferings()
    {
        return $this->hasMany(CourseOffering::class);
    }

    public function yearLevel(): int
    {
        $yearLevel = intval(Year::getCurrentYear()->name) - intval($this->year->name) + 1; // plus one because if currentyear is 2025 and the batch year of the section is also 2025 we want to get 1 instead of 0

        return $yearLevel;
    }

    public function getActiveCurricula()
    {
        $curricula = Curriculum::where('year_level', $this->yearLevel())
            ->where('semester', Semester::getActiveSemester()->level)
            ->where('track_id', $this->track_id)
            ->with(['course' => function ($q) {
                $q->with(['courseOfferings' => fn($q) => $q->where('section_id', $this->id)->with('instructor')]);
            }])->get();

        return $curricula;
    }
}
