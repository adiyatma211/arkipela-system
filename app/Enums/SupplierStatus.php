<?php

namespace App\Enums;

enum SupplierStatus: string
{
    case PROSPECT = 'prospect';
    case CONTACTED = 'contacted';
    case SAMPLE_REQUESTED = 'sample_requested';
    case SAMPLE_RECEIVED = 'sample_received';
    case QC_CHECKING = 'qc_checking';
    case APPROVED = 'approved';
    case ACTIVE = 'active';
    case HOLD = 'hold';
    case REJECTED = 'rejected';
    case BLACKLISTED = 'blacklisted';

    public function label(): string
    {
        return match ($this) {
            self::PROSPECT => 'Prospect',
            self::CONTACTED => 'Contacted',
            self::SAMPLE_REQUESTED => 'Sample Requested',
            self::SAMPLE_RECEIVED => 'Sample Received',
            self::QC_CHECKING => 'QC Checking',
            self::APPROVED => 'Approved',
            self::ACTIVE => 'Active',
            self::HOLD => 'Hold',
            self::REJECTED => 'Rejected',
            self::BLACKLISTED => 'Blacklisted',
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
