<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Status;
use Illuminate\Support\Facades\Auth;

class StatusController extends Controller
{

    public function requestScholarship(Request $request, Student $student)
    {
        $request->validate([
            'scholarship_reason' => 'required|string|max:255',
        ]);

        $status = $student->status;
        if (! $status) {
            // Create a new status record if it doesn't exist
            $status = new Status;
            $status->student_id = $student->id;
            $status->user_id = Auth::user()->id; // Assuming you want to set the current user as the one who created the status
        }
        $status->is_scholarship_requested = 1;
        $status->scholarship_requested_by_name = Auth::user()->name;
        $status->scholarship_reason = $request->input('scholarship_reason', 'No reason provided');
        $status->scholarship_requested_at = now();
        // Save the status record
        $status->save();

        // Reload the student and status relationships to ensure fresh data
        $student->refresh();
        $student->load('status');

        return redirect()->route('students.show', $student)->with('success', 'Scholarship request submitted successfully.');
    }

    public function approveScholarship(Request $request, Student $student)
    {
        $status = $student->status;

        // If it's not set, set it to 1
        $status->is_scholarship = 1;
        $status->is_scholarship_approved = 1;
        $status->scholarship_approved_by_name = Auth::user()->name;
        $status->scholarship_approved_at = now();

        // Set the scholarship rejected status to 0
        $status->is_scholarship_rejected = 0;
        $status->scholarship_rejected_reason = null;
        $status->scholarship_rejected_by_name = null;
        $status->scholarship_rejected_at = null;

        // Check if the scholarship status is already set to 1
        if ($status->is_scholarship == 1) {

            $studentPayments = $student->payments()->where('payments.status', 'pending')->get();

            foreach ($studentPayments as $studentPayment) {
                $studentPayment->update(['status' => 'paid_by_college']);
            }

            $pendingEnrollments = $student->enrollments()->where('status', 'pending')->get();
            if ($pendingEnrollments) {
                foreach ($pendingEnrollments as $pendingEnrollment) {
                    $pendingEnrollment->update(['status' => 'enrolled']);
                }
            }
        }

        // Save the status record
        $status->save();
        $student;
        return redirect()->route('students.show', $student)->with('success', 'Student status updated successfully.');
    }

    public function rejectScholarship(Request $request, Student $student)
    {
        $status = $student->status;

        // Set the scholarship rejected status to 0 (rejected)
        $status->is_scholarship = 0;
        $status->is_scholarship_rejected = 1;
        $status->scholarship_rejected_reason = $request->input('scholarship_rejected_reason', 'No reason provided');
        $status->scholarship_rejected_by_name = Auth::user()->name;
        $status->scholarship_rejected_at = now();

        // Set the scholarship approved status to 0
        $status->is_scholarship_approved = 0;
        $status->scholarship_approved_by_name = null;
        $status->scholarship_approved_at = null;

        // Save the status record
        $status->save();

        return redirect()->route('students.show', $student)->with('success', 'Scholarship request rejected successfully.');
    }
}
