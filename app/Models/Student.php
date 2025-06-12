<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function getActiveStudents()
    {
        return Student::whereHas('status', function ($q) {
            $q->where('is_active', true);
        })->get();
    }

    public function status()
    {
        return $this->hasOne(Status::class);
    }

    public function church()
    {
        return $this->hasOne(Church::class);
    }

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function track()
    {
        return $this->belongsTo(Track::class);
    }

    public function studyMode()
    {
        return $this->belongsTo(StudyMode::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function classSchedules()
    {
        return $this->enrollments()
            ->with(
                'courseOffering.classSchedules.courseOffering.course',
                'courseOffering.classSchedules.courseOffering.instructor',
                'courseOffering.classSchedules.courseOffering.section.track',
                'courseOffering.classSchedules.courseOffering.section.studyMode'
            )
            ->get()
            ->pluck('courseOffering.classSchedules')
            ->flatten();
    }

    public function classSessions()
    {
        return $this->enrollments()
            ->with(
                'courseOffering.classSessions.courseOffering.course',
                'courseOffering.classSessions.courseOffering.instructor',
                'courseOffering.classSessions.courseOffering.section.track',
                'courseOffering.classSessions.courseOffering.section.studyMode'
            )
            ->get()
            ->pluck('courseOffering.classSessions')
            ->flatten();
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function year()
    {
        return $this->belongsTo(Year::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function results()
    {
        return $this->hasMany(Result::class);
    }

    public function grades()
    {
        return $this->hasMany(Grade::class);
    }

    public function semesterStudent()
    {
        return $this->hasMany(SemesterStudent::class);
    }

    public function semesters()
    {
        return $this->belongsToMany(Semester::class)->withPivot('academic_status', 'payment_status');
    }

    public function centers()
    {
        return $this->belongsToMany(Center::class, 'center_student');
    }

    public function study_modes()
    {
        return $this->belongsTo(StudyMode::class, 'study_mode');
    }
}
