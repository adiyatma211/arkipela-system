<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplierProduct extends Model
{
    protected $fillable = [
        'supplier_id',
        'product_id',
        'product_sku_id',
        'product_name',
        'monthly_capacity_kg',
        'minimum_order_kg',
        'lead_time_days',
        'packaging_type',
        'is_active',
        'notes',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'monthly_capacity_kg' => 'decimal:2',
            'minimum_order_kg' => 'decimal:2',
            'lead_time_days' => 'integer',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function productSku(): BelongsTo
    {
        return $this->belongsTo(ProductSku::class, 'product_sku_id');
    }

    public function getProductNameAttribute(?string $value): ?string
    {
        return $value ?: $this->product?->product_name;
    }

    public function skuLabel(): ?string
    {
        if (! $this->productSku) {
            return null;
        }

        return collect([
            $this->productSku->variant_name,
            $this->productSku->sku_code,
        ])->filter()->implode(' | ');
    }
}
