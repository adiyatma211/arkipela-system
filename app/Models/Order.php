<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'order_code',
        'client_id',
        'destination_country',
        'destination_port',
        'shipment_mode',
        'order_date',
        'delivery_date',
        'po_number',
        'currency',
        'incoterm',
        'payment_term',
        'status',
        'subtotal_sales',
        'subtotal_buying',
        'gross_profit',
        'gross_margin',
        'local_logistics_cost',
        'export_document_cost',
        'forwarding_cost',
        'freight_cost',
        'insurance_cost',
        'compliance_cost',
        'destination_cost',
        'misc_cost',
        'total_additional_cost',
        'net_profit',
        'net_margin',
        'confirmed_at',
        'notes',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'order_date' => 'date',
            'delivery_date' => 'date',
            'confirmed_at' => 'datetime',
            'subtotal_sales' => 'decimal:2',
            'subtotal_buying' => 'decimal:2',
            'gross_profit' => 'decimal:2',
            'gross_margin' => 'decimal:2',
            'local_logistics_cost' => 'decimal:2',
            'export_document_cost' => 'decimal:2',
            'forwarding_cost' => 'decimal:2',
            'freight_cost' => 'decimal:2',
            'insurance_cost' => 'decimal:2',
            'compliance_cost' => 'decimal:2',
            'destination_cost' => 'decimal:2',
            'misc_cost' => 'decimal:2',
            'total_additional_cost' => 'decimal:2',
            'net_profit' => 'decimal:2',
            'net_margin' => 'decimal:2',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
