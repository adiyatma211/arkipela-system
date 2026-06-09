# PB0 Schema Draft for Product, SKU, and Barcode Module

## 1. Tujuan

Dokumen ini adalah deliverable `PB0` untuk track `Product + SKU + Barcode Retail`.

Fokus dokumen:

- memotret kondisi codebase saat ini
- menetapkan batas ownership antar modul
- mendefinisikan entity dan relasi target
- menyusun migration sequence yang aman
- menyusun backfill strategy untuk data existing
- mengurangi risiko bug saat nanti masuk `PB1`

Referensi utama:

- [Product Barcode Sprint Plan](./product_barcode_sprint_plan.md)
- [PRD](./archipela_web_system_prd.md)

---

## 2. Current State Audit

### 2.1 Product Master

Kondisi saat ini:

- [app/Models/Product.php](E:/KERJAAN%20HARIS/SentosakuTechDev/Development/ArchipleSpice/arkipela-system/app/Models/Product.php:1) sudah ada tetapi masih kosong
- belum ada migration `products`
- belum ada route CRUD product
- belum ada permission khusus product module
- belum ada menu product di sidebar

Kesimpulan:

- `Product` secara domain sudah direncanakan
- `Product` secara implementasi belum aktif

### 2.2 Supplier Product

Kondisi saat ini:

- [database/migrations/2026_06_06_220000_create_supplier_products_table.php](E:/KERJAAN%20HARIS/SentosakuTechDev/Development/ArchipleSpice/arkipela-system/database/migrations/2026_06_06_220000_create_supplier_products_table.php:1) menyimpan `product_name` sebagai string
- [app/Models/SupplierProduct.php](E:/KERJAAN%20HARIS/SentosakuTechDev/Development/ArchipleSpice/arkipela-system/app/Models/SupplierProduct.php:1) masih fillable `product_name`
- [app/Http/Requests/SupplierRequest.php](E:/KERJAAN%20HARIS/SentosakuTechDev/Development/ArchipleSpice/arkipela-system/app/Http/Requests/SupplierRequest.php:1) mewajibkan `products.*.product_name`
- [resources/views/suppliers/_form.blade.php](E:/KERJAAN%20HARIS/SentosakuTechDev/Development/ArchipleSpice/arkipela-system/resources/views/suppliers/_form.blade.php:1) masih memakai input text `Product Name`
- [app/Http/Controllers/SupplierController.php](E:/KERJAAN%20HARIS/SentosakuTechDev/Development/ArchipleSpice/arkipela-system/app/Http/Controllers/SupplierController.php:1) membangun summary supplier dari nama produk bebas

Kesimpulan:

- supplier saat ini bertindak sebagai `owner` definisi produk
- ini harus diubah karena bertentangan dengan target product module yang berdiri sendiri

### 2.3 Order Item

Kondisi saat ini:

- [database/migrations/2026_06_06_130501_create_order_items_table.php](E:/KERJAAN%20HARIS/SentosakuTechDev/Development/ArchipleSpice/arkipela-system/database/migrations/2026_06_06_130501_create_order_items_table.php:1) menyimpan `product_name` sebagai string
- [app/Models/OrderItem.php](E:/KERJAAN%20HARIS/SentosakuTechDev/Development/ArchipleSpice/arkipela-system/app/Models/OrderItem.php:1) belum punya `product_id` atau `product_sku_id`
- [app/Http/Requests/OrderRequest.php](E:/KERJAAN%20HARIS/SentosakuTechDev/Development/ArchipleSpice/arkipela-system/app/Http/Requests/OrderRequest.php:1) memvalidasi kecocokan produk supplier berdasarkan string `product_name`

Kesimpulan:

- order saat ini belum punya traceability ke product master
- saat retail SKU masuk, order item perlu referensi terstruktur

### 2.4 Navigation and Permissions

Kondisi saat ini:

- [app/Enums/UserPermission.php](E:/KERJAAN%20HARIS/SentosakuTechDev/Development/ArchipleSpice/arkipela-system/app/Enums/UserPermission.php:1) belum memiliki permission `products.view` atau `products.manage`
- [resources/views/layouts/partials/sidebar.blade.php](E:/KERJAAN%20HARIS/SentosakuTechDev/Development/ArchipleSpice/arkipela-system/resources/views/layouts/partials/sidebar.blade.php:1) belum memiliki menu product module
- [routes/web.php](E:/KERJAAN%20HARIS/SentosakuTechDev/Development/ArchipleSpice/arkipela-system/routes/web.php:1) belum memiliki route product CRUD

Kesimpulan:

- PB1 perlu mencakup perubahan authorization dan navigation, bukan hanya schema

---

## 3. Modul Ownership

Ownership target:

### 3.1 Product Module

Menjadi owner untuk:

- commodity master
- SKU master
- packaging hierarchy
- barcode metadata
- retail readiness status

### 3.2 Supplier Module

Hanya menyimpan:

- supplier profile
- capability supply
- MOQ
- capacity
- lead time
- sourcing note
- mapping ke product atau SKU master

Supplier module tidak boleh:

- membuat definisi product baru
- menjadi source of truth product name
- menjadi source of truth barcode

### 3.3 Order Module

Hanya menyimpan:

- relasi ke product atau SKU master
- snapshot transaksi pada saat order dibuat
- supplier assignment untuk item tersebut

Order module tidak boleh:

- menjadi tempat definisi product baru
- bergantung hanya pada text `product_name`

---

## 4. Entity Target

Entity minimum untuk track ini:

1. `products`
2. `product_skus`
3. `product_packagings`
4. `supplier_products`
5. `order_items`

Entity 1 sampai 3 adalah `product-owned tables`.

Entity 4 dan 5 adalah `consumer tables` yang mereferensikan master tersebut.

---

## 5. Schema Draft

## 5.1 products

Tujuan:

- master commodity
- dipakai lintas supplier, client interest, order, reporting

Field draft:

```text
id
product_code                 unique
product_name
category                     nullable
scientific_name              nullable
origin_area                  nullable
form                         nullable
default_unit                 default KG
status                       default active
notes                        nullable
created_by                   nullable fk users.id
created_at
updated_at
deleted_at                   nullable
```

Index minimum:

```text
unique(product_code)
index(product_name)
index(status)
```

Catatan:

- `product_name` harus unik secara business rule, walau unique index bisa diputuskan setelah normalisasi data
- gunakan soft delete

## 5.2 product_skus

Tujuan:

- representasi item jual aktual
- titik utama untuk UPC/GTIN retail

Field draft:

```text
id
product_id                   fk products.id
sku_code                     unique
variant_name
brand_name                   nullable
net_weight                   decimal nullable
weight_unit                  default G
sellable_unit                default EACH
barcode_type                 nullable
gtin                         nullable
barcode_number               nullable
barcode_status               default draft
is_retail_sellable           boolean default false
is_active                    boolean default true
notes                        nullable
created_by                   nullable fk users.id
created_at
updated_at
deleted_at                   nullable
```

Index minimum:

```text
unique(sku_code)
unique(barcode_number) where barcode_number is not null
index(product_id, is_active)
index(barcode_status)
```

Catatan:

- `gtin` dan `barcode_number` boleh sama jika bisnis ingin satu field canonical, tapi untuk tahap awal lebih aman dipisah dulu
- `barcode_status` disiapkan untuk PB6:
  - draft
  - assigned
  - verified

## 5.3 product_packagings

Tujuan:

- membedakan barcode retail POS dengan barcode distribution level

Field draft:

```text
id
product_sku_id               fk product_skus.id
level                        each|inner|case|pallet
units_per_pack               integer nullable
barcode_type                 nullable
gtin                         nullable
barcode_number               nullable
length                       decimal nullable
width                        decimal nullable
height                       decimal nullable
dimension_unit               default CM
net_weight                   decimal nullable
gross_weight                 decimal nullable
is_default_for_level         boolean default false
notes                        nullable
created_at
updated_at
deleted_at                   nullable
```

Index minimum:

```text
index(product_sku_id, level)
unique(barcode_number) where barcode_number is not null
```

Catatan:

- `each` biasanya adalah retail POS level
- outer levels dipakai untuk carton, case, pallet, atau distribution use

## 5.4 supplier_products

Tujuan:

- bukan lagi owner product
- menjadi pivot sourcing capability

Field target:

```text
id
supplier_id                  fk suppliers.id
product_id                   fk products.id
product_sku_id               nullable fk product_skus.id
monthly_capacity_kg          decimal nullable
minimum_order_kg             decimal nullable
lead_time_days               integer nullable
packaging_type               nullable
notes                        nullable
is_active                    boolean default true
sort_order                   integer default 0
created_at
updated_at
```

Field legacy yang akan dipensiunkan:

```text
product_name
```

Aturan:

- `product_id` wajib
- `product_sku_id` optional
- kalau supplier hanya supply commodity bulk, cukup map ke `product_id`
- kalau supplier supply SKU retail spesifik, isi `product_sku_id`

## 5.5 order_items

Tujuan:

- order item refer ke master
- tetapi tetap menyimpan snapshot audit

Field target tambahan:

```text
product_id                   nullable fk products.id
product_sku_id               nullable fk product_skus.id
variant_name                 nullable
barcode_number               nullable
packaging_summary            nullable
```

Field snapshot yang tetap dipertahankan:

```text
product_name
specification
```

Aturan:

- untuk backward compatibility, `product_name` jangan langsung dihapus
- pada fase awal, field snapshot tetap dipakai untuk dokumen dan histori lama

---

## 6. Relationships

Relationship target:

```text
Product
  hasMany ProductSku
  hasMany SupplierProduct
  hasMany OrderItem

ProductSku
  belongsTo Product
  hasMany ProductPackaging
  hasMany SupplierProduct
  hasMany OrderItem

Supplier
  hasMany SupplierProduct

SupplierProduct
  belongsTo Supplier
  belongsTo Product
  belongsTo ProductSku optional

OrderItem
  belongsTo Order
  belongsTo Supplier optional
  belongsTo Product optional during transition
  belongsTo ProductSku optional during transition
```

---

## 7. Compatibility Rules

Supaya aman saat migrasi:

1. `supplier_products.product_name` tidak boleh langsung dihapus pada awal refactor
2. `order_items.product_name` tidak boleh langsung dihapus
3. lookup baru harus mulai memakai `product_id` dan `product_sku_id`
4. dokumen lama tetap membaca snapshot text jika relasi baru kosong
5. semua migration baru harus additive dulu, destructive belakangan

---

## 8. Migration Sequence

Urutan implementasi yang aman:

### Phase A - Additive Product Tables

1. create `products`
2. create `product_skus`
3. create `product_packagings`

### Phase B - Authorization and Navigation

4. tambah permission:
   - `products.view`
   - `products.manage`
5. update permission seeder
6. tambah route product module
7. tambah sidebar/menu product module

### Phase C - Consumer Table Extension

8. alter `supplier_products`
   - add `product_id`
   - add `product_sku_id`
   - add `lead_time_days`
   - add `packaging_type`
   - add `notes`
   - add `is_active`
9. alter `order_items`
   - add `product_id`
   - add `product_sku_id`
   - add `variant_name`
   - add `barcode_number`
   - add `packaging_summary`

### Phase D - Backfill and Application Refactor

10. seed products from normalized commodity list
11. backfill `supplier_products.product_id`
12. backfill `order_items.product_id` where possible
13. refactor forms and validation to prefer ids over names

### Phase E - Legacy Cleanup

14. stop using `product_name` input in supplier form
15. stop using `product_name` input in order form
16. drop or archive legacy columns only after data and code stabilizes

---

## 9. Backfill Strategy

## 9.1 supplier_products

Strategy:

1. build canonical product dictionary
2. normalize legacy `product_name`
3. map normalized name to `products.id`
4. fill `product_id`
5. keep `product_name` sementara sebagai audit field jika dibutuhkan

Normalisasi minimum:

- trim whitespace
- lower case comparison
- collapse multiple spaces
- samakan ejaan commodity inti

Contoh mapping:

```text
Clove -> Clove
Nutmeg -> Nutmeg
Mace -> Mace
White pepper -> White Pepper
Cinnamon cut -> Cinnamon
Clove stem -> Clove
Vanilla bean -> Vanilla
Cinnamon stick -> Cinnamon
```

Catatan:

- beberapa nama existing adalah `processed form`, bukan commodity utama
- untuk itu `form` atau `variant` nanti harus dipindah ke layer SKU atau metadata produk

## 9.2 order_items

Strategy:

1. coba match `product_name` existing ke `products`
2. jika match pasti, isi `product_id`
3. jika belum pasti, biarkan `product_id` null dan pertahankan snapshot text
4. jangan paksa backfill yang ambigu

Aturan aman:

- lebih baik `null` daripada salah map

---

## 10. Validation Impact

## 10.1 Supplier Form Future State

Current:

- `products.*.product_name` text input

Future:

- `products.*.product_id` required
- `products.*.product_sku_id` optional

Validation draft:

```text
products.*.product_id exists:products,id
products.*.product_sku_id exists:product_skus,id nullable
products.*.product_sku_id must belong to selected product if filled
```

## 10.2 Order Form Future State

Current:

- `items.*.product_name` text input
- validasi supplier-product match berbasis string

Future:

- `items.*.product_id` required
- `items.*.product_sku_id` optional or required by business flow
- supplier-product match berbasis relation

Validation draft:

```text
items.*.product_id exists:products,id
items.*.product_sku_id exists:product_skus,id nullable
selected product or sku must be available for chosen supplier
gross weight must remain >= net weight
```

---

## 11. Authorization Impact

Permission baru yang dibutuhkan:

```text
products.view
products.manage
```

Saran role mapping awal:

- Owner: view + manage
- Admin Export: view
- Procurement: view + manage
- Sales: view
- Finance: view

Catatan:

- detail mapping final bisa diputuskan di PB1, tapi permission slug perlu disiapkan dari awal

---

## 12. Route and UI Impact

Route minimum PB1:

```text
GET    /products
GET    /products/create
POST   /products
GET    /products/{product}
GET    /products/{product}/edit
PUT    /products/{product}
DELETE /products/{product}
```

Route minimum PB2:

```text
GET    /products/{product}/skus
POST   /products/{product}/skus
GET    /product-skus/{sku}
PUT    /product-skus/{sku}
DELETE /product-skus/{sku}
```

Sidebar impact:

- tambahkan menu `Products`
- posisinya sebaiknya sejajar dengan `Suppliers`, `Clients`, dan `Orders`

---

## 13. Risks to Avoid Before PB1

Risiko utama:

1. langsung mengganti supplier form tanpa product master aktif
2. langsung menghapus `product_name` lama
3. memaksa order lama ikut schema baru secara destructive
4. mencampur commodity master dengan SKU retail
5. memakai supplier sebagai tempat create product baru

Guardrail:

- PB1 hanya aktifkan `Product` module dulu
- PB2 baru aktifkan `SKU`
- PB4 baru sentuh supplier linkage
- PB5 baru sentuh order integration

---

## 14. Ready for PB1 Checklist

PB0 dianggap selesai jika checklist ini terpenuhi:

- ownership modul sudah jelas
- entity target sudah disepakati
- migration sequence sudah additive-first
- backfill strategy sudah tertulis
- route dan permission impact sudah teridentifikasi
- tidak ada perubahan runtime app yang berisiko di tahap PB0

Status saat dokumen ini dibuat:

- checklist PB0: `ready`

