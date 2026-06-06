<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'client_code',
        'company_name',
        'country',
        'city',
        'address',
        'website',
        'pic_name',
        'pic_position',
        'pic_email',
        'pic_whatsapp',
        'interested_products',
        'target_quantity_kg',
        'target_price',
        'currency',
        'preferred_incoterm',
        'preferred_payment_term',
        'status',
        'source',
        'notes',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'target_quantity_kg' => 'decimal:2',
            'target_price' => 'decimal:2',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
