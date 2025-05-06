<?php

namespace App\Http\Controllers;

use App\Models\PaymentType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class PaymentTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'type' => 'required|string|max:255',
            'language' => 'nullable|string|max:255',
            'duration' => 'nullable|string|max:255',
            'amount' => 'nullable|numeric|min:0',
            'study_mode_id' => 'nullable|exists:study_modes,id',
            'payment_category_id' => 'nullable|exists:payment_categories,id',
        ]);
        $data['tenant_id'] = Auth::user()->tenant_id;
        $data['created_by'] = Auth::user()->id;

        $paymentType = PaymentType::create($data);

        return redirect()->back()->with('success', 'Payment type created successfully.');
    }

    public function show(PaymentType $paymentType)
    {
        return Inertia::render('PaymentTypes/Show', [
            'category' => $paymentType,
        ]);
    }

    public function update(Request $request, PaymentType $paymentType)
    {
        $data = $request->validate([
            'type' => 'required|string|max:255',
            'language' => 'nullable|string|max:255',
            'duration' => 'nullable|string|max:255',
            'amount' => 'nullable|numeric|min:0',
            'study_mode_id' => 'nullable|exists:study_modes,id',
            'payment_category_id' => 'nullable|exists:payment_categories,id',
        ]);
        $data['updated_by'] = Auth::user()->id;

        $paymentType->update($data);

        return redirect()->back()->with('success', 'Payment type updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PaymentType $paymentType)
    {
        //
    }
}
