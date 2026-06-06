# Archipela Web Build Workflow

## 1. Tujuan

Dokumen ini menjadi workflow implementasi utama untuk 4 modul awal:

1. Dashboard Owner
2. Supplier Management
3. Client Management / CRM
4. Order Management

Workflow ini mengikuti dua keputusan teknis utama:

- Database utama memakai **MySQL**
- Template UI utama memakai aset dari `public/template`

---

## 2. Keputusan Teknis

### 2.1 Database

Gunakan MySQL sebagai database default project.

Konfigurasi environment:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=archipela_web
DB_USERNAME=root
DB_PASSWORD=
```

Catatan:

- Buat database `archipela_web` terlebih dulu di MySQL lokal
- Gunakan collation `utf8mb4_unicode_ci`
- Tetap gunakan migration Laravel sebagai single source of truth schema

### 2.2 Template UI

Template dasar yang dipakai:

```text
public/template/index.html
public/template/assets
```

Artinya workflow frontend tidak dimulai dari `welcome.blade.php`, tetapi dari ekstraksi layout Mazer-style yang sudah ada di `public/template`.

Yang harus dipisahkan dari template:

- `layouts/app.blade.php`
- `layouts/auth.blade.php`
- `layouts/partials/sidebar.blade.php`
- `layouts/partials/header.blade.php`
- `layouts/partials/footer.blade.php`

Asset yang dipakai:

- `public/template/assets/compiled/css/*`
- `public/template/assets/compiled/js/*`
- `public/template/assets/static/*`
- `public/template/assets/extensions/*`

---

## 3. Prinsip Workflow

Supaya build rapi, urutan kerja harus seperti ini:

1. Selesaikan fondasi data dan auth dulu
2. Jadikan template `public/template` sebagai layout Blade reusable
3. Bangun shared component dulu sebelum CRUD per modul
4. Bangun modul berdasarkan dependency data
5. Dashboard dibangun dari data real modul, bukan dummy final

Dependency modul:

```text
Auth + Roles
-> Shared Layout + Navigation
-> Products Master Minimal
-> Suppliers
-> Clients
-> Orders
-> Dashboard Summary
```

---

## 4. Workflow Implementasi

## Phase 0 - Environment Setup

Checklist:

- Ubah `.env` dan `.env.example` ke MySQL
- Buat database `archipela_web`
- Pastikan extension `pdo_mysql` aktif di PHP
- Jalankan `php artisan key:generate`
- Jalankan `php artisan migrate`

Output phase ini:

- Project siap terkoneksi ke MySQL
- Semua migration berjalan ke database yang benar

---

## Phase 1 - Foundation

Fokus:

- Authentication berbasis session
- Roles dasar
- Shared layout dari template
- Base route dan middleware

Task:

1. Tambah tabel `roles`
2. Tambah kolom role ke `users`
3. Seed role awal:
   - Owner
   - Admin Export
   - Procurement
   - Sales
   - QC Admin
   - Finance
4. Buat login flow internal
5. Ubah route `/` menjadi redirect ke login atau dashboard
6. Pecah `public/template/index.html` menjadi layout Blade utama

Output phase ini:

- User bisa login
- Sidebar dan topbar sudah reusable
- Setiap modul bisa memakai shell layout yang sama

---

## Phase 2 - Shared App Structure

Fokus:

- Standardisasi struktur sebelum CRUD banyak file dibuat

Task:

1. Buat enum/status constants:
   - `SupplierStatus`
   - `ClientStatus`
   - `OrderStatus`
   - `UserRole`
2. Buat service:
   - `CodeGeneratorService`
   - `ActivityLogService`
   - `DashboardService`
3. Buat request validation base untuk setiap modul
4. Buat shared UI partial:
   - flash message
   - page title
   - filter bar
   - empty state
   - status badge

Output phase ini:

- Controller tidak hardcode status
- UI list dan form tetap konsisten antar modul

---

## Phase 3 - Product Master Minimal

Walau modul produk belum jadi fokus utama, order dan supplier tetap butuh referensi produk.

Task:

1. Buat tabel `products`
2. Seed produk rempah dasar:
   - Clove
   - Cinnamon
   - Nutmeg
   - Mace
   - Black Pepper
   - White Pepper
3. Sediakan relasi produk untuk supplier dan order item

Output phase ini:

- Supplier dan order tidak memakai input nama produk bebas

---

## Phase 4 - Supplier Management

Fokus:

- CRUD supplier
- Filter dan status supplier
- Struktur siap dipakai order

Tabel minimum:

- `suppliers`

Field minimum:

- `supplier_code`
- `supplier_name`
- `supplier_type`
- `pic_name`
- `phone`
- `email`
- `city`
- `province`
- `country`
- `products_summary`
- `monthly_capacity_kg`
- `minimum_order_kg`
- `payment_term`
- `legal_status`
- `status`
- `notes`
- `created_by`

Halaman:

1. Supplier List
2. Add Supplier
3. Edit Supplier
4. Supplier Detail

Fitur minimum:

- Search
- Filter status
- Filter supplier type
- Pagination
- Soft delete
- Activity log create/update/delete

Business rule:

- Supplier untuk order hanya boleh dipilih jika status `approved` atau `active`

Output phase ini:

- Procurement bisa mengelola supplier dengan rapi
- Data supplier siap dipakai di modul order

---

## Phase 5 - Client Management / CRM

Fokus:

- CRUD client
- Status pipeline
- Catatan kebutuhan buyer

Tabel minimum:

- `clients`

Field minimum:

- `client_code`
- `company_name`
- `country`
- `city`
- `address`
- `website`
- `pic_name`
- `pic_position`
- `pic_email`
- `pic_whatsapp`
- `interested_products`
- `target_quantity_kg`
- `target_price`
- `currency`
- `preferred_incoterm`
- `preferred_payment_term`
- `status`
- `source`
- `notes`
- `created_by`

Halaman:

1. Client List
2. Add Client
3. Edit Client
4. Client Detail

Fitur minimum:

- Search
- Filter country
- Filter status
- Filter product interest
- Pagination
- Activity log

Output phase ini:

- Sales bisa mengelola buyer pipeline
- Data client siap dipakai saat create order

---

## Phase 6 - Order Management

Fokus:

- Membuat dan memonitor order
- Menghubungkan client, supplier, product, quantity, price

Tabel minimum:

- `orders`
- `order_items`

Field order minimum:

- `order_code`
- `client_id`
- `status`
- `order_date`
- `target_shipment_date`
- `actual_shipment_date`
- `incoterm`
- `payment_term`
- `currency`
- `exchange_rate`
- `total_selling_amount`
- `total_buying_amount`
- `total_additional_cost`
- `gross_profit`
- `gross_margin_percentage`
- `risk_status`
- `notes`
- `created_by`

Field order item minimum:

- `order_id`
- `product_id`
- `supplier_id`
- `quantity_kg`
- `unit`
- `selling_price_per_kg`
- `buying_price_per_kg`
- `selling_total`
- `buying_total`
- `estimated_margin`
- `notes`

Halaman:

1. Order List
2. Create Order
3. Edit Order
4. Order Detail

Fitur minimum:

- Search order
- Filter status
- Filter client
- Filter risk status
- Multi item per order
- Auto calculate totals
- Activity log

Business rule:

- `order_code` generated otomatis
- Supplier yang bisa dipilih hanya `approved` atau `active`
- Total dan margin dihitung server-side

Output phase ini:

- Admin export dan sales bisa membuat order yang valid
- Data order siap dibaca dashboard

---

## Phase 7 - Dashboard Owner

Dashboard dikerjakan setelah supplier, client, dan order sudah memakai data real.

Card minimum:

- Total Active Orders
- Total Revenue Pipeline
- Total Confirmed Revenue
- Estimated Gross Profit
- Total Active Clients
- Total Active Suppliers

Section minimum:

1. Active order table
2. Client pipeline summary
3. Supplier status summary
4. Risk alert list

Sumber data dashboard:

- `orders`
- `order_items`
- `clients`
- `suppliers`

Contoh risk alert awal:

- Margin di bawah 10%
- Target shipment dekat
- Order belum update
- Supplier hold tapi masih terpakai

Output phase ini:

- Owner bisa lihat ringkasan operasional dari 3 modul inti

---

## 5. Struktur Folder yang Disarankan

```text
app/
  Enums/
  Http/
    Controllers/
      Auth/
      DashboardController.php
      SupplierController.php
      ClientController.php
      OrderController.php
    Requests/
      LoginRequest.php
      SupplierRequest.php
      ClientRequest.php
      OrderRequest.php
  Models/
    Role.php
    Product.php
    Supplier.php
    Client.php
    Order.php
    OrderItem.php
    ActivityLog.php
  Services/
    CodeGeneratorService.php
    ActivityLogService.php
    DashboardService.php

resources/views/
  auth/
  dashboard/
  suppliers/
  clients/
  orders/
  layouts/
    partials/
```

---

## 6. Workflow Route

Route utama yang dipakai:

```text
GET  /login
POST /login
POST /logout

GET  /dashboard

GET  /suppliers
GET  /suppliers/create
POST /suppliers
GET  /suppliers/{supplier}
GET  /suppliers/{supplier}/edit
PUT  /suppliers/{supplier}
DELETE /suppliers/{supplier}

GET  /clients
GET  /clients/create
POST /clients
GET  /clients/{client}
GET  /clients/{client}/edit
PUT  /clients/{client}
DELETE /clients/{client}

GET  /orders
GET  /orders/create
POST /orders
GET  /orders/{order}
GET  /orders/{order}/edit
PUT  /orders/{order}
DELETE /orders/{order}
```

---

## 7. Aturan Build Supaya Tetap Rapi

Aturan kerja:

1. Jangan bangun page per page tanpa shared layout
2. Jangan hardcode status di Blade
3. Jangan hitung total margin di frontend saja
4. Jangan buat order sebelum supplier dan client stabil
5. Jangan isi dashboard dengan dummy setelah modul real tersedia
6. Semua list page wajib punya search, filter, pagination
7. Semua create/update/delete wajib dicatat ke `activity_logs`

---

## 8. Definition of Done per Modul

### Dashboard

- Summary card tampil dari data real
- Active order table tampil
- Risk alert muncul

### Supplier

- CRUD jalan
- Search dan filter jalan
- Status badge konsisten

### Client

- CRUD jalan
- Pipeline status bisa diubah
- List dan detail rapi

### Order

- Order bisa dibuat dengan item
- Totals dan margin otomatis dihitung
- Detail order bisa dilihat

---

## 9. Next Build Order

Urutan implementasi yang direkomendasikan:

1. MySQL configuration
2. Roles + users update
3. Template to Blade layout
4. Product seed minimal
5. Supplier module
6. Client module
7. Order module
8. Dashboard module

Urutan ini paling aman karena mengikuti dependency data dan mengurangi rework tampilan.
