<?php

namespace App\Enums;

enum BarcodeType: string
{
    case UPC_A = 'UPC-A';
    case EAN_13 = 'EAN-13';
    case ITF_14 = 'ITF-14';
    case CODE_128 = 'CODE128';

    public function label(): string
    {
        return $this->value;
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function options(): array
    {
        return array_map(
            fn (self $type) => ['value' => $type->value, 'label' => $type->label()],
            self::cases(),
        );
    }
}
