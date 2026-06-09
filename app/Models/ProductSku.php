<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductSku extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'product_id',
        'sku_code',
        'variant_name',
        'brand_name',
        'net_weight',
        'weight_unit',
        'sellable_unit',
        'barcode_type',
        'gtin',
        'upc',
        'ean',
        'barcode_number',
        'barcode_image_path',
        'is_retail_sellable',
        'is_active',
        'notes',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'net_weight' => 'decimal:2',
            'is_retail_sellable' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function packagings(): HasMany
    {
        return $this->hasMany(ProductPackaging::class)
            ->orderBy('level')
            ->orderBy('id');
    }

    public function barcodeImageUrl(): ?string
    {
        return $this->barcode_image_path
            ? asset($this->barcode_image_path)
            : null;
    }
}
