<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'supplier_id',
        'product_name',
        'specification',
        'quantity_kg',
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
            'selling_price' => 'decimal:2',
            'buying_price' => 'decimal:2',
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
