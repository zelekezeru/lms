<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\PaymentCategory;
use App\Models\PaymentScheduleItem;
use App\Models\Student;
use App\Models\PaymentType; // Assuming you have this
use App\Models\PaymentMethod; // Add this line to import PaymentMethod
use App\Models\StudyMode; // Import StudyMode model
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Http\Resources\StudentResource; // Add this line to import StudentResource
use App\Http\Resources\StudyModeResource; // Add this line to import StudyModeResource
use Illuminate\Support\Facades\Auth; // Import Auth facade

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $payments = Payment::get();
        
        $paymentCategories = PaymentCategory::get();
        $paymentMethods = PaymentMethod::get();
        $students = StudentResource::collection(Student::all()->sortBy('name'));
        $studyModes = StudyModeResource::collection(StudyMode::with('program')->orderBy('mode')->get());
        $paymentTypes = PaymentType::all();

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
            'payment_category_id' => 'nullable|exists:payment_categories,id',
            'payment_date' => 'required|date',
            'total_amount' => 'required|numeric|min:0.01',
            'narration' => 'nullable|string|max:255',
            'status' => 'nullable|string|in:pending,completed,failed,refunded',
            'payment_reference' => 'nullable|string|max:255',            
        ]);
        $data['tenant_id'] = Auth::user()->tenant_id;
        $data['created_by'] =  Auth::user()->id;

        $payment = Payment::create($data);

        return redirect()->route('students.show', $request->student_id)->with('success', 'Payment recorded successfully.');
    }

    public function show(Payment $payment)
    {
        $payment->load(['student', 'paymentType', 'paymentCategory', 'paymentScheduleItem', 'paymentItems']);
        return Inertia::render('Payments/Show', [
            'payment' => $payment,
        ]);
    }

    public function update(Request $request, Payment $payment)
    {
        $data = $request->validate([
            'student_id' => 'required|exists:students,id',
            'payment_method_id' => 'nullable|exists:payment_methods,id',
            'payment_category_id' => 'nullable|exists:payment_categories,id',
            'payment_date' => 'required|date',
            'total_amount' => 'required|numeric|min:0.01',
            'narration' => 'nullable|string|max:255',
            'status' => 'nullable|string|in:pending,completed,failed,refunded',
            'payment_reference' => 'nullable|string|max:255',            
        ]);
        $data['tenant_id'] = Auth::user()->tenant_id;
        $data['created_by'] =  Auth::user()->id;
        
        $payment->update($data);

        return redirect()->route('payments.show', $payment->id)->with('success', 'Payment updated successfully.');
    }

    public function destroy(Payment $payment)
    {
        $payment->delete();
        return redirect()->route('payments.index')->with('success', 'Payment deleted successfully.');
    }

}