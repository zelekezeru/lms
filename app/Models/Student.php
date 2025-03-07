<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'academic_year', 
        'semester', 
        'student_id', 
        'program', 
        'year_of_study',
        'student_name', 
        'father_name', 
        'grand_father_name',
        'home_phone',
        'mobile_phone', 
        'office_phone', 
        'date_of_birth', 
        'marital_status',
        'sex', 
        'pastor_name', 
        'address_1', 
        'address_2', 
        'position_denomination',
        'total_credit_hours', 
        'total_amount_paid', 
        'student_signature',
        'office_use_notes'
    ];
}
