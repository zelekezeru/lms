<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InventoryStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:100',
            'quantity' => 'required|numeric',
            'unit_price' => 'nullable|numeric',
            'description' => 'nullable|string',
            'status' => 'required|string|in:ACTIVE,IN_ACTIVE',
            'tenant_id' => 'nullable|exists:tenants',
            'category_id' => 'required|exists:inventory_categories,id',
            'supplier_id' => 'nullable|exists:inventory_suppliers,id',
        ];
    }
}
