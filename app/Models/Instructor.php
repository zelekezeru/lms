<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Instructor extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'specialization',
        'employment_type',
        'hire_date',
        'status',
        'bio',
        'profile_image',
    ];

    // BelongsTo relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    // BelongsToMany relationships
    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class);
    }

    public function sections(): BelongsToMany
    {
        return $this->belongsToMany(Section::class);
    }

    // HasMany relationships
    public function weights(): HasMany
    {
        return $this->hasMany(Weight::class);
    }

    public function results(): HasMany
    {
        return $this->hasMany(Result::class);
    }

    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class);
    }
}
