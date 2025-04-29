<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Registration;

class Course extends Model
{
    protected $guarded = [];

    public function track()
    {
        return $this->belongsTo(Track::class);
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class);
    }

    public function sections(): BelongsToMany
    {
        return $this->belongsToMany(Section::class)->withPivot('instructor_id');
    }

    public function instructors(): BelongsToMany
    {
        return $this->belongsToMany(Instructor::class);
    }

    public function trackS(): BelongsToMany
    {
        return $this->belongsToMany(Track::class);
    }

    public function years()
    {
        return $this->belongsTo(Year::class);
    }

    public function semesters()
    {
        return $this->belongsTo(Semester::class);
    }

    public function programs()
    {
        return $this->belongsToMany(Program::class);
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

    public function curricula()
    {
        return $this->hasMany(Curriculum::class);
    }

    public function prerequisites()
    {
        return $this->belongsToMany(Course::class, 'course_prerequisites', 'course_id', 'prerequisite_id');
    }

    public function isEligible($studentId)
    {
        $prereqs = $this->prerequisites;

        foreach ($prereqs as $pre) {
            $passed = Student::where('id', $studentId)
                ->where('status', 'completed')
                ->whereHas('curriculum', function ($q) use ($pre) {
                    $q->where('course_id', $pre->id);
                })->exists();

            if (!$passed) return false;
        }
        return true;
    }
}
