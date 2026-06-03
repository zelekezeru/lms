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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AutoEnrollmentService
{
    public static function autoEnroll($studyModeId, $creatorId = null)
    {
        $studyMode = StudyMode::find($studyModeId);
        if (!$studyMode) {
            Log::warning("AutoEnrollment: StudyMode ID {$studyModeId} not found.");
            return;
        }

        $activeSemester = $studyMode->activeSemester();
        if (!$activeSemester) {
            Log::warning("AutoEnrollment: No active semester found for StudyMode ID {$studyModeId}.");
            return;
        }

        // Query payment configuration types once outside the loop to avoid N+1 query storms
        $semesterRegistrationPaymentType = PaymentType::where('type', 'Semester Registration')
            ->where('study_mode_id', $studyModeId)
            ->first();

        $courseFeePaymentType = PaymentType::where('type', 'Course Fee')
            ->where('duration', 'per-course')
            ->where('study_mode_id', $studyModeId)
            ->first();

        $regAmount = $semesterRegistrationPaymentType->amount ?? 0;
        $courseAmount = $courseFeePaymentType->amount ?? 0;

        // Retrieve active students in chunks to optimize memory and DB execution times
        Student::whereHas('status', function ($q) {
            $q->where('is_active', true);
        })
        ->where('study_mode_id', $studyModeId)
        ->chunkById(100, function ($students) use ($activeSemester, $semesterRegistrationPaymentType, $courseFeePaymentType, $regAmount, $courseAmount, $creatorId) {
            
            DB::transaction(function () use ($students, $activeSemester, $semesterRegistrationPaymentType, $courseFeePaymentType, $regAmount, $courseAmount, $creatorId) {
                
                foreach ($students as $student) {
                    $tenantId = $student->tenant_id ?? 1;

                    // Clean up unpaid / pending enrollments from previous semesters
                    $student->enrollments()->where('status', 'pending')->update(['status' => 'dropped']);

                    // Clean up ongoing active enrollments (resetting for the new semester)
                    $student->enrollments()->where('status', 'enrolled')->update(['status' => 'dropped']);

                    if (!$student->section) {
                        continue;
                    }

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

                    // Skip if student is not eligible and already exists in the new semester
                    $alreadyRegistered = SemesterStudent::where('student_id', $student->id)
                        ->where('semester_id', $activeSemester->id)
                        ->exists();

                    if (!$isEligible && $alreadyRegistered) {
                        continue;
                    }

                    // Create semester registration fee invoice payment record
                    Payment::create([
                        'student_id' => $student->id,
                        'payment_type_id' => $semesterRegistrationPaymentType?->id,
                        'semester_id' => $activeSemester->id,
                        'tenant_id' => $tenantId,
                        'created_by' => $creatorId,
                        'status' => ($student->status && $student->status->is_scholarship) ? 'paid_by_college' : 'pending',
                        'paid_amount' => 0,
                        'total_amount' => $regAmount,
                    ]);

                    // Ensure student has a SemesterStudent record for the active semester
                    SemesterStudent::updateOrCreate([
                        'student_id' => $student->id,
                        'semester_id' => $activeSemester->id,
                    ], [
                        'payment_status' => ($student->status && $student->status->is_scholarship) ? 'paid' : 'unpaid',
                        'academic_status' => 'in_progress'
                    ]);

                    // Fetch course offerings for the section and semester levels
                    $courseOfferingIds = CourseOffering::where('section_id', $section->id)
                        ->where('semester_level', $activeSemester->level)
                        ->where('year_level', $section->yearLevel())
                        ->pluck('id');

                    // Register enrollments for all section course offerings
                    foreach ($courseOfferingIds as $courseOfferingId) {
                        $alreadyEnrolled = Enrollment::where('student_id', $student->id)
                            ->where('course_offering_id', $courseOfferingId)
                            ->where('semester_id', $activeSemester->id)
                            ->whereIn('status', ['pending', 'enrolled'])
                            ->exists();

                        if (!$alreadyEnrolled) {
                            Enrollment::create([
                                'student_id' => $student->id,
                                'course_offering_id' => $courseOfferingId,
                                'semester_id' => $activeSemester->id,
                                'status' => ($student->status && $student->status->is_scholarship) ? 'enrolled' : 'pending',
                                'academic_status' => 'in_progress'
                            ]);
                        }
                    }

                    // Create tuition fee payment record
                    Payment::create([
                        'student_id' => $student->id,
                        'payment_type_id' => $courseFeePaymentType?->id,
                        'semester_id' => $activeSemester->id,
                        'description' => 'Tuition Fee',
                        'tenant_id' => $tenantId,
                        'created_by' => $creatorId,
                        'status' => ($student->status && $student->status->is_scholarship) ? 'paid_by_college' : 'pending',
                        'paid_amount' => 0,
                        'total_amount' => $courseAmount * count($courseOfferingIds),
                    ]);
                }
            });
        });
    }
}
