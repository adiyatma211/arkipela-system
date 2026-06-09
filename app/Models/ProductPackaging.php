<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductPackaging extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'product_sku_id',
        'level',
        'units_per_pack',
        'barcode_type',
        'gtin',
        'upc',
        'ean',
        'barcode_number',
        'barcode_image_path',
        'length',
        'width',
        'height',
        'dimension_unit',
        'net_weight',
        'gross_weight',
        'is_default_for_level',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'units_per_pack' => 'integer',
            'length' => 'decimal:2',
            'width' => 'decimal:2',
            'height' => 'decimal:2',
            'net_weight' => 'decimal:2',
            'gross_weight' => 'decimal:2',
            'is_default_for_level' => 'boolean',
        ];
    }

    public function productSku(): BelongsTo
    {
        return $this->belongsTo(ProductSku::class);
    }

    public function barcodeImageUrl(): ?string
    {
        return $this->barcode_image_path
            ? asset($this->barcode_image_path)
            : null;
    }
}
