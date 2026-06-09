<?php

namespace App\Http\Requests;

use App\Enums\BarcodeType;
use App\Support\RetailBarcodeFormatter;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductPackagingRequest extends FormRequest
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
        $productPackagingId = $this->route('productPackaging')?->id;

        return [
            'level' => ['required', Rule::in(['each', 'inner', 'case', 'pallet'])],
            'units_per_pack' => ['nullable', 'integer', 'min:1'],
            'barcode_type' => ['nullable', Rule::in(BarcodeType::values())],
            'gtin' => ['nullable', 'string', 'max:50'],
            'upc' => ['nullable', 'string', 'max:20'],
            'ean' => ['nullable', 'string', 'max:20'],
            'barcode_number' => ['nullable', 'string', 'max:50', Rule::unique('product_packagings', 'barcode_number')->ignore($productPackagingId)],
            'length' => ['nullable', 'numeric', 'min:0'],
            'width' => ['nullable', 'numeric', 'min:0'],
            'height' => ['nullable', 'numeric', 'min:0'],
            'dimension_unit' => ['nullable', 'string', 'max:20'],
            'net_weight' => ['nullable', 'numeric', 'min:0'],
            'gross_weight' => ['nullable', 'numeric', 'min:0'],
            'is_default_for_level' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'level' => strtolower($this->string('level')->trim()->toString()),
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
            'dimension_unit' => $this->filled('dimension_unit')
                ? strtoupper($this->string('dimension_unit')->trim()->toString())
                : 'CM',
            'is_default_for_level' => $this->boolean('is_default_for_level'),
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
