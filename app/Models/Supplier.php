<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Supplier extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'supplier_code',
        'supplier_name',
        'supplier_type',
        'pic_name',
        'phone',
        'email',
        'address',
        'city',
        'province',
        'country',
        'products_summary',
        'monthly_capacity_kg',
        'minimum_order_kg',
        'payment_term',
        'legal_status',
        'status',
        'notes',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'monthly_capacity_kg' => 'decimal:2',
            'minimum_order_kg' => 'decimal:2',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(SupplierProduct::class)
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function resolvedProducts(): Collection
    {
        $products = $this->relationLoaded('products')
            ? $this->products
            : $this->products()->get();

        if ($products->isNotEmpty()) {
            return $products;
        }

        $names = collect(explode(',', (string) $this->products_summary))
            ->map(fn (string $name) => trim($name))
            ->filter()
            ->values();

        return $names->map(function (string $name, int $index) {
            return new SupplierProduct([
                'product_name' => $name,
                'monthly_capacity_kg' => $index === 0 ? $this->monthly_capacity_kg : null,
                'minimum_order_kg' => $index === 0 ? $this->minimum_order_kg : null,
                'sort_order' => $index,
            ]);
        });
    }

    public function resolvedProductsSummary(): ?string
    {
        $summary = $this->resolvedProducts()
            ->pluck('product_name')
            ->filter()
            ->implode(', ');

        return $summary !== '' ? $summary : null;
    }

    public function resolvedMonthlyCapacityKg(): ?float
    {
        $capacities = $this->resolvedProducts()
            ->pluck('monthly_capacity_kg')
            ->filter(fn ($value) => $value !== null && $value !== '');

        if ($capacities->isNotEmpty()) {
            return (float) $capacities->sum(fn ($value) => (float) $value);
        }

        return $this->monthly_capacity_kg !== null
            ? (float) $this->monthly_capacity_kg
            : null;
    }

    public function resolvedMinimumOrderKg(): ?float
    {
        $minimumOrders = $this->resolvedProducts()
            ->pluck('minimum_order_kg')
            ->filter(fn ($value) => $value !== null && $value !== '');

        if ($minimumOrders->isNotEmpty()) {
            return (float) $minimumOrders->min(fn ($value) => (float) $value);
        }

        return $this->minimum_order_kg !== null
            ? (float) $this->minimum_order_kg
            : null;
    }
}
