<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_name', 
        'father_name', 
        'grand_father_name',
        'mobile_phone', 
        'office_phone', 
        'date_of_birth', 
        'email',
        'marital_status',
        'sex', 
        'academic_year', 
        'student_id', 
        'semester', 
        'program', 
        'pastor_name', 
        'pastor_phone', 
        'address_1', 
        'position_denomination',
        'church_name',
        'church_address',
        'total_credit_hours', 
        'total_amount_paid', 
        'student_signature',
        'office_use_notes',
        'default_password',
        
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    } 
    
    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class);
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
}
