<?php

namespace App\Http\Requests;

use App\Enums\SupplierPhotoType;
use App\Enums\SupplierStatus;
use App\Enums\SupplierType;
use App\Models\ProductSku;
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
            'products' => ['required', 'array', 'min:1'],
            'products.*.product_id' => ['required', 'exists:products,id'],
            'products.*.product_sku_id' => ['nullable', 'exists:product_skus,id'],
            'products.*.monthly_capacity_kg' => ['nullable', 'numeric', 'min:0'],
            'products.*.minimum_order_kg' => ['nullable', 'numeric', 'min:0'],
            'products.*.lead_time_days' => ['nullable', 'integer', 'min:0'],
            'products.*.packaging_type' => ['nullable', 'string', 'max:255'],
            'products.*.is_active' => ['nullable', 'boolean'],
            'products.*.notes' => ['nullable', 'string'],
            'payment_term' => ['nullable', 'string', 'max:255'],
            'legal_status' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::in(SupplierStatus::values())],
            'notes' => ['nullable', 'string'],
            'photos' => ['nullable', 'array', 'max:10'],
            'photos.*.file' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'photos.*.photo_type' => ['required', Rule::in(SupplierPhotoType::values())],
            'photos.*.caption' => ['nullable', 'string', 'max:255'],
            'existing_photos_to_delete' => ['nullable', 'array'],
            'existing_photos_to_delete.*' => [
                'integer',
                Rule::exists('supplier_photos', 'id')->where(fn ($query) => $query->where('supplier_id', $supplierId)),
            ],
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
            'products' => collect($this->input('products', []))
                ->map(function ($product) {
                    return [
                        'product_id' => filled(data_get($product, 'product_id'))
                            ? (int) data_get($product, 'product_id')
                            : null,
                        'product_sku_id' => filled(data_get($product, 'product_sku_id'))
                            ? (int) data_get($product, 'product_sku_id')
                            : null,
                        'monthly_capacity_kg' => filled(data_get($product, 'monthly_capacity_kg'))
                            ? data_get($product, 'monthly_capacity_kg')
                            : null,
                        'minimum_order_kg' => filled(data_get($product, 'minimum_order_kg'))
                            ? data_get($product, 'minimum_order_kg')
                            : null,
                        'lead_time_days' => filled(data_get($product, 'lead_time_days'))
                            ? data_get($product, 'lead_time_days')
                            : null,
                        'packaging_type' => trim((string) data_get($product, 'packaging_type')),
                        'is_active' => filter_var(data_get($product, 'is_active', true), FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE) ?? false,
                        'notes' => trim((string) data_get($product, 'notes')),
                    ];
                })
                ->filter(fn (array $product) => $product['product_id'] !== null
                    || $product['product_sku_id'] !== null
                    || filled($product['monthly_capacity_kg'])
                    || filled($product['minimum_order_kg'])
                    || filled($product['lead_time_days'])
                    || $product['packaging_type'] !== ''
                    || $product['notes'] !== '')
                ->values()
                ->all(),
            'payment_term' => $this->string('payment_term')->trim()->toString(),
            'legal_status' => $this->string('legal_status')->trim()->toString(),
            'photos' => collect($this->input('photos', []))
                ->map(function ($photo) {
                    return [
                        'photo_type' => trim((string) data_get($photo, 'photo_type')),
                        'caption' => trim((string) data_get($photo, 'caption')),
                    ];
                })
                ->all(),
            'existing_photos_to_delete' => collect($this->input('existing_photos_to_delete', []))
                ->filter(fn ($photoId) => filled($photoId))
                ->values()
                ->all(),
        ]);
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            foreach ($this->input('products', []) as $index => $productRow) {
                $productId = data_get($productRow, 'product_id');
                $productSkuId = data_get($productRow, 'product_sku_id');

                if (! $productId || ! $productSkuId) {
                    continue;
                }

                $belongsToProduct = ProductSku::query()
                    ->whereKey($productSkuId)
                    ->where('product_id', $productId)
                    ->exists();

                if (! $belongsToProduct) {
                    $validator->errors()->add(
                        "products.$index.product_sku_id",
                        'Selected SKU does not belong to the chosen product.'
                    );
                }
            }
        });
    }
}
