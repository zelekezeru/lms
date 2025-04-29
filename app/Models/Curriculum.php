<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Curriculum extends Model
{
    protected $guarded = [];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function track()
    {
        return $this->belongsTo(Track::class);
    }

    public function studyMode()
    {
        return $this->belongsTo(StudyMode::class);
    }
}
