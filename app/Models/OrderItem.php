<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'supplier_id',
        'item_code',
        'product_name',
        'hs_code',
        'specification',
        'quantity_kg',
        'quantity_pcs',
        'quantity_unit',
        'pieces_per_package',
        'package_count',
        'package_type',
        'outer_package_type',
        'length_cm',
        'width_cm',
        'height_cm',
        'dimension_unit',
        'net_weight_kg',
        'gross_weight_kg',
        'package_notes',
        'selling_price',
        'buying_price',
        'line_total_sales',
        'line_total_buying',
        'line_profit',
    ];

    protected function casts(): array
    {
        return [
            'quantity_kg' => 'decimal:2',
            'quantity_pcs' => 'integer',
            'selling_price' => 'decimal:2',
            'buying_price' => 'decimal:2',
            'pieces_per_package' => 'integer',
            'package_count' => 'integer',
            'length_cm' => 'decimal:2',
            'width_cm' => 'decimal:2',
            'height_cm' => 'decimal:2',
            'net_weight_kg' => 'decimal:2',
            'gross_weight_kg' => 'decimal:2',
            'line_total_sales' => 'decimal:2',
            'line_total_buying' => 'decimal:2',
            'line_profit' => 'decimal:2',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }
}
