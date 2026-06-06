<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

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
}
