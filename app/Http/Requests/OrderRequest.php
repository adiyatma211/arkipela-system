<?php

namespace App\Http\Requests;

use App\Enums\OrderStatus;
use App\Enums\SupplierStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrderRequest extends FormRequest
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
        return [
            'client_id' => ['required', 'exists:clients,id'],
            'destination_country' => ['nullable', 'string', 'max:255'],
            'destination_port' => ['nullable', 'string', 'max:255'],
            'shipment_mode' => ['nullable', 'string', 'max:255'],
            'order_date' => ['required', 'date'],
            'delivery_date' => ['nullable', 'date', 'after_or_equal:order_date'],
            'po_number' => ['nullable', 'string', 'max:255'],
            'currency' => ['nullable', 'string', 'max:20'],
            'incoterm' => ['nullable', 'string', 'max:255'],
            'payment_term' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::in(OrderStatus::values())],
            'local_logistics_cost' => ['nullable', 'numeric', 'min:0'],
            'export_document_cost' => ['nullable', 'numeric', 'min:0'],
            'forwarding_cost' => ['nullable', 'numeric', 'min:0'],
            'freight_cost' => ['nullable', 'numeric', 'min:0'],
            'insurance_cost' => ['nullable', 'numeric', 'min:0'],
            'compliance_cost' => ['nullable', 'numeric', 'min:0'],
            'destination_cost' => ['nullable', 'numeric', 'min:0'],
            'misc_cost' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.supplier_id' => [
                'nullable',
                Rule::exists('suppliers', 'id')->where(fn ($query) => $query->whereIn('status', [
                    SupplierStatus::APPROVED->value,
                    SupplierStatus::ACTIVE->value,
                ])),
            ],
            'items.*.product_name' => ['required', 'string', 'max:255'],
            'items.*.specification' => ['nullable', 'string'],
            'items.*.quantity_kg' => ['required', 'numeric', 'min:0.01'],
            'items.*.selling_price' => ['required', 'numeric', 'min:0'],
            'items.*.buying_price' => ['required', 'numeric', 'min:0'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $rawItems = $this->input('items', []);

        $items = collect(is_array($rawItems) ? $rawItems : [])
            ->map(function ($item) {
                $row = is_array($item) ? $item : [];

                return [
                    'supplier_id' => filled($row['supplier_id'] ?? null) ? $row['supplier_id'] : null,
                    'product_name' => trim((string) ($row['product_name'] ?? '')),
                    'specification' => trim((string) ($row['specification'] ?? '')),
                    'quantity_kg' => $row['quantity_kg'] ?? null,
                    'selling_price' => $row['selling_price'] ?? null,
                    'buying_price' => $row['buying_price'] ?? null,
                ];
            })
            ->filter(fn (array $item) => $item['product_name'] !== '' || filled($item['supplier_id']) || filled($item['quantity_kg']) || filled($item['selling_price']) || filled($item['buying_price']))
            ->values()
            ->all();

        $this->merge([
            'destination_country' => $this->string('destination_country')->trim()->toString(),
            'destination_port' => $this->string('destination_port')->trim()->toString(),
            'shipment_mode' => $this->string('shipment_mode')->trim()->toString(),
            'po_number' => $this->string('po_number')->trim()->toString(),
            'currency' => $this->filled('currency') ? strtoupper($this->string('currency')->trim()->toString()) : 'USD',
            'incoterm' => $this->string('incoterm')->trim()->toString(),
            'payment_term' => $this->string('payment_term')->trim()->toString(),
            'items' => $items,
        ]);
    }
}
