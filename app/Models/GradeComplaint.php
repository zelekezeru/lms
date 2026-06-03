<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GradeComplaint extends Model
{
    protected $guarded = [];

    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function filedByUser()
    {
        return $this->belongsTo(User::class, 'filed_by_user_id');
    }

    public function reviewedByUser()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function auditLogs()
    {
        return $this->hasMany(GradeAuditLog::class, 'complaint_id');
    }
}
