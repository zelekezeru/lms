<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use App\Models\GradeAuditLog;
use App\Models\GradeComplaint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GradeComplaintController extends Controller
{
    /**
     * Student or Instructor files a grade complaint.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'grade_id' => 'required|exists:grades,id',
            'reason' => 'required|string|max:1000',
            'filed_by_role' => 'required|in:student,instructor',
        ]);

        $grade = Grade::with('complaints')->findOrFail($data['grade_id']);

        // Guard: grade must be locked (approved) before a complaint can be filed
        if (!$grade->is_locked) {
            return back()->with('error', 'A grade complaint can only be filed after the grade has been officially approved and locked.');
        }

        // Guard: only one active complaint at a time
        $existing = $grade->complaints()->whereIn('status', ['pending'])->first();
        if ($existing) {
            return back()->with('error', 'There is already a pending complaint for this grade.');
        }

        GradeComplaint::create([
            'grade_id' => $grade->id,
            'student_id' => $grade->student_id,
            'section_id' => $grade->section_id,
            'course_id' => $grade->course_id,
            'filed_by_user_id' => Auth::id(),
            'filed_by_role' => $data['filed_by_role'],
            'reason' => $data['reason'],
            'status' => 'pending',
            'original_grade_point' => $grade->grade_point,
            'original_grade_letter' => $grade->grade_letter,
        ]);

        return back()->with('success', 'Grade complaint submitted successfully. Awaiting review by Academic Dean.');
    }

    /**
     * Academic Dean / Admin approves the complaint — unlocks grade for instructor improvement.
     */
    public function approve(Request $request, GradeComplaint $gradeComplaint)
    {
        $request->validate(['review_notes' => 'nullable|string|max:500']);

        if ($gradeComplaint->status !== 'pending') {
            return back()->with('error', 'This complaint has already been reviewed.');
        }

        DB::beginTransaction();
        try {
            $gradeComplaint->update([
                'status' => 'approved',
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now(),
                'review_notes' => $request->review_notes,
            ]);

            // Unlock the grade so the instructor can improve it
            Grade::where('id', $gradeComplaint->grade_id)
                ->update(['is_locked' => false, 'grade_status' => 'Under Review']);

            DB::commit();
            return back()->with('success', 'Complaint approved. The instructor can now submit an improved grade.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to approve complaint: ' . $e->getMessage());
        }
    }

    /**
     * Academic Dean / Admin rejects the complaint.
     */
    public function reject(Request $request, GradeComplaint $gradeComplaint)
    {
        $request->validate(['review_notes' => 'nullable|string|max:500']);

        if ($gradeComplaint->status !== 'pending') {
            return back()->with('error', 'This complaint has already been reviewed.');
        }

        $gradeComplaint->update([
            'status' => 'rejected',
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
            'review_notes' => $request->review_notes,
        ]);

        return back()->with('success', 'Complaint rejected. The original grade remains unchanged.');
    }

    /**
     * Instructor submits an improved grade after complaint is approved.
     */
    public function improve(Request $request, GradeComplaint $gradeComplaint)
    {
        $data = $request->validate([
            'improved_grade_point' => 'required|numeric|min:0|max:100',
            'improved_grade_letter' => 'required|string|max:3',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($gradeComplaint->status !== 'approved') {
            return back()->with('error', 'Improvement can only be submitted after the complaint is approved.');
        }

        if ($gradeComplaint->improvement_applied) {
            return back()->with('error', 'An improvement has already been applied for this complaint.');
        }

        $grade = Grade::findOrFail($gradeComplaint->grade_id);

        DB::beginTransaction();
        try {
            $oldPoint = $grade->grade_point;
            $oldLetter = $grade->grade_letter;

            // Apply the improvement
            $grade->update([
                'grade_point' => $data['improved_grade_point'],
                'grade_letter' => $data['improved_grade_letter'],
                'changed_grade' => $data['improved_grade_point'],
                'changed_letter' => $data['improved_grade_letter'],
                'changed_by' => Auth::id(),
                'grade_status' => 'Approved',
                'is_locked' => true,  // Re-lock
            ]);

            // Record on the complaint
            $gradeComplaint->update([
                'improved_grade_point' => $data['improved_grade_point'],
                'improved_grade_letter' => $data['improved_grade_letter'],
                'improvement_applied' => true,
            ]);

            // Audit log
            GradeAuditLog::create([
                'grade_id' => $grade->id,
                'student_id' => $grade->student_id,
                'course_id' => $grade->course_id,
                'section_id' => $grade->section_id,
                'changed_by' => Auth::id(),
                'change_type' => 'complaint_improvement',
                'old_grade_point' => $oldPoint,
                'old_grade_letter' => $oldLetter,
                'new_grade_point' => $data['improved_grade_point'],
                'new_grade_letter' => $data['improved_grade_letter'],
                'complaint_id' => $gradeComplaint->id,
                'notes' => $data['notes'] ?? 'Grade improved following approved complaint',
            ]);

            DB::commit();
            return back()->with('success', 'Grade improved and re-locked. The audit trail has been recorded.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to apply improvement: ' . $e->getMessage());
        }
    }
}
