<?php

namespace App\Http\Controllers;

use App\Models\PaymentCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|unique:payment_categories|max:255',
            'description' => 'nullable|string|max:255',
            'is_active' => 'required|boolean',
        ]);
        $data['tenant_id'] = Auth::user()->tenant_id;
        $data['created_by'] = Auth::user()->id;

        $paymentCategory = PaymentCategory::create($data);

        return redirect()->back()->with('success', 'Payment category created successfully.');
    }

    public function show(PaymentCategory $paymentCategory)
    {
        return Inertia::render('PaymentCategories/Show', [
            'category' => $paymentCategory,
        ]);
    }

    public function update(Request $request, PaymentCategory $paymentCategory)
    {
        $data = $request->validate([
            'name' => 'required|string|unique:payment_categories,name,'.$paymentCategory->id.'|max:255',
            'description' => 'nullable|string|max:255',
            'is_active' => 'required|boolean',
        ]);
        $data['tenant_id'] = Auth::user()->tenant_id;
        $data['updated_by'] = Auth::user()->id;

        $paymentCategory->update($data);

        return redirect()->back()->with('success', 'Payment category updated successfully.');
    }

    public function destroy(PaymentCategory $paymentCategory)
    {
        $paymentCategory->delete();

        return redirect()->back()->with('success', 'Payment category deleted successfully.');
    }
}
