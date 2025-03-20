<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Instructor extends Model
{
    use HasFactory;

    protected $fillable = [
        // 'tenant_id',
        // 'user_id',
        'department_id',
        'specialization',
        'employment_type',
        'hire_date',
        'status',
        'bio',
        'profile_image',
    ];

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
}
