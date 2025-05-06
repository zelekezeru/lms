<?php

namespace App\Http\Controllers;

use App\Models\PaymentSchedule;
use App\Models\Student;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PaymentScheduleController extends Controller
{
    public function index(Student $student)
    {
        $schedules = $student->paymentSchedules()->with('items')->latest()->paginate(10);

        return Inertia::render('PaymentSchedules/Index', [
            'student' => $student,
            'schedules' => $schedules,
        ]);
    }

    public function create(Student $student)
    {
        return Inertia::render('PaymentSchedules/Create', [
            'student' => $student,
        ]);
    }

    public function store(Request $request, Student $student)
    {
        $request->validate([
            'description' => 'nullable|string|max:255',
            'total_expected_amount' => 'required|numeric|min:0.01',
            'items' => 'required|array|min:1',
            'items.*.name' => 'nullable|string|max:255',
            'items.*.due_date' => 'nullable|date',
            'items.*.expected_amount' => 'required|numeric|min:0.01',
        ]);

        $schedule = $student->paymentSchedules()->create($request->only(['description', 'total_expected_amount']));

        foreach ($request->input('items') as $itemData) {
            $schedule->items()->create($itemData);
        }

        return redirect()->route('students.show', $student->id)->with('success', 'Payment schedule created successfully.');
    }

    public function show(PaymentSchedule $paymentSchedule)
    {
        $paymentSchedule->load(['student', 'items.payments']);

        return Inertia::render('PaymentSchedules/Show', [
            'schedule' => $paymentSchedule,
        ]);
    }

    public function edit(PaymentSchedule $paymentSchedule)
    {
        $paymentSchedule->load('items');

        return Inertia::render('PaymentSchedules/Edit', [
            'schedule' => $paymentSchedule,
        ]);
    }

    public function update(Request $request, PaymentSchedule $paymentSchedule)
    {
        $request->validate([
            'description' => 'nullable|string|max:255',
            'total_expected_amount' => 'required|numeric|min:0.01',
            'items' => 'required|array|min:1',
            'items.*.id' => 'nullable|exists:payment_schedule_items,id',
            'items.*.name' => 'nullable|string|max:255',
            'items.*.due_date' => 'nullable|date',
            'items.*.expected_amount' => 'required|numeric|min:0.01',
        ]);
        $paymentSchedule->update($request->only(['description', 'total_expected_amount']));

        // Sync Payment Schedule Items
        $existingItemIds = $paymentSchedule->items()->pluck('id')->toArray();
        $submittedItemIds = collect($request->input('items'))->pluck('id')->filter()->toArray();

        // Delete items that were removed
        $paymentSchedule->items()->whereNotIn('id', $submittedItemIds)->delete();

        // Update or create items
        foreach ($request->input('items') as $itemData) {
            if (isset($itemData['id'])) {
                $item = $paymentSchedule->items()->findOrFail($itemData['id']);
                $item->update($itemData);
            } else {
                $paymentSchedule->items()->create($itemData);
            }
        }

        return redirect()->route('payment-schedules.show', $paymentSchedule->id)->with('success', 'Payment schedule updated successfully.');
    }

    public function destroy(PaymentSchedule $paymentSchedule)
    {
        $studentId = $paymentSchedule->student_id;
        $paymentSchedule->delete();

        return redirect()->route('students.show', $studentId)->with('success', 'Payment schedule deleted successfully.');
    }
}
