<?php

namespace App\Enums;

enum SupplierPhotoType: string
{
    case LOCATION = 'location';
    case WAREHOUSE = 'warehouse';
    case PRODUCT = 'product';
    case LEGAL_DOCUMENT = 'legal_document';

    public function label(): string
    {
        return match ($this) {
            self::LOCATION => 'Foto Lokasi Usaha',
            self::WAREHOUSE => 'Foto Gudang / Stok',
            self::PRODUCT => 'Foto Barang / Rempah',
            self::LEGAL_DOCUMENT => 'Foto Dokumen Legalitas',
        };
    }

    public static function options(): array
    {
        return array_map(
            fn (self $type) => ['value' => $type->value, 'label' => $type->label()],
            self::cases(),
        );
    }

    public static function values(): array
    {
        return array_map(fn (self $type) => $type->value, self::cases());
    }
}
