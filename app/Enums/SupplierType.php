<?php

namespace App\Enums;

enum SupplierType: string
{
    case FARMER = 'farmer';
    case COLLECTOR = 'collector';
    case COOPERATIVE = 'cooperative';
    case FACTORY = 'factory';
    case TRADER = 'trader';
    case EXPORTER_PARTNER = 'exporter_partner';

    public function label(): string
    {
        return match ($this) {
            self::FARMER => 'Farmer',
            self::COLLECTOR => 'Collector',
            self::COOPERATIVE => 'Cooperative',
            self::FACTORY => 'Factory',
            self::TRADER => 'Trader',
            self::EXPORTER_PARTNER => 'Exporter Partner',
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
