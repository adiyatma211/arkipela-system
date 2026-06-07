<?php

namespace App\Enums;

enum OrderDocumentStatus: string
{
    case DRAFT = 'draft';
    case GENERATED = 'generated';
    case OUTDATED = 'outdated';
    case VERIFIED = 'verified';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::GENERATED => 'Generated',
            self::OUTDATED => 'Outdated',
            self::VERIFIED => 'Verified',
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
