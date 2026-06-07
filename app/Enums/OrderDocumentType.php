<?php

namespace App\Enums;

enum OrderDocumentType: string
{
    case COMMERCIAL_INVOICE = 'commercial_invoice';
    case PACKING_LIST = 'packing_list';

    public function label(): string
    {
        return match ($this) {
            self::COMMERCIAL_INVOICE => 'Commercial Invoice',
            self::PACKING_LIST => 'Packing List',
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

    /**
     * @return array<int, self>
     */
    public static function mandatory(): array
    {
        return [
            self::COMMERCIAL_INVOICE,
            self::PACKING_LIST,
        ];
    }
}
