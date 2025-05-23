<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromotionStatus extends Model
{
    protected $guarded = [];

    public function semesterStudent()
    {
        return $this->belongsTo(SemesterStudent::class);
    }
}
