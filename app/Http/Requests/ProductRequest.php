<?php

namespace App\Http\Requests;

use App\Enums\ProductStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
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
        $productId = $this->route('product')?->id;

        return [
            'product_name' => ['required', 'string', 'max:255', Rule::unique('products', 'product_name')->ignore($productId)],
            'category' => ['nullable', 'string', 'max:255'],
            'scientific_name' => ['nullable', 'string', 'max:255'],
            'origin_area' => ['nullable', 'string', 'max:255'],
            'form' => ['nullable', 'string', 'max:255'],
            'default_unit' => ['nullable', 'string', 'max:20'],
            'status' => ['required', Rule::in(ProductStatus::values())],
            'notes' => ['nullable', 'string'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'product_name' => $this->string('product_name')->trim()->toString(),
            'category' => $this->string('category')->trim()->toString(),
            'scientific_name' => $this->string('scientific_name')->trim()->toString(),
            'origin_area' => $this->string('origin_area')->trim()->toString(),
            'form' => $this->string('form')->trim()->toString(),
            'default_unit' => $this->filled('default_unit')
                ? strtoupper($this->string('default_unit')->trim()->toString())
                : 'KG',
        ]);
    }
}
