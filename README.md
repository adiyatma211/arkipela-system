# Archipela Web

Internal export operating system for Archipela Spice, built with Laravel 12.

This project is focused on internal operations, not external customer-facing documents only. It covers supplier, client, order, document, parameter, dashboard, and reporting workflows for the export team and owner.

## Current Scope

- Supplier management
- Client management / sales pipeline base
- Order management with item-level packaging detail
- Commercial invoice preview
- Packing list preview
- Internal reports with date range and export
- Owner dashboard
- Master parameter management for reusable options

## Main Features

### 1. Orders and Packaging

Order items already support detailed internal fields such as:

- item code
- HS code
- quantity in kg
- quantity in pcs
- quantity unit
- pieces per package
- package count
- inner package
- outer package
- length / width / height
- dimension unit
- net weight
- gross weight
- packaging notes

These fields are used for internal reporting and document generation.

### 2. Document Preview

Available document previews:

- Commercial Invoice
- Packing List

Document templates are prepared for browser print / save as PDF workflow.

### 3. Parameter Master

Reusable parameter master is stored in `arkipela_parameters` and currently used for:

- quantity units
- dimension units
- packaging types
- outer packaging types

Parameter CRUD is available from the UI under:

- `Settings > Parameters`

### 4. Reports

Reports are internal-facing and currently available as submenu pages:

- `Reports > Dashboard`
- `Reports > Orders`
- `Reports > Clients`
- `Reports > Products`

Each report supports:

- date range filter
- export HTML
- export Excel (`.xls` HTML table format)

### 5. Owner Dashboard

Owner dashboard is already connected to live database data for:

- active orders
- revenue pipeline
- confirmed revenue
- gross profit
- client pipeline summary
- active order table
- supplier performance
- risk alerts

Important note:

- Some dashboard cards such as QC / payment related cards currently use proxy logic based on available supplier, order, and document statuses because dedicated QC and payment modules do not exist yet.

## Tech Stack

- PHP `^8.2`
- Laravel `^12.0`
- Blade
- Bootstrap-based admin template
- MySQL or compatible relational database

## Local Setup

### 1. Install dependencies

```bash
composer install
npm install
```

### 2. Prepare environment

```bash
copy .env.example .env
php artisan key:generate
```

Update your database credentials in `.env`.

### 3. Run migration and seeders

```bash
php artisan migrate
php artisan db:seed
```

### 4. Run application

```bash
php artisan serve
npm run dev
```

## Important Seeders

The database seeding currently includes:

- `RoleSeeder`
- `PermissionSeeder`
- `RolePermissionSeeder`
- `UserSeeder`
- `ArkipelaParameterSeeder`
- `SupplierSeeder`
- `ClientSeeder`
- `OrderSeeder`

Useful manual commands:

```bash
php artisan db:seed --class=ArkipelaParameterSeeder
php artisan db:seed --class=OrderSeeder
php artisan db:seed --class=PermissionSeeder
php artisan db:seed --class=RolePermissionSeeder
```

## Demo Accounts

Seeded users include:

- `owner@archipela.test`
- `admin.export@archipela.test`
- `procurement@archipela.test`
- `sales@archipela.test`
- `qc.admin@archipela.test`
- `finance@archipela.test`

Default password:

```text
password
```

## Important Routes

### Core

- `/dashboard`
- `/suppliers`
- `/clients`
- `/orders`

### Reports

- `/reports/dashboard`
- `/reports/orders`
- `/reports/clients`
- `/reports/products`

### Settings

- `/settings/users`
- `/settings/roles`
- `/settings/parameters`

## Recent Implementation Notes

Recent work already added:

- desktop sidebar toggle
- responsive order item form behavior
- settings submenu structure
- reports submenu structure
- internal order report with detailed owner-facing columns
- report exports to HTML and Excel
- richer order seeder data
- commercial invoice and packing list preview improvements
- order item packaging parameterization

## Known Limitations

- PDF output is currently browser print/save based, not server-generated PDF
- Report Excel export still uses HTML table export, not native `.xlsx`
- Dashboard QC and payment cards still rely on status-based proxy logic
- Order report summary is already detailed, but item-per-row report is not separated yet

## Recommended Next Steps

- Add dedicated QC module and tables
- Add dedicated payment / collection module
- Add true Excel export with `.xlsx`
- Add `Order Item Detail Report` for per-item operational analysis
- Add report filters for status, client, supplier, and product
- Add server-side PDF generation if needed

## Testing

Basic test command:

```bash
php artisan test
```

Quick check used during recent updates:

```bash
php artisan test --filter=ExampleTest
```

## Notes for GitHub

This repository is currently closer to an internal MVP / evolving operations system than a finished product. Some parts are already production-shaped, while some others still use temporary internal logic until their dedicated modules are built.
