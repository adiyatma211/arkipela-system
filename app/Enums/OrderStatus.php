<?php

namespace App\Enums;

enum OrderStatus: string
{
    case DRAFT = 'draft';
    case QUOTATION = 'quotation';
    case CONFIRMED = 'confirmed';
    case PRODUCTION = 'production';
    case READY_TO_SHIP = 'ready_to_ship';
    case SHIPPED = 'shipped';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::QUOTATION => 'Quotation',
            self::CONFIRMED => 'Confirmed',
            self::PRODUCTION => 'Production',
            self::READY_TO_SHIP => 'Ready to Ship',
            self::SHIPPED => 'Shipped',
            self::COMPLETED => 'Completed',
            self::CANCELLED => 'Cancelled',
        };
    }

    public static function options(): array
    {
        return array_map(
            fn (self $status) => ['value' => $status->value, 'label' => $status->label()],
            self::cases(),
        );
    }

    public static function values(): array
    {
        return array_map(fn (self $status) => $status->value, self::cases());
    }
}
