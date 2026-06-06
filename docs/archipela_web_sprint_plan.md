# Archipela Web Sprint Plan

## 1. Tujuan

Dokumen ini memecah workflow build Archipela Web menjadi sprint yang rapi dan bisa langsung dipakai sebagai acuan implementasi.

Fokus utama MVP:

1. Dashboard Owner
2. Supplier Management
3. Client Management / CRM
4. Order Management

Dokumen referensi utama:

- [PRD](./archipela_web_system_prd.md)
- [Build Workflow](./archipela_web_build_workflow.md)

---

## 2. Asumsi Sprint

- Stack: Laravel 12, Blade, MySQL
- Template UI: `public/template`
- Durasi sprint yang disarankan: `1 minggu per sprint`
- Setiap sprint harus menghasilkan fitur yang bisa dibuka dan diuji di browser

---

## 3. Sprint Overview

| Sprint | Fokus | Outcome Utama |
| --- | --- | --- |
| Sprint 0 | Setup dan fondasi | Project siap pakai di MySQL dan punya layout dasar |
| Sprint 1 | Auth, roles, shared layout | User bisa login dan masuk ke app shell |
| Sprint 2 | Supplier Management | CRUD supplier stabil dan siap dipakai order |
| Sprint 3 | Client Management / CRM | CRUD client dan pipeline buyer berjalan |
| Sprint 4 | Order Management | Order dan order item bisa dibuat dan dipantau |
| Sprint 5 | Dashboard Owner | Ringkasan bisnis tampil dari data real |
| Sprint 6 | Hardening MVP | Validasi, polish UI, smoke test, dan cleanup |

---

## 4. Detail Sprint

## Sprint 0 - Setup dan Fondasi

### Goal

Menyiapkan project agar siap dibangun dengan MySQL dan template internal.

### Scope

- Konfigurasi `.env` dan `.env.example` ke MySQL
- Verifikasi koneksi MySQL lokal
- Siapkan database `archipela_web`
- Pastikan `pdo_mysql` aktif
- Siapkan struktur docs kerja
- Audit template `public/template`

### Deliverables

- Konfigurasi environment siap MySQL
- Dokumen workflow build tersedia
- Dokumen sprint plan tersedia

### Acceptance Criteria

- App bisa membaca konfigurasi MySQL
- Team punya referensi workflow dan sprint yang jelas

### Risks

- MySQL server belum aktif
- Credential lokal tiap developer berbeda

---

## Sprint 1 - Auth, Roles, Shared Layout

### Goal

Membangun fondasi aplikasi internal yang aman dan reusable sebelum CRUD modul utama dimulai.

### Scope

- Tabel `roles`
- Tambah relasi role ke `users`
- Seeder roles dan user awal
- Login dan logout berbasis session
- Middleware auth
- Route group internal app
- Konversi `public/template/index.html` menjadi Blade layout
- Buat partial:
  - sidebar
  - header
  - footer
  - flash message
  - page heading

### Deliverables

- Halaman login
- Layout aplikasi internal
- Sidebar dengan menu:
  - Dashboard
  - Suppliers
  - Clients
  - Orders

### Acceptance Criteria

- User bisa login
- User yang belum login tidak bisa akses halaman internal
- Semua halaman internal memakai layout Blade yang sama

### Dependency

- Sprint 0 selesai

### Risks

- Template HTML perlu dipotong manual agar tidak berantakan
- Asset path dari `public/template/assets` harus konsisten

---

## Sprint 2 - Supplier Management

### Goal

Membangun modul supplier yang stabil sebagai fondasi procurement dan order assignment.

### Scope

- Migration `suppliers`
- Model `Supplier`
- Enum atau constants untuk status supplier
- `SupplierController`
- `SupplierRequest`
- Halaman:
  - supplier list
  - add supplier
  - edit supplier
  - supplier detail
- Search
- Filter status
- Filter type
- Pagination
- Soft delete
- Activity log create, update, delete

### Field Minimum

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

### Deliverables

- Supplier CRUD end-to-end
- List page yang bisa difilter
- Detail supplier yang bisa dilihat procurement dan owner

### Acceptance Criteria

- Procurement bisa tambah, edit, lihat, dan hapus supplier
- Kode supplier generated otomatis
- Status supplier tampil sebagai badge yang konsisten

### Dependency

- Sprint 1 selesai

### Risks

- Kalau produk belum tersedia, field produk harus dibuat sebagai summary dulu
- Rule approval supplier perlu dijaga untuk order sprint berikutnya

---

## Sprint 3 - Client Management / CRM

### Goal

Membangun modul client untuk mengelola buyer pipeline dari lead sampai active buyer.

### Scope

- Migration `clients`
- Model `Client`
- Enum atau constants untuk pipeline status client
- `ClientController`
- `ClientRequest`
- Halaman:
  - client list
  - add client
  - edit client
  - client detail
- Search
- Filter country
- Filter status
- Filter product interest
- Pagination
- Activity log

### Field Minimum

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

### Deliverables

- Client CRUD end-to-end
- Pipeline status management
- Client detail page untuk owner dan sales

### Acceptance Criteria

- Sales bisa tambah, edit, lihat, dan hapus client
- Kode client generated otomatis
- Status pipeline bisa dipantau dari list page

### Dependency

- Sprint 1 selesai

### Risks

- Kalau follow up timeline belum dibangun, client detail harus tetap rapi tanpa fitur itu
- Product interest bisa sementara berbentuk text sebelum relasi produk penuh dibuat

---

## Sprint 4 - Order Management

### Goal

Membangun modul order sebagai pusat transaksi yang menghubungkan client dan supplier.

### Scope

- Migration `products` minimal
- Seeder produk rempah dasar
- Migration `orders`
- Migration `order_items`
- Model `Order`
- Model `OrderItem`
- Enum atau constants status order
- `OrderController`
- `OrderRequest`
- Halaman:
  - order list
  - create order
  - edit order
  - order detail
- Search
- Filter status
- Filter client
- Filter risk
- Input multi item
- Kalkulasi total dan margin server-side
- Activity log

### Business Rules

- `order_code` generated otomatis
- Client harus valid
- Supplier hanya bisa dipilih jika `approved` atau `active`
- Total selling, total buying, gross profit, dan gross margin dihitung server-side

### Deliverables

- Order CRUD end-to-end
- Order item per produk
- Perhitungan margin dasar

### Acceptance Criteria

- Admin export atau sales bisa membuat order
- Order bisa memiliki satu atau lebih item
- Margin tampil di list dan detail

### Dependency

- Sprint 2 selesai
- Sprint 3 selesai

### Risks

- Multi item form bisa rumit jika tanpa komponen frontend sederhana
- Perlu disiplin validasi agar supplier inactive tidak bisa dipilih

---

## Sprint 5 - Dashboard Owner

### Goal

Menyajikan ringkasan operasional bisnis dari data nyata supplier, client, dan order.

### Scope

- `DashboardController`
- `DashboardService`
- Summary cards
- Active order table
- Client pipeline summary
- Supplier summary
- Risk alert list

### Card Minimum

- Total Active Orders
- Total Revenue Pipeline
- Total Confirmed Revenue
- Estimated Gross Profit
- Total Active Clients
- Total Active Suppliers

### Risk Alert Minimum

- Margin di bawah 10%
- Target shipment mendekat
- Supplier tidak valid masih terpakai
- Order tidak update dalam periode tertentu

### Deliverables

- Dashboard owner berbasis data real
- KPI utama langsung terlihat setelah login

### Acceptance Criteria

- Dashboard tidak memakai dummy data
- Angka summary sesuai isi database
- Owner bisa melihat daftar order aktif dan alert utama

### Dependency

- Sprint 2 selesai
- Sprint 3 selesai
- Sprint 4 selesai

### Risks

- Definisi metrik harus konsisten sejak awal
- Query dashboard bisa berat kalau eager loading tidak rapi

---

## Sprint 6 - Hardening MVP

### Goal

Merapikan aplikasi agar siap dipakai internal dan lebih aman untuk lanjut ke fase berikutnya.

### Scope

- Review validasi form
- Review authorization per role
- Review navigation dan UX
- Tambah empty state dan error state
- Rapikan badge status dan table actions
- Smoke test CRUD utama
- Cleanup controller dan view
- Rapikan seed data awal

### Deliverables

- MVP lebih stabil
- Bug utama dari sprint sebelumnya ditutup
- Dokumentasi next phase lebih jelas

### Acceptance Criteria

- Login, supplier, client, order, dan dashboard bisa diuji end-to-end
- Tidak ada blocker besar di flow utama
- Struktur kode cukup rapi untuk masuk fase QC dan shipment berikutnya

### Dependency

- Sprint 1 sampai Sprint 5 selesai

### Risks

- Scope bugfix bisa melebar jika sprint sebelumnya terlalu longgar

---

## 5. Prioritas Backlog per Modul

### Dashboard

- Summary cards
- Active order table
- Risk alert list
- Pipeline summary

### Supplier

- CRUD
- Search dan filter
- Status badge
- Detail page

### Client

- CRUD
- Pipeline status
- Detail page
- Filter product interest

### Order

- CRUD
- Multi item
- Margin calculation
- Risk status

---

## 6. Definition of Done MVP

MVP dianggap siap ketika:

- User bisa login dan masuk ke dashboard
- Supplier bisa di-create, di-edit, difilter, dan dilihat detailnya
- Client bisa di-create, di-edit, difilter, dan dilihat detailnya
- Order bisa dibuat dengan item, supplier, harga, dan quantity
- Margin order dihitung otomatis
- Dashboard owner tampil dari data real

---

## 7. Rekomendasi Eksekusi

Urutan kerja paling aman:

1. Selesaikan Sprint 1 sampai benar-benar stabil
2. Kerjakan Sprint 2 dan Sprint 3 paralel hanya jika layout dan shared component sudah matang
3. Mulai Sprint 4 setelah supplier dan client sudah stabil
4. Kerjakan Sprint 5 hanya ketika order sudah memakai data real
5. Sisakan Sprint 6 khusus untuk hardening, bukan fitur baru

---

## 8. Catatan Teknis

- Semua list page wajib punya search, filter, dan pagination
- Semua create, update, delete wajib masuk activity log
- Jangan hardcode label status langsung di banyak Blade
- Gunakan service untuk code generation dan dashboard summary
- Gunakan MySQL sebagai source utama selama development
