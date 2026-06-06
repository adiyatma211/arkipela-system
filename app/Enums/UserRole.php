<?php

namespace App\Enums;

enum UserRole: string
{
    case OWNER = 'owner';
    case ADMIN_EXPORT = 'admin-export';
    case PROCUREMENT = 'procurement';
    case SALES = 'sales';
    case QC_ADMIN = 'qc-admin';
    case FINANCE = 'finance';

    public function label(): string
    {
        return match ($this) {
            self::OWNER => 'Owner',
            self::ADMIN_EXPORT => 'Admin Export',
            self::PROCUREMENT => 'Procurement',
            self::SALES => 'Sales',
            self::QC_ADMIN => 'QC Admin',
            self::FINANCE => 'Finance',
        };
    }
}
