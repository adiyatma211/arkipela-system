<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplierProduct extends Model
{
    protected $fillable = [
        'supplier_id',
        'product_name',
        'monthly_capacity_kg',
        'minimum_order_kg',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'monthly_capacity_kg' => 'decimal:2',
            'minimum_order_kg' => 'decimal:2',
            'sort_order' => 'integer',
        ];
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }
}
