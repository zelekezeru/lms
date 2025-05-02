<?php

namespace App\Http\Controllers;

use App\Models\PaymentCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PaymentCategoryController extends Controller
{
    public function index()
    {
        $categories = PaymentCategory::latest()->paginate(10);
        return Inertia::render('PaymentCategories/Index', [
            'categories' => $categories,
        ]);
    }

    public function create()
    {
        return Inertia::render('PaymentCategories/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:payment_categories|max:255',
            'description' => 'nullable|string|max:255',
        ]);

        PaymentCategory::create($request->all());
        return redirect()->route('payment-categories.index')->with('success', 'Payment category created successfully.');
    }

    public function show(PaymentCategory $paymentCategory)
    {
        return Inertia::render('PaymentCategories/Show', [
            'category' => $paymentCategory,
        ]);
    }

    public function edit(PaymentCategory $paymentCategory)
    {
        return Inertia::render('PaymentCategories/Edit', [
            'category' => $paymentCategory,
        ]);
    }

    public function update(Request $request, PaymentCategory $paymentCategory)
    {
        $request->validate([
            'name' => 'required|string|unique:payment_categories,name,' . $paymentCategory->id . '|max:255',
            'description' => 'nullable|string|max:255',
        ]);

        $paymentCategory->update($request->all());
        return redirect()->route('payment-categories.index')->with('success', 'Payment category updated successfully.');
    }

    public function destroy(PaymentCategory $paymentCategory)
    {
        $paymentCategory->delete();
        return redirect()->route('payment-categories.index')->with('success', 'Payment category deleted successfully.');
    }
}