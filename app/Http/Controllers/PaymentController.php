<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\PaymentCategory;
use App\Models\PaymentScheduleItem;
use App\Models\Student;
use App\Models\PaymentType; // Assuming you have this
use App\Models\PaymentMethod; // Add this line to import PaymentMethod
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Http\Resources\StudentResource; // Add this line to import StudentResource

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $payments = Payment::with(['student', 'paymentType', 'paymentCategory', 'paymentScheduleItem'])
            ->latest()
            ->paginate(10);

            
        $paymentCategories = PaymentCategory::paginate(30);
        $paymentTypes = PaymentType::paginate(30);
        $paymentMethods = PaymentMethod::paginate(30);
        $students = StudentResource::collection(Student::all()->sortBy('name'));

        $filters = [
            'category' => $request->input('category'),
            'payment_type' => $request->input('payment_type'),
            'student' => $request->input('student'),
            'schedule' => $request->input('schedule'),
        ];

        $payments->appends($filters);
        $payments->setPath(route('payments.index'));
        $payments->withQueryString();

        return Inertia::render('Payments/Index', [
            'payments' => $payments,
            'paymentCategories' => $paymentCategories,
            'paymentTypes' => $paymentTypes,
            'paymentMethods' => $paymentMethods,
            'students' => $students,
            'filters' => $filters,
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
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'payment_type_id' => 'required|exists:payment_types,id',
            'payment_category_id' => 'nullable|exists:payment_categories,id',
            'payment_schedule_item_id' => 'nullable|exists:payment_schedule_items,id',
            'payment_date' => 'required|date',
            'total_amount' => 'required|numeric|min:0.01',
            'payment_method' => 'nullable|string|max:255',
            'status' => 'nullable|string|in:pending,completed,failed,refunded',
            'items' => 'nullable|array',
            'items.*.name' => 'required_with:items|string|max:255',
            'items.*.quantity' => 'nullable|integer|min:1',
            'items.*.unit_price' => 'required_with:items|numeric|min:0.01',
        ]);

        $payment = Payment::create($request->except('items'));

        if ($request->has('items')) {
            foreach ($request->input('items') as $itemData) {
                $payment->paymentItems()->create($itemData);
            }
        }

        // Update PaymentScheduleItem's paid amount if linked
        if ($payment->payment_schedule_item_id) {
            $scheduleItem = PaymentScheduleItem::findOrFail($payment->payment_schedule_item_id);
            $scheduleItem->increment('paid_amount', $payment->total_amount);
            $this->updatePaymentScheduleItemStatus($scheduleItem);
        }

        return redirect()->route('students.show', $request->student_id)->with('success', 'Payment recorded successfully.');
    }

    public function show(Payment $payment)
    {
        $payment->load(['student', 'paymentType', 'paymentCategory', 'paymentScheduleItem', 'paymentItems']);
        return Inertia::render('Payments/Show', [
            'payment' => $payment,
        ]);
    }

    public function edit(Payment $payment)
    {
        $payment->load(['student', 'paymentType', 'paymentCategory', 'paymentScheduleItem', 'paymentItems']);
        $paymentTypes = PaymentType::all();
        $paymentCategories = PaymentCategory::all();
        $paymentSchedules = $payment->student->paymentSchedules()->with('items')->get();

        return Inertia::render('Payments/Edit', [
            'payment' => $payment,
            'paymentTypes' => $paymentTypes,
            'paymentCategories' => $paymentCategories,
            'paymentSchedules' => $paymentSchedules,
        ]);
    }

    public function update(Request $request, Payment $payment)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'payment_type_id' => 'required|exists:payment_types,id',
            'payment_category_id' => 'nullable|exists:payment_categories,id',
            'payment_schedule_item_id' => 'nullable|exists:payment_schedule_items,id',
            'payment_date' => 'required|date',
            'total_amount' => 'required|numeric|min:0.01',
            'payment_method' => 'nullable|string|max:255',
            'status' => 'nullable|string|in:pending,completed,failed,refunded',
            'items' => 'nullable|array',
            'items.*.id' => 'nullable|exists:payment_items,id',
            'items.*.name' => 'required_with:items|string|max:255',
            'items.*.quantity' => 'nullable|integer|min:1',
            'items.*.unit_price' => 'required_with:items|numeric|min:0.01',
        ]);

        // Handle Payment updates
        $payment->update($request->except('items'));

        // Handle Payment Items updates
        if ($request->has('items')) {
            // Sync existing and create new items
            $existingItemIds = $payment->paymentItems()->pluck('id')->toArray();
            $submittedItemIds = collect($request->input('items'))->pluck('id')->filter()->toArray();

            // Delete items that were removed
            $payment->paymentItems()->whereNotIn('id', $submittedItemIds)->delete();

            // Update or create items
            foreach ($request->input('items') as $itemData) {
                if (isset($itemData['id'])) {
                    $item = $payment->paymentItems()->findOrFail($itemData['id']);
                    $item->update($itemData);
                } else {
                    $payment->paymentItems()->create($itemData);
                }
            }
        } else {
            // If no items are submitted, delete all existing ones
            $payment->paymentItems()->delete();
        }

        // Recalculate paid amount on PaymentScheduleItem if linked and update status
        if ($payment->payment_schedule_item_id) {
            $scheduleItem = PaymentScheduleItem::findOrFail($payment->payment_schedule_item_id);
            $scheduleItem->paid_amount = $scheduleItem->payments()->sum('total_amount');
            $scheduleItem->save();
            $this->updatePaymentScheduleItemStatus($scheduleItem);
        }

        return redirect()->route('payments.show', $payment->id)->with('success', 'Payment updated successfully.');
    }

    public function destroy(Payment $payment)
    {
        // Optionally handle decrementing paid amount on PaymentScheduleItem
        if ($payment->payment_schedule_item_id) {
            $scheduleItem = PaymentScheduleItem::findOrFail($payment->payment_schedule_item_id);
            $scheduleItem->decrement('paid_amount', $payment->total_amount);
            $this->updatePaymentScheduleItemStatus($scheduleItem);
        }

        $payment->delete();
        return redirect()->route('payments.index')->with('success', 'Payment deleted successfully.');
    }

    protected function updatePaymentScheduleItemStatus(PaymentScheduleItem $item)
    {
        if ($item->paid_amount >= $item->expected_amount) {
            $item->status = 'paid';
        } elseif ($item->paid_amount > 0) {
            $item->status = 'partially_paid';
        } elseif ($item->due_date < now() && $item->paid_amount < $item->expected_amount) {
            $item->status = 'overdue';
        } else {
            $item->status = 'pending';
        }
        $item->save();
    }
}