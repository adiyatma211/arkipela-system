<?php

namespace App\Http\Requests;

use App\Enums\OrderStatus;
use App\Enums\SupplierApprovalStatus;
use App\Models\Supplier;
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
                'required',
                Rule::exists('suppliers', 'id')->where(fn ($query) => $query
                    ->where('approval_status', SupplierApprovalStatus::APPROVED->value)),
            ],
            'items.*.item_code' => ['nullable', 'string', 'max:255'],
            'items.*.product_name' => ['required', 'string', 'max:255'],
            'items.*.hs_code' => ['nullable', 'string', 'max:255'],
            'items.*.specification' => ['nullable', 'string'],
            'items.*.quantity_kg' => ['required', 'numeric', 'min:0.01'],
            'items.*.quantity_pcs' => ['nullable', 'integer', 'min:1'],
            'items.*.quantity_unit' => ['nullable', 'string', 'max:20'],
            'items.*.pieces_per_package' => ['nullable', 'integer', 'min:1'],
            'items.*.package_count' => ['nullable', 'integer', 'min:1'],
            'items.*.package_type' => ['nullable', 'string', 'max:255'],
            'items.*.outer_package_type' => ['nullable', 'string', 'max:255'],
            'items.*.length_cm' => ['nullable', 'numeric', 'min:0'],
            'items.*.width_cm' => ['nullable', 'numeric', 'min:0'],
            'items.*.height_cm' => ['nullable', 'numeric', 'min:0'],
            'items.*.dimension_unit' => ['nullable', 'string', 'max:20'],
            'items.*.net_weight_kg' => ['nullable', 'numeric', 'min:0'],
            'items.*.gross_weight_kg' => ['nullable', 'numeric', 'min:0'],
            'items.*.package_notes' => ['nullable', 'string'],
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
                    'item_code' => trim((string) ($row['item_code'] ?? '')),
                    'product_name' => trim((string) ($row['product_name'] ?? '')),
                    'hs_code' => trim((string) ($row['hs_code'] ?? '')),
                    'specification' => trim((string) ($row['specification'] ?? '')),
                    'quantity_kg' => $row['quantity_kg'] ?? null,
                    'quantity_pcs' => $row['quantity_pcs'] ?? null,
                    'quantity_unit' => strtoupper(trim((string) ($row['quantity_unit'] ?? 'PCS'))),
                    'pieces_per_package' => $row['pieces_per_package'] ?? null,
                    'package_count' => $row['package_count'] ?? null,
                    'package_type' => trim((string) ($row['package_type'] ?? '')),
                    'outer_package_type' => trim((string) ($row['outer_package_type'] ?? '')),
                    'length_cm' => $row['length_cm'] ?? null,
                    'width_cm' => $row['width_cm'] ?? null,
                    'height_cm' => $row['height_cm'] ?? null,
                    'dimension_unit' => strtoupper(trim((string) ($row['dimension_unit'] ?? 'CM'))),
                    'net_weight_kg' => $row['net_weight_kg'] ?? null,
                    'gross_weight_kg' => $row['gross_weight_kg'] ?? null,
                    'package_notes' => trim((string) ($row['package_notes'] ?? '')),
                    'selling_price' => $row['selling_price'] ?? null,
                    'buying_price' => $row['buying_price'] ?? null,
                ];
            })
            ->filter(fn (array $item) => $item['product_name'] !== ''
                || filled($item['supplier_id'])
                || filled($item['item_code'])
                || filled($item['hs_code'])
                || filled($item['quantity_kg'])
                || filled($item['quantity_pcs'])
                || filled($item['selling_price'])
                || filled($item['buying_price']))
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

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $items = $this->input('items', []);
            $supplierIds = collect($items)
                ->pluck('supplier_id')
                ->filter()
                ->unique()
                ->values();

            if ($supplierIds->isEmpty()) {
                return;
            }

            $suppliers = Supplier::query()
                ->with('products')
                ->whereIn('id', $supplierIds)
                ->get()
                ->keyBy('id');

            foreach ($items as $index => $item) {
                $supplierId = data_get($item, 'supplier_id');
                $productName = trim((string) data_get($item, 'product_name'));

                if (! $supplierId || $productName === '') {
                    continue;
                }

                $supplier = $suppliers->get((int) $supplierId);
                $validProducts = $supplier?->resolvedProducts()
                    ->pluck('product_name')
                    ->filter()
                    ->map(fn (string $name) => trim($name))
                    ->all() ?? [];

                if (! in_array($productName, $validProducts, true)) {
                    $validator->errors()->add(
                        "items.$index.product_name",
                        'Selected product is not available for the chosen supplier.'
                    );
                }

                $netWeight = data_get($item, 'net_weight_kg');
                $grossWeight = data_get($item, 'gross_weight_kg');

                if (filled($netWeight) && filled($grossWeight) && (float) $grossWeight < (float) $netWeight) {
                    $validator->errors()->add(
                        "items.$index.gross_weight_kg",
                        'Gross weight must be greater than or equal to net weight.'
                    );
                }
            }
        });
    }
}
