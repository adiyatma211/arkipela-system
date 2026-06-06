<?php

namespace App\Http\Requests;

use App\Enums\SupplierStatus;
use App\Enums\SupplierType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SupplierRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $supplierId = $this->route('supplier')?->id;

        return [
            'supplier_name' => ['required', 'string', 'max:255'],
            'supplier_type' => ['required', Rule::in(SupplierType::values())],
            'pic_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('suppliers', 'email')->ignore($supplierId)],
            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:255'],
            'province' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:255'],
            'products_summary' => ['nullable', 'string', 'max:255'],
            'monthly_capacity_kg' => ['nullable', 'numeric', 'min:0'],
            'minimum_order_kg' => ['nullable', 'numeric', 'min:0'],
            'payment_term' => ['nullable', 'string', 'max:255'],
            'legal_status' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::in(SupplierStatus::values())],
            'notes' => ['nullable', 'string'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'supplier_name' => $this->string('supplier_name')->trim()->toString(),
            'pic_name' => $this->string('pic_name')->trim()->toString(),
            'email' => $this->filled('email') ? strtolower($this->string('email')->trim()->toString()) : null,
            'city' => $this->string('city')->trim()->toString(),
            'province' => $this->string('province')->trim()->toString(),
            'country' => $this->filled('country') ? $this->string('country')->trim()->toString() : 'Indonesia',
            'products_summary' => $this->string('products_summary')->trim()->toString(),
            'payment_term' => $this->string('payment_term')->trim()->toString(),
            'legal_status' => $this->string('legal_status')->trim()->toString(),
        ]);
    }
}
