# Archipela Product, SKU, and Barcode Sprint Plan

## 1. Tujuan

Dokumen ini memecah implementasi modul `Product`, `SKU`, dan `Barcode Retail` menjadi sprint yang bisa langsung dipakai sebagai acuan delivery.

Dokumen ini dibuat karena struktur saat ini masih memakai:

- nama produk bebas di supplier
- nama produk bebas di order item
- belum ada `product master` yang aktif
- belum ada identitas SKU retail yang stabil untuk `UPC/GTIN/barcode`

Target akhir track ini:

1. Sistem punya `commodity master` yang rapi
2. Sistem punya `SKU retail` yang bisa dibedakan per varian
3. Setiap SKU bisa menyimpan `barcode retail`
4. Modul product berdiri sendiri dan tidak digabung dengan modul supplier
5. Supplier dan order hanya `mereferensikan` product atau SKU master
6. Data produk siap dipakai untuk retail store, dokumen, dan label

Dokumen referensi:

- [PRD](./archipela_web_system_prd.md)
- [Sprint Plan MVP](./archipela_web_sprint_plan.md)
- [Build Workflow](./archipela_web_build_workflow.md)

---

## 2. Asumsi Sprint

- Durasi sprint yang disarankan: `1 minggu per sprint`
- Track ini berjalan sebagai `phase lanjutan` setelah fondasi supplier, client, dan order dasar tersedia
- Fokus utama bukan langsung label printing, tetapi `product identity and retail readiness`
- Existing order dan supplier data tidak boleh rusak saat refactor

---

## 3. Prinsip Desain

Pisahkan level data dengan jelas:

1. `Product`
   Komoditas utama, misalnya Clove, Nutmeg, Cinnamon
2. `SKU / Product Variant`
   Barang jual aktual, misalnya Clove Whole 50g atau Nutmeg Powder 100g
3. `Packaging Level`
   Each, inner, case, pallet
4. `Barcode`
   Identitas scan per sellable unit atau packaging level

Aturan penting:

- `1 sellable SKU = 1 barcode retail`
- beda berat, beda form, beda isi, atau beda bundle = SKU berbeda
- `Product master` harus bisa hidup tanpa supplier
- supplier tidak membuat definisi product baru
- supplier hanya di-link ke product atau SKU yang sudah ada
- order item harus menyimpan referensi `product` atau `sku`, bukan hanya text
- snapshot data produk di order tetap perlu untuk audit dokumen

Prinsip ownership:

- Modul `Product` adalah `master data owner`
- Modul `Supplier` hanya menyimpan:
  - kemampuan supply
  - kapasitas
  - MOQ
  - lead time
  - harga atau catatan sourcing
- Modul `Order` hanya memakai master yang sudah ada

---

## 4. Sprint Overview

| Sprint | Fokus | Outcome Utama |
| --- | --- | --- |
| Sprint PB0 | Discovery dan data model | Struktur entity, ownership modul, dan migration path disepakati |
| Sprint PB1 | Product Master Foundation | Modul product berdiri sendiri dan aktif |
| Sprint PB2 | SKU dan Barcode Master | SKU retail dan barcode aktif tanpa ketergantungan supplier |
| Sprint PB3 | Packaging dan Retail Readiness | Packaging hierarchy dan readiness status tertata |
| Sprint PB4 | Supplier Catalog Linkage | Supplier hanya map ke product atau SKU master |
| Sprint PB5 | Order Integration | Order item memilih product atau SKU dari master dan simpan snapshot |
| Sprint PB6 | Hardening dan Cleanup | Validasi, reporting, test, dan cleanup field legacy |

---

## 5. Detail Sprint

## Sprint PB0 - Discovery dan Data Model

### Goal

Menetapkan desain domain yang benar sebelum migrasi schema dan refactor modul berjalan.

### Scope

- Audit schema dan flow existing:
  - `suppliers`
  - `supplier_products`
  - `orders`
  - `order_items`
- Finalisasi entity minimum:
  - `products`
  - `product_skus`
  - `product_packagings`
- Tentukan field wajib per entity
- Tentukan strategy migrasi dari text ke relation
- Tentukan strategy backfill untuk existing supplier product dan order item
- Tentukan rule barcode:
  - `barcode_type`
  - `gtin`
  - `barcode_number`
  - uniqueness
- Tetapkan batas ownership antar modul

### Deliverables

- ERD atau schema draft final
- Mapping lama ke baru
- Backfill strategy
- Batas ownership antar modul
- Prioritized backlog sprint implementasi

### Acceptance Criteria

- Team sepakat kapan pakai `product`, kapan pakai `sku`
- Team sepakat packaging level minimum
- Team sepakat apakah barcode ditempel di `sku` atau `packaging`
- Team sepakat bahwa supplier bukan owner data product

### Risks

- Domain retail dan export bulk bisa bercampur jika definisi SKU tidak tegas
- Existing data bebas teks berpotensi punya banyak nama produk duplikat

---

## Sprint PB1 - Product Master Foundation

### Goal

Membangun `Product Master` sebagai source of truth komoditas yang berdiri sendiri dari supplier.

### Scope

- Migration `products`
- Model `Product`
- `ProductController`
- `ProductRequest`
- Seeder produk rempah dasar
- Halaman:
  - product list
  - create product
  - edit product
  - product detail
- Search
- Filter status
- Soft delete
- Activity log
- Route dan menu khusus product module

### Field Minimum

- `product_code`
- `product_name`
- `category`
- `scientific_name`
- `origin_area`
- `form`
- `default_unit`
- `status`
- `notes`

### Deliverables

- Product CRUD end-to-end
- Product code generated otomatis
- Data rempah inti tersedia di master
- Modul product bisa dipakai tanpa harus membuka supplier module

### Acceptance Criteria

- User tidak perlu lagi mendefinisikan commodity inti berulang-ulang
- Product list bisa dipakai sebagai referensi lintas modul
- Tidak ada product duplikat dengan code yang sama
- Product bisa dikelola langsung dari menu master data

### Dependency

- Sprint PB0 selesai

### Risks

- Nama commodity existing perlu dinormalisasi sebelum import

---

## Sprint PB2 - SKU dan Barcode Master

### Goal

Membangun layer `SKU retail` dan barcode sebagai bagian dari modul product, tanpa bergantung ke supplier.

### Scope

- Migration `product_skus`
- Model `ProductSku`
- `ProductSkuController`
- `ProductSkuRequest`
- Halaman:
  - sku list
  - create sku
  - edit sku
  - sku detail
- Field barcode retail
- Validasi uniqueness barcode
- Filter by product
- Filter by retail readiness

### Field Minimum SKU

- `product_id`
- `sku_code`
- `variant_name`
- `brand_name`
- `net_weight`
- `weight_unit`
- `sellable_unit`
- `barcode_type`
- `gtin`
- `barcode_number`
- `is_retail_sellable`
- `is_active`
- `notes`

### Deliverables

- SKU CRUD end-to-end
- Barcode data tersimpan terstruktur
- Product detail bisa menampilkan turunan SKU

### Acceptance Criteria

- Beda varian harus menghasilkan SKU berbeda
- Barcode number tidak boleh duplikat
- SKU bisa aktif walau belum di-link ke supplier mana pun

### Dependency

- Sprint PB1 selesai

### Risks

- Jika barcode source belum resmi dari GS1, data bisa terisi placeholder dan harus dibedakan statusnya
- Retail dan bulk SKU bisa bercampur jika naming convention lemah

---

## Sprint PB3 - Packaging dan Retail Readiness

### Goal

Menambahkan struktur packaging level agar retail unit dan distribution unit tidak tercampur.

### Scope

- Migration `product_packagings`
- Model `ProductPackaging`
- Packaging CRUD di dalam SKU detail
- Level minimum:
  - `each`
  - `inner`
  - `case`
  - `pallet`
- Status retail readiness:
  - no barcode
  - bulk only
  - retail ready
- Summary packaging di SKU detail

### Field Packaging Minimum

- `sku_id`
- `level`
- `units_per_pack`
- `barcode_type`
- `gtin`
- `barcode_number`
- `length`
- `width`
- `height`
- `dimension_unit`
- `net_weight`
- `gross_weight`

### Deliverables

- Packaging hierarchy per SKU
- Readiness status untuk retail dan distribution
- Pemisahan barcode retail vs barcode distribution lebih jelas

### Acceptance Criteria

- Satu SKU bisa punya beberapa level packaging
- Team bisa tahu barcode mana untuk POS retail dan mana untuk outer packaging
- SKU readiness bisa dilihat tanpa masuk ke supplier module

### Dependency

- Sprint PB2 selesai

### Risks

- Packaging design di lapangan bisa berubah dan perlu snapshot yang jelas
- Team bisa bingung jika nomenklatur level tidak dibakukan

---

## Sprint PB4 - Supplier Catalog Linkage

### Goal

Membuat supplier hanya menjadi `linked source` untuk product atau SKU yang sudah ada, bukan owner modul produk.

### Scope

- Ubah schema `supplier_products`
- Tambah `product_id`
- Optional tambah `product_sku_id` jika supplier supply level sudah spesifik SKU
- Pertahankan field operasional supplier:
  - `monthly_capacity_kg`
  - `minimum_order_kg`
  - `lead_time_days`
  - `packaging_type`
  - `notes`
  - `is_active`
- Update model relation:
  - `Supplier`
  - `SupplierProduct`
  - `Product`
  - `ProductSku`
- Refactor form supplier:
  - pilih product dari master
  - optional pilih SKU
- Buat migration/backfill dari `product_name` lama ke `product_id`
- Update supplier validation

### Deliverables

- Supplier product menggunakan selector master data
- Existing data supplier tetap terbawa
- Supplier detail tetap tampil rapi tanpa menjadi tempat definisi product

### Acceptance Criteria

- Supplier tidak bisa membuat product baru dari form supplier
- Supplier hanya bisa di-link ke product atau SKU yang sudah ada
- Existing supplier detail tetap bisa dibuka setelah migration

### Dependency

- Sprint PB1 selesai
- Sprint PB2 selesai jika supplier perlu link sampai level SKU

### Risks

- Backfill nama produk lama bisa gagal kalau ejaan tidak konsisten
- Procurement perlu adaptasi karena tidak lagi bebas mengetik nama produk

---

## Sprint PB5 - Order Integration

### Goal

Menghubungkan order item ke product atau SKU master agar data transaksi konsisten dan siap dipakai dokumen.

### Scope

- Tambah `product_id` dan/atau `product_sku_id` ke `order_items`
- Pertahankan snapshot:
  - `product_name`
  - `variant_name`
  - `barcode_number`
  - `packaging_summary`
- Refactor `OrderRequest`
- Refactor `OrderController`
- Refactor form order item:
  - pilih supplier
  - pilih product atau SKU yang valid
- Validasi bahwa product dipilih memang tersedia pada supplier terkait
- Update dokumen agar membaca snapshot dan relation dengan aman

### Deliverables

- Order create/edit memakai selector master data
- Snapshot order aman untuk audit
- Existing document preview tetap kompatibel

### Acceptance Criteria

- User tidak lagi mengetik nama produk manual saat create order
- Supplier-product mismatch tertolak server-side
- Existing order lama masih bisa dibuka

### Dependency

- Sprint PB4 selesai

### Risks

- Refactor ini menyentuh flow order paling inti, jadi regression risk tinggi
- Backward compatibility dokumen harus dijaga

---

## Sprint PB6 - Hardening dan Cleanup

### Goal

Merapikan modul product track agar siap dipakai operasional dan lebih aman untuk scale.

### Scope

- Validasi barcode format per type
- Status barcode:
  - draft
  - assigned
  - verified
- Report produk, SKU, dan barcode readiness
- Indicator di product detail:
  - no barcode
  - bulk only
  - retail ready
- Review authorization dan UX
- Cleanup field lama berbasis text jika sudah aman
- Tambah test coverage minimum untuk:
  - product CRUD
  - sku CRUD
  - packaging CRUD
  - supplier-product mapping
  - order integration

### Deliverables

- Dashboard atau report retail readiness dasar
- Data lama yang redundant mulai dibersihkan
- Flow input produk lebih aman

### Acceptance Criteria

- Team bisa tahu SKU mana yang belum punya barcode
- Team bisa tahu supplier mana yang hanya supply bulk product
- Flow create order tidak lagi ambigu antara commodity dan retail SKU

### Dependency

- Sprint PB5 selesai

### Risks

- Cleanup field lama tidak boleh memutus report existing
- Format barcode perlu mengikuti keputusan bisnis dan standar partner

---

## 6. Backlog Entitas

### Product

- CRUD
- Code generator
- Search/filter
- Soft delete
- Activity log

### Product SKU

- CRUD
- Barcode fields
- Retail ready flag
- Product relation

### Packaging

- Hierarchy per SKU
- Weight and dimension
- Distribution barcode

### Supplier Linkage

- Mapping ke product
- Optional mapping ke SKU
- Capacity
- MOQ
- Lead time

### Order Integration

- Product/SKU selector
- Snapshot on create
- Validation supplier-product match

---

## 7. Definition of Done Track

Track ini dianggap selesai ketika:

- Product master aktif dan dipakai sistem
- SKU retail bisa dibuat dan dibedakan per varian
- Barcode retail bisa disimpan per SKU atau packaging level
- Packaging hierarchy aktif dan jelas
- Supplier hanya mereferensikan product atau SKU master
- Order item memilih product atau SKU dari master
- Existing order dan dokumen lama tetap bisa diakses

---

## 8. Rekomendasi Eksekusi

Urutan paling aman:

1. Selesaikan `PB0` dulu tanpa menulis terlalu banyak code
2. Implement `PB1` dan stabilkan CRUD product
3. Kerjakan `PB2` dan `PB3` untuk membuat modul product benar-benar mandiri
4. Baru link ke supplier di `PB4`
5. Kerjakan `PB5` secara hati-hati dengan kompatibilitas existing order
6. Sisakan `PB6` untuk test, cleanup, dan report readiness

Catatan praktis:

- Kalau target jangka pendek masih bulk export, `PB3` bisa dibuat ringan dulu
- Kalau target partner US retail sudah aktif, `PB2`, `PB3`, dan `PB5` tidak sebaiknya ditunda lama
