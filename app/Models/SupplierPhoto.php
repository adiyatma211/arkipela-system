<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplierPhoto extends Model
{
    protected $fillable = [
        'supplier_id',
        'photo_type',
        'file_path',
        'caption',
        'sort_order',
        'uploaded_by',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function photoUrl(): string
    {
        return asset('uploads/' . ltrim(str_replace('\\', '/', $this->file_path), '/'));
    }
}
