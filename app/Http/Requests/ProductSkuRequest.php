<?php

namespace App\Http\Requests;

use App\Enums\BarcodeType;
use App\Support\RetailBarcodeFormatter;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductSkuRequest extends FormRequest
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
        $productSkuId = $this->route('productSku')?->id;

        return [
            'variant_name' => ['required', 'string', 'max:255'],
            'brand_name' => ['nullable', 'string', 'max:255'],
            'net_weight' => ['nullable', 'numeric', 'min:0'],
            'weight_unit' => ['nullable', 'string', 'max:20'],
            'sellable_unit' => ['nullable', 'string', 'max:50'],
            'barcode_type' => ['nullable', Rule::in(BarcodeType::values())],
            'gtin' => ['nullable', 'string', 'max:50'],
            'upc' => ['nullable', 'string', 'max:20'],
            'ean' => ['nullable', 'string', 'max:20'],
            'barcode_number' => ['nullable', 'string', 'max:50', Rule::unique('product_skus', 'barcode_number')->ignore($productSkuId)],
            'is_retail_sellable' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'variant_name' => $this->string('variant_name')->trim()->toString(),
            'brand_name' => $this->string('brand_name')->trim()->toString(),
            'weight_unit' => $this->filled('weight_unit')
                ? strtoupper($this->string('weight_unit')->trim()->toString())
                : 'G',
            'sellable_unit' => $this->filled('sellable_unit')
                ? strtoupper($this->string('sellable_unit')->trim()->toString())
                : 'EACH',
            'barcode_type' => RetailBarcodeFormatter::normalizeType($this->input('barcode_type')),
            'gtin' => $this->filled('gtin')
                ? RetailBarcodeFormatter::normalizeDigits($this->string('gtin')->trim()->toString())
                : null,
            'upc' => $this->filled('upc')
                ? RetailBarcodeFormatter::normalizeDigits($this->string('upc')->trim()->toString())
                : null,
            'ean' => $this->filled('ean')
                ? RetailBarcodeFormatter::normalizeDigits($this->string('ean')->trim()->toString())
                : null,
            'barcode_number' => $this->filled('barcode_number')
                ? RetailBarcodeFormatter::normalizeDigits($this->string('barcode_number')->trim()->toString())
                : null,
            'is_retail_sellable' => $this->boolean('is_retail_sellable'),
            'is_active' => $this->boolean('is_active', true),
        ]);
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $resolved = RetailBarcodeFormatter::resolveCanonicalBarcode(
                barcodeType: $this->input('barcode_type'),
                barcodeNumber: $this->input('barcode_number'),
                upc: $this->input('upc'),
                ean: $this->input('ean'),
                gtin: $this->input('gtin'),
            );

            if ($resolved['error']) {
                $validator->errors()->add('barcode_number', $resolved['error']);
            }
        });
    }
}
