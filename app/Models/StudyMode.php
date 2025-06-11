<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudyMode extends Model
{
    protected $guarded = [];

    public function programs()
    {
        return $this->belongsToMany(Program::class)->withPivot('duration');
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function sections(): HasMany
    {
        return $this->hasMany(Section::class);
    }

    public function curricula()
    {
        return $this->hasMany(Curriculum::class);
    }

    public function paymentTypes(): HasMany
    {
        return $this->hasMany(PaymentType::class);
    }
}
