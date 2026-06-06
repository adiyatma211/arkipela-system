<?php

namespace App\Enums;

enum ClientStatus: string
{
    case LEAD = 'lead';
    case CONTACTED = 'contacted';
    case QUALIFIED = 'qualified';
    case SAMPLE_REQUESTED = 'sample_requested';
    case SAMPLE_SENT = 'sample_sent';
    case QUOTATION_SENT = 'quotation_sent';
    case NEGOTIATION = 'negotiation';
    case PO_RECEIVED = 'po_received';
    case ACTIVE_BUYER = 'active_buyer';
    case REPEAT_BUYER = 'repeat_buyer';
    case LOST = 'lost';
    case INACTIVE = 'inactive';

    public function label(): string
    {
        return match ($this) {
            self::LEAD => 'Lead',
            self::CONTACTED => 'Contacted',
            self::QUALIFIED => 'Qualified',
            self::SAMPLE_REQUESTED => 'Sample Requested',
            self::SAMPLE_SENT => 'Sample Sent',
            self::QUOTATION_SENT => 'Quotation Sent',
            self::NEGOTIATION => 'Negotiation',
            self::PO_RECEIVED => 'PO Received',
            self::ACTIVE_BUYER => 'Active Buyer',
            self::REPEAT_BUYER => 'Repeat Buyer',
            self::LOST => 'Lost',
            self::INACTIVE => 'Inactive',
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
