<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GradeAuditLog extends Model
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

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function changedByUser()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    public function complaint()
    {
        return $this->belongsTo(GradeComplaint::class, 'complaint_id');
    }
}
