<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CenterCourse extends Model
{
    protected $guarded = [];

    public function center()
    {
        return $this->belongsTo(Center::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
