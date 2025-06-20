<?php

namespace App\Http\Controllers;

use App\Http\Resources\StudentResource;
use App\Http\Resources\StudyModeResource;
use App\Http\Resources\PaymentTypeResource; // Import PaymentTypeResource
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\PaymentCategory; // Assuming you have this
use App\Models\PaymentMethod; // Add this line to import PaymentMethod
use App\Models\PaymentType; // Import StudyMode model
use App\Models\Semester;
use App\Models\SemesterStudent;
use App\Models\Student;
use App\Models\StudyMode;
use Illuminate\Http\Request; // Add this line to import StudentResource
use Illuminate\Support\Facades\Auth; // Add this line to import StudyModeResource
use Inertia\Inertia; // Import Auth facade

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $payments = Payment::get();

        $paymentCategories = PaymentCategory::get();
        $paymentMethods = PaymentMethod::get();
        $students = StudentResource::collection(Student::all()->sortBy('name'));
        $studyModes = StudyModeResource::collection(StudyMode::with('programs')->get());
        $paymentTypes = PaymentTypeResource::collection(PaymentType::with(['studyMode'])->get());

        $filters = [
            'category' => $request->input('category'),
            'payment_method' => $request->input('payment_method'),
            'student' => $request->input('student'),
            'schedule' => $request->input('schedule'),
        ];

        return Inertia::render('Payments/Index', [
            'payments' => $payments,
            'paymentCategories' => $paymentCategories,
            'paymentMethods' => $paymentMethods,
            'paymentTypes' => $paymentTypes,
            'students' => $students,
            'filters' => $filters,
            'studyModes' => $studyModes,
        ]);
    }

    public function create(Student $student)
    {
        $paymentCategories = PaymentCategory::all();
        $paymentSchedules = PaymentCategory::all();

        return Inertia::render('Payments/Create', [
            'student' => $student,
            'paymentCategories' => $paymentCategories,
            'paymentSchedules' => $paymentSchedules,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'student_id' => 'required|exists:students,id',
            'payment_method_id' => 'nullable|exists:payment_methods,id',
            'payment_type_id' => 'nullable|exists:payment_types,id',
            'payment_date' => 'required|date',
            'total_amount' => 'required|numeric|min:0.01',
            'paid_amount' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:255',
            'status' => 'required|string|in:pending,completed,canceled',
            'reference_number' => 'nullable|string|max:255',
        ]);
        $student = Student::find($data['student_id']);

        $semester = Semester::getActiveSemester();
        $data['tenant_id'] = Auth::user()->tenant_id;
        $data['created_by'] = Auth::user()->id;
        $data['semester_id'] = $semester->id;

        $status = $data['total_amount'] <= $data['paid_amount'] ? 'completed' : 'pending';
        $data['status'] = $student->status->is_scholarship ? 'paid_by_college' : $status;

        $semesterStudent = $semester->SemesterStudents()->where('student_id', $data['student_id'])->where('payment_status', 'unpaid')->first();

        $selectedPayment = PaymentType::find($data['payment_type_id']);

        $existingCoursePayment = Payment::where('student_id', $data['student_id'])
            ->where('semester_id', $data['semester_id'])
            ->where('payment_type_id', $data['payment_type_id'])
            ->first();

        if ($existingCoursePayment) {
            return redirect()->route('students.show', $request->student_id)->withErrors(['error' => 'This Type Of Payment Is Already Pending Please Finish ' . $existingCoursePayment->paymentType->type]);
        }

        // if payment type is course fee(per-course)
        if ($selectedPayment->duration == 'per-course') {

            if ($semesterStudent) {
                return redirect()->route('students.show', $request->student_id)->withErrors(['error' => 'The Student Needs To Pay Registration Fee Of ' . $semester->year->name . ' Semester ' . $semester->level . ' Before Paying For This Courses']);
            }

            if ($data['total_amount'] == $data['paid_amount'] || $student->status->is_scholarship) {
                $enrollments = $semester->enrollments()->where('student_id', $data['student_id'])->where('status', 'pending')->get();


                foreach ($enrollments as $enrollment) {
                    $enrollment->update([
                        'status' => 'enrolled'
                    ]);
                }
            }
        } else if ($selectedPayment->duration == 'per-semester' && $selectedPayment->type == 'Semester Registration') {

            if (! $semesterStudent) {
                return redirect()->route('students.show', $request->student_id)->withErrors(['error' => 'The Student Has Already Payed All Semester Registration Fees']);
            }
            if ($data['total_amount'] == $data['paid_amount'] || $student->status->is_scholarship) {
                $semesterStudent = $semesterStudent->update([
                    'payment_status' => 'paid'
                ]);
            }
        } else if ($selectedPayment->duration == 'one-time' && $selectedPayment->type == 'Registration Fee') {
            if ($data['total_amount'] == $data['paid_amount'] || $student->status->is_scholarship) {
                $student->status->update([
                    'is_active' => true
                ]);
            }
        }

        $payment = Payment::create($data);

        return redirect()->route('students.show', $request->student_id)->with('success', 'Payment recorded successfully.')->with('reload', true);
    }

    public function show(Payment $payment)
    {
        $payment->load(['student', 'paymentType', 'paymentCategory', 'paymentScheduleItem', 'paymentMethod', 'semester']);

        return Inertia::render('Payments/Show', [
            'payment' => $payment,
        ]);
    }

    public function update(Request $request, Payment $payment)
    {
        $data = $request->validate([
            'payment_method_id' => 'nullable|exists:payment_methods,id',
            'payment_date' => 'required|date',
            'paid_amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:255',
            'payment_reference' => 'nullable|string|max:255',
        ]);

        $student = Student::find($payment->student_id);

        if ($payment->total_amount < $data['paid_amount']) {
            return redirect()->route('students.show', $payment->student->id)->withErrors(['error' => 'The paid amount can not be more than ' . $payment->total_amount]);
        }

        $status = $payment->total_amount == $data['paid_amount'] ? 'completed' : 'pending';
        $data['status'] = $student->status->is_scholarship ? 'paid_by_college' : $status;


        $data['tenant_id'] = Auth::user()->tenant_id;
        $data['updated_by'] = Auth::user()->id;
        $semester = Semester::getActiveSemester();
        $semesterStudent = $semester->SemesterStudents()->where('student_id', $payment->student_id)->where('payment_status', 'unpaid')->first();

        $payment->update($data);
        if ($payment->paymentType->duration == 'per-course') {

            if ($payment->total_amount == $payment->paid_amount  || $student->status->is_scholarship) {
                $enrollments = $semester->enrollments()->where('student_id', $payment->student_id)->where('status', 'pending')->get();

                foreach ($enrollments as $enrollment) {
                    $enrollment->update([
                        'status' => 'enrolled'
                    ]);
                }
            }
        } else if ($payment->paymentType->duration == 'per-semester' && $payment->paymentType->type == 'Semester Registration') {
            if (! $semesterStudent) {
                return back()->withErrors(['error' => 'Not Registered To A Semester']);
            }
            if ($payment->total_amount == $payment->paid_amount || $student->status->is_scholarship) {
                $semesterStudent = $semesterStudent->update([
                    'payment_status' => 'paid'
                ]);
            }
        } else if ($payment->paymentType->duration == 'one-time' && $payment->paymentType->type == 'Registration Fee') {
            if ($payment->total_amount == $payment->paid_amount || $student->status->is_scholarship) {
                $student->status->update([
                    'is_active' => true
                ]);
            }
        }
        return back()->with('success', 'Payment updated successfully.');
    }

    public function destroy(Payment $payment)
    {
        $payment->delete();

        return redirect()->route('payments.index')->with('success', 'Payment deleted successfully.');
    }
}
