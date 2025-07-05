<?php

namespace App\Http\Services;

use App\Models\CourseOffering;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\PaymentType;
use App\Models\Semester;
use App\Models\SemesterStudent;
use App\Models\Student;
use App\Models\StudyMode;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AutoEnrollmentService
{
  public static function autoEnroll($studyModeId)
  {
    $activeSemester = StudyMode::find($studyModeId)->activeSemester();
    $students = Student::getActiveForStudyMode($studyModeId);

    foreach ($students as $student) {
      if (! $student->section) continue;

      $section = $student->section;

      // Check and complete old semesters
      $semesterStudentRecords = SemesterStudent::where('student_id', $student->id)
        ->where('academic_status', 'in_progress')
        ->get();

      $isEligible = false;

      foreach ($semesterStudentRecords as $record) {
        if ($record->isEligibleForCompletion()) {
          $record->update(['academic_status' => 'completed']);
          $isEligible = true;
        }
      }

      // Skip if student is not eligible for new semester
      if (
        !$isEligible && SemesterStudent::where('student_id', $student->id)
        ->where('semester_id', $activeSemester->id)
        ->exists()
      ) {
        continue;
      }

      $semesterRegistrationPaymentType = PaymentType::where('type', 'Semester Registration')->where('study_mode_id', $student->study_mode_id)->first();

      $total_amount = $semesterRegistrationPaymentType->amount ?? 0;

      $semesterRegistrationPayment = Payment::create([
        'student_id' => $student->id,
        'payment_type_id' => $semesterRegistrationPaymentType?->id,
        'semester_id' => $activeSemester->id,
        'tenant_id' => 1,
        'created_by' => Auth::id(),
        'status' => $student->status->is_scholarship ? 'paid_by_college' : 'pending',
        'paid_amount' => 0,
        'total_amount' => $total_amount,
      ]);

      // Ensure student has a SemesterStudent record for the current semester
      $semesterStudent = SemesterStudent::updateOrCreate([
        'student_id' => $student->id,
        'semester_id' => $activeSemester->id,
      ], [
        'payment_status' => $student->status->is_scholarship ? 'paid' : 'unpaid',
        'academic_status' => 'in_progress'
      ]);

      // Fetch course offerings for the section
      $courseOfferingIds = CourseOffering::where('section_id', $section->id)
        ->where('semester_level', $activeSemester->level)
        ->where('year_level', $section->yearLevel())
        ->pluck('id');

      foreach ($courseOfferingIds as $courseOfferingId) {
        $alreadyEnrolled = Enrollment::where('student_id', $student->id)
          ->where('course_offering_id', $courseOfferingId)
          ->where('semester_id', $activeSemester->id)
          ->whereIn('status', ['pending', 'enrolled'])
          ->exists();

        if (!$alreadyEnrolled) {
          $enrollment = Enrollment::create([
            'student_id' => $student->id,
            'course_offering_id' => $courseOfferingId,
            'semester_id' => $activeSemester->id,
            'status' => $student->status->is_scholarship ? 'enrolled' : 'pending',
            'academic_status' => 'in_progress'
          ]);
        }
      }

      $courseFeePaymentType = PaymentType::where('type', 'Course Fee')->where('duration', 'per-course')->where('study_mode_id', $student->study_mode_id)->first();

      $total_amount = $semesterRegistrationPaymentType->amount ?? 0;

      Payment::create([
        'student_id' => $student->id,
        'payment_type_id' => $courseFeePaymentType?->id,
        'semester_id' => $activeSemester->id,
        'description' => 'Tuition Fee',
        'tenant_id' => 1,
        'created_by' => Auth::id(),
        'status' => $student->status->is_scholarship ? 'paid_by_college' : 'pending',
        'paid_amount' => 0,
        'total_amount' => $total_amount * count($courseOfferingIds),
      ]);
    }
  }
}
