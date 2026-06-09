<?php

namespace App\Enums;

enum UserPermission: string
{
    case DASHBOARD_VIEW = 'dashboard.view';
    case PRODUCTS_VIEW = 'products.view';
    case PRODUCTS_MANAGE = 'products.manage';
    case SUPPLIERS_VIEW = 'suppliers.view';
    case SUPPLIERS_MANAGE = 'suppliers.manage';
    case CLIENTS_VIEW = 'clients.view';
    case CLIENTS_MANAGE = 'clients.manage';
    case ORDERS_VIEW = 'orders.view';
    case ORDERS_MANAGE = 'orders.manage';
    case REPORTS_VIEW = 'reports.view';
    case USERS_VIEW = 'users.view';
    case USERS_MANAGE = 'users.manage';
    case SETTINGS_MANAGE = 'settings.manage';

    public function label(): string
    {
        return match ($this) {
            self::DASHBOARD_VIEW => 'View Dashboard',
            self::PRODUCTS_VIEW => 'View Products',
            self::PRODUCTS_MANAGE => 'Manage Products',
            self::SUPPLIERS_VIEW => 'View Suppliers',
            self::SUPPLIERS_MANAGE => 'Manage Suppliers',
            self::CLIENTS_VIEW => 'View Clients',
            self::CLIENTS_MANAGE => 'Manage Clients',
            self::ORDERS_VIEW => 'View Orders',
            self::ORDERS_MANAGE => 'Manage Orders',
            self::REPORTS_VIEW => 'View Reports',
            self::USERS_VIEW => 'View Users',
            self::USERS_MANAGE => 'Manage Users',
            self::SETTINGS_MANAGE => 'Manage Settings',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::DASHBOARD_VIEW => 'Access owner dashboard summary.',
            self::PRODUCTS_VIEW => 'Open product master listing and detail pages.',
            self::PRODUCTS_MANAGE => 'Create, update, and delete product master records.',
            self::SUPPLIERS_VIEW => 'Open supplier listing and detail pages.',
            self::SUPPLIERS_MANAGE => 'Create, update, and delete suppliers.',
            self::CLIENTS_VIEW => 'Open client listing and detail pages.',
            self::CLIENTS_MANAGE => 'Create, update, and delete clients.',
            self::ORDERS_VIEW => 'Open order listing and detail pages.',
            self::ORDERS_MANAGE => 'Create, update, and delete orders.',
            self::REPORTS_VIEW => 'Access operational and profitability report pages.',
            self::USERS_VIEW => 'Open user listing and profile settings pages.',
            self::USERS_MANAGE => 'Create users and update role, status, or password.',
            self::SETTINGS_MANAGE => 'Manage role and permission assignments.',
        };
    }

    public function module(): string
    {
        return explode('.', $this->value)[0];
    }
}
