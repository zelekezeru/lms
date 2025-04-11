<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class StudyMode extends Model
{
    protected $guarded =[];

    public function programs(): BelongsToMany
    {
        return $this->belongsToMany(Program::class);
    }
}
