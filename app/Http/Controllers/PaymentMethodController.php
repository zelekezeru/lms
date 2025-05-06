<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class PaymentMethodController extends Controller
{
    public function index()
    {
        $methods = PaymentMethod::latest()->paginate(10);

        return Inertia::render('PaymentCategories/Index', [
            'methods' => $methods,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'payment_type' => 'required|string|max:255',
            'bank_name' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:255',
            'is_active' => 'required|boolean',
        ]);
        $data['tenant_id'] = Auth::user()->tenant_id;
        $data['created_by'] = Auth::user()->id;

        $paymentMethod = PaymentMethod::create($data);

        return redirect()->back()->with('success', 'Payment category created successfully.');
    }

    public function show(PaymentMethod $paymentMethod)
    {
        return Inertia::render('PaymentCategories/Show', [
            'category' => $paymentMethod,
        ]);
    }

    public function update(Request $request, PaymentMethod $paymentMethod)
    {
        $data = $request->validate([
            'payment_type' => 'required|string|max:255',
            'bank_name' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:255',
            'is_active' => 'required|boolean',
        ]);
        $data['updated_by'] = Auth::user()->id;

        $paymentMethod->update($data);

        return redirect()->back()->with('success', 'Payment category updated successfully.');
    }

    public function destroy(PaymentMethod $paymentMethod)
    {
        $paymentMethod->delete();

        return redirect()->back()->with('success', 'Payment category deleted successfully.');
    }
}
