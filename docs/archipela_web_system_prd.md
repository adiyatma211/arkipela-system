# Archipela Export Management System — Web Based MVP

## 1. Project Context

Archipela adalah perusahaan ekspor yang saat ini fokus pada komoditas **spices/rempah-rempah**. Sistem ini dibuat untuk mengontrol operasional ekspor dari sisi supplier, client, order, quality control, shipment, dokumen, dan dashboard owner.

Fokus tahap awal adalah **web based internal system**. Mobile app untuk anak lapangan akan dibuat pada fase berikutnya, tetapi struktur data web harus sudah siap untuk menerima data QC lapangan di masa depan.

---

## 2. Product Vision

Membangun sistem internal Archipela sebagai **Export Operating System** untuk memastikan seluruh proses ekspor bisa dipantau, diukur, dan dikontrol dari satu dashboard.

Sistem harus membantu Archipela menjawab pertanyaan utama berikut:

1. Supplier mana yang siap supply dan kualitasnya bagus?
2. Client/buyer mana yang sedang aktif, negotiation, atau sudah PO?
3. Order mana yang sedang berjalan dan sudah sampai tahap apa?
4. Barang dari supplier mana yang lolos QC, hold, atau reject?
5. Berapa estimasi revenue, cost, dan gross margin per order?
6. Risiko operasional apa yang harus segera ditangani owner?

---

## 3. MVP Goal

MVP pertama harus fokus pada 5 modul utama:

1. **Dashboard Owner**
2. **Supplier Management**
3. **Client Management / CRM**
4. **Order Management**
5. **Quality Control Management**

Modul shipment, document, payment, dan mobile app boleh disiapkan struktur datanya, tetapi fitur lengkapnya masuk fase berikutnya.

---

## 4. Recommended Tech Stack

Gunakan stack yang cepat dibangun dan mudah dimaintain.

### Option A — Laravel Fullstack

Recommended untuk MVP cepat.

- Backend: Laravel 11+
- Frontend: Laravel Blade + Tailwind CSS / Livewire
- Database: MySQL / PostgreSQL
- Auth: Laravel Breeze / Laravel Sanctum
- File Storage: Local storage untuk MVP, S3-compatible untuk production
- Export PDF: DomPDF / Browsershot later

### Option B — API + Modern Frontend

Recommended jika ingin scalable dari awal.

- Backend: Laravel / NestJS / FastAPI
- Frontend: Next.js / React
- Database: PostgreSQL
- Auth: JWT / Sanctum
- File Storage: S3-compatible / Cloudflare R2

### MVP Decision

Untuk build cepat, gunakan:

```text
Laravel + Blade + Tailwind + MySQL/PostgreSQL
```

Pastikan struktur backend tetap API-friendly agar nanti mobile app bisa connect.

---

## 5. User Roles

### 5.1 Owner

Akses penuh ke seluruh data dan dashboard.

Owner bisa:

- Melihat dashboard revenue, order, supplier, client, QC, dan risk alert
- Approve/reject supplier
- Approve/reject hasil QC
- Melihat margin order
- Melihat status shipment dan payment
- Export report

### 5.2 Admin Export

Mengelola data operasional ekspor.

Admin bisa:

- Input dan update order
- Update status shipment
- Upload dokumen export
- Input payment status
- Melihat supplier dan client

### 5.3 Procurement

Mengelola supplier dan pembelian barang.

Procurement bisa:

- Input supplier
- Input supplier product
- Input harga supplier
- Update supplier status
- Assign supplier ke order

### 5.4 Sales / Client Relation

Mengelola buyer/client pipeline.

Sales bisa:

- Input client
- Update status client pipeline
- Buat quotation
- Convert quotation ke order
- Input follow-up notes

### 5.5 QC Admin

Mengelola quality control.

QC Admin bisa:

- Input QC report
- Upload foto QC
- Set result: Passed / Hold / Rejected
- Melihat QC history supplier

### 5.6 Finance

Mengelola biaya dan pembayaran.

Finance bisa:

- Input order cost
- Input payment progress
- Lihat margin order
- Update status DP / pelunasan

---

## 6. Main Navigation

```text
Dashboard

Master Data
- Products
- Product Grades
- Countries
- Ports
- Incoterms
- Payment Terms
- Packaging Types

Suppliers
- Supplier List
- Add Supplier
- Supplier Products
- Supplier Score
- Supplier QC History

Clients
- Client List
- Add Client
- Client Pipeline
- Follow Up Notes
- Quotation History

Orders
- Inquiry
- Quotation
- Active Orders
- Order Detail
- Costing & Margin
- Shipment Status
- Payment Status

Quality Control
- QC Reports
- Add QC Report
- Batch Tracking
- QC Photos
- Rejected Goods

Documents
- Export Document Checklist
- Commercial Invoice
- Packing List
- Sales Contract
- Buyer PO
- Supplier Invoice
- QC Report

Reports
- Sales Report
- Supplier Report
- Client Report
- QC Report
- Profit Report
- Shipment Report

Settings
- Users
- Roles
- Permissions
- Company Profile
```

---

# 7. Dashboard Owner

## 7.1 Purpose

Dashboard owner harus menjadi halaman utama untuk melihat kondisi bisnis Archipela secara cepat.

Dashboard harus menjawab:

- Berapa order aktif?
- Berapa potensi revenue?
- Berapa estimasi gross profit?
- Berapa order yang stuck?
- Berapa QC yang gagal?
- Supplier mana yang bermasalah?
- Client mana yang paling potensial?

---

## 7.2 Dashboard Cards

Tampilkan card berikut:

```text
Total Active Orders
Total Revenue Pipeline
Total Confirmed Revenue
Estimated Gross Profit
Average Gross Margin
Total Active Clients
Total Active Suppliers
QC Pass Rate
Pending QC
Rejected QC
Pending Payment
Delayed Shipment
```

---

## 7.3 Dashboard Sections

### A. Sales Pipeline Summary

Tampilkan jumlah client/order berdasarkan status:

```text
Lead
Contacted
Qualified
Quotation Sent
Negotiation
PO Received
Active Buyer
Lost
```

### B. Active Order Summary

Tampilkan order yang sedang berjalan.

Kolom:

```text
Order Code
Client
Country
Product
Quantity
Current Status
Target Shipment Date
Estimated Revenue
Estimated Margin
Risk Status
```

### C. QC Summary

Tampilkan ringkasan QC.

```text
Passed
Hold
Rejected
Pending Review
```

### D. Supplier Performance

Tampilkan ranking supplier berdasarkan:

```text
QC Pass Rate
Average Price
Supply Capacity
Delivery Reliability
Response Speed
Total Orders Supplied
```

### E. Risk Alert

Tampilkan alert jika:

```text
QC rejected
QC hold lebih dari 2 hari
Target shipment kurang dari 7 hari tapi QC belum selesai
Payment overdue
Supplier delay
Order tidak update lebih dari 3 hari
Margin di bawah threshold
Dokumen export belum lengkap
```

---

# 8. Supplier Management

## 8.1 Purpose

Supplier Management digunakan untuk menyimpan, menilai, dan mengontrol supplier rempah Archipela.

Sistem harus bisa membedakan supplier berdasarkan:

- Komoditas
- Lokasi
- Kapasitas supply
- Harga
- Legalitas
- Kualitas barang
- Riwayat QC
- Status approval

---

## 8.2 Supplier Status Flow

```text
Prospect
↓
Contacted
↓
Sample Requested
↓
Sample Received
↓
QC Checking
↓
Approved / Rejected / Hold
↓
Active Supplier / Blacklisted
```

---

## 8.3 Supplier Fields

```text
supplier_code
supplier_name
supplier_type
pic_name
phone
email
address
city
province
country
latitude
longitude
products_supplied
monthly_capacity_kg
minimum_order_kg
payment_term
legal_status
nib_number
npwp_number
bank_name
bank_account_number
bank_account_name
status
notes
created_by
created_at
updated_at
```

---

## 8.4 Supplier Type Options

```text
Farmer
Collector
Cooperative
Factory
Trader
Exporter Partner
```

---

## 8.5 Supplier Status Options

```text
Prospect
Contacted
Sample Requested
Sample Received
QC Checking
Approved
Active
Hold
Rejected
Blacklisted
```

---

## 8.6 Supplier Product Fields

Satu supplier bisa punya banyak produk.

```text
supplier_id
product_id
grade_id
origin_area
price_per_kg
currency
minimum_order_kg
monthly_capacity_kg
lead_time_days
packaging_type
moisture_standard
foreign_matter_standard
notes
is_active
```

---

## 8.7 Supplier Score

Supplier score dihitung dari beberapa parameter.

```text
quality_score: 30%
price_score: 20%
capacity_score: 20%
delivery_score: 10%
response_score: 10%
legal_score: 10%
```

Final score:

```text
supplier_score = quality_score + price_score + capacity_score + delivery_score + response_score + legal_score
```

Supplier score ditampilkan dalam bentuk:

```text
A: 85 - 100
B: 70 - 84
C: 50 - 69
D: below 50
```

---

## 8.8 Supplier Detail Page

Supplier detail harus menampilkan:

```text
Supplier Profile
PIC Contact
Products Supplied
Price History
QC History
Order History
Uploaded Documents
Supplier Score
Activity Notes
```

---

# 9. Client Management / CRM

## 9.1 Purpose

Client Management digunakan untuk mengelola buyer/export client dari tahap lead sampai repeat order.

---

## 9.2 Client Pipeline Flow

```text
Lead
↓
Contacted
↓
Qualified
↓
Sample Requested
↓
Sample Sent
↓
Quotation Sent
↓
Negotiation
↓
PO Received
↓
Active Buyer
↓
Repeat Buyer
```

Jika gagal:

```text
Lost
Inactive
```

---

## 9.3 Client Fields

```text
client_code
company_name
country
city
address
website
pic_name
pic_position
pic_email
pic_whatsapp
interested_products
target_quantity_kg
target_price
currency
preferred_incoterm
preferred_payment_term
status
source
notes
created_by
created_at
updated_at
```

---

## 9.4 Client Status Options

```text
Lead
Contacted
Qualified
Sample Requested
Sample Sent
Quotation Sent
Negotiation
PO Received
Active Buyer
Repeat Buyer
Lost
Inactive
```

---

## 9.5 Client Follow Up Notes

Setiap komunikasi dengan client harus dicatat.

Fields:

```text
client_id
follow_up_date
follow_up_type
summary
next_action
next_follow_up_date
created_by
```

Follow up type options:

```text
WhatsApp
Email
Call
Meeting
Online Meeting
Sample Discussion
Price Negotiation
Document Discussion
Other
```

---

## 9.6 Client Detail Page

Client detail harus menampilkan:

```text
Client Profile
PIC Contact
Product Interest
Pipeline Status
Follow Up Timeline
Quotation History
Order History
Payment History
Notes
```

---

# 10. Product & Commodity Master

## 10.1 Purpose

Product master digunakan untuk mendefinisikan komoditas rempah yang dijual Archipela.

---

## 10.2 Product Fields

```text
product_code
product_name
category
scientific_name
origin_area
form
unit
status
notes
```

---

## 10.3 Example Products

```text
Clove
Cinnamon
Nutmeg
Mace
Black Pepper
White Pepper
Cardamom
Turmeric
Ginger
Vanilla
Star Anise
```

---

## 10.4 Product Form Options

```text
Whole
Powder
Stick
Cut
Granule
Dry
Raw
Processed
```

---

## 10.5 Product Grade Fields

```text
product_id
grade_name
grade_code
description
moisture_max
foreign_matter_max
broken_max
color_standard
aroma_standard
size_standard
is_active
```

---

# 11. Order Management

## 11.1 Purpose

Order Management adalah pusat kontrol seluruh transaksi export Archipela.

Order harus menghubungkan:

```text
Client
Product
Supplier
QC Batch
Costing
Shipment
Documents
Payment
```

---

## 11.2 Order Flow

```text
Inquiry
↓
Quotation Sent
↓
Sample Process
↓
Sample Approved
↓
PO Received
↓
Supplier Assigned
↓
Goods Checking
↓
QC Passed / QC Hold / QC Failed
↓
Packing
↓
Document Process
↓
Ready to Ship
↓
Shipped
↓
Payment Pending
↓
Closed
```

---

## 11.3 Order Status Options

```text
Inquiry
Quotation Sent
Sample Process
Sample Approved
PO Received
Supplier Assigned
Goods Checking
QC Passed
QC Hold
QC Failed
Packing
Document Process
Ready to Ship
Shipped
Payment Pending
Closed
Cancelled
```

---

## 11.4 Order Fields

```text
order_code
client_id
quotation_id
status
order_date
target_shipment_date
actual_shipment_date
incoterm
payment_term
currency
exchange_rate
total_selling_amount
total_buying_amount
total_additional_cost
gross_profit
gross_margin_percentage
risk_status
notes
created_by
created_at
updated_at
```

---

## 11.5 Order Item Fields

Satu order bisa punya banyak item.

```text
order_id
product_id
grade_id
supplier_id
quantity_kg
unit
selling_price_per_kg
buying_price_per_kg
selling_total
buying_total
estimated_margin
notes
```

---

## 11.6 Order Detail Page

Order detail harus punya tab:

```text
Overview
Items
Supplier Assignment
QC Reports
Costing & Margin
Documents
Shipment
Payment
Activity Log
```

---

## 11.7 Order Code Format

Gunakan format:

```text
ARC-ORD-YYYYMM-0001
```

Contoh:

```text
ARC-ORD-202606-0001
```

---

# 12. Quotation Management

## 12.1 Purpose

Quotation digunakan untuk mencatat penawaran harga ke client.

Di MVP, quotation boleh berupa data internal dulu. Auto-generate PDF bisa masuk fase berikutnya.

---

## 12.2 Quotation Flow

```text
Draft
↓
Sent
↓
Negotiation
↓
Accepted / Rejected / Expired
↓
Convert to Order
```

---

## 12.3 Quotation Fields

```text
quotation_code
client_id
quotation_date
valid_until
status
incoterm
payment_term
currency
notes
created_by
created_at
updated_at
```

---

## 12.4 Quotation Item Fields

```text
quotation_id
product_id
grade_id
quantity_kg
unit_price
amount
notes
```

---

## 12.5 Quotation Status Options

```text
Draft
Sent
Negotiation
Accepted
Rejected
Expired
Converted to Order
```

---

# 13. Quality Control Management

## 13.1 Purpose

QC Management digunakan untuk memastikan barang spices dari supplier sesuai standar buyer dan standar Archipela.

QC harus berbasis **batch/lot** agar traceability jelas.

---

## 13.2 QC Flow

```text
Supplier Assigned to Order
↓
Batch Created
↓
QC Report Created
↓
QC Photos Uploaded
↓
QC Result: Passed / Hold / Rejected
↓
Owner/Admin Review
↓
Final Decision
```

---

## 13.3 Batch Code Format

Gunakan format:

```text
ARC-{PRODUCT_CODE}-{YYYYMMDD}-{SUPPLIER_CODE}-B{NUMBER}
```

Contoh:

```text
ARC-CLOVE-20260606-SUP001-B01
```

---

## 13.4 Batch Fields

```text
batch_code
order_id
supplier_id
product_id
grade_id
quantity_kg
received_date
warehouse_location
qc_status
notes
created_by
created_at
updated_at
```

---

## 13.5 QC Report Fields

```text
qc_code
batch_id
order_id
supplier_id
product_id
grade_id
checked_quantity_kg
sample_size_kg
moisture_percentage
foreign_matter_percentage
broken_percentage
color_condition
aroma_condition
size_condition
cleanliness_condition
packaging_condition
pest_insect_found
mold_found
result
hold_reason
rejected_reason
recommendation
checked_by
checked_at
reviewed_by
reviewed_at
final_decision
notes
created_at
updated_at
```

---

## 13.6 QC Result Options

```text
Passed
Hold
Rejected
```

---

## 13.7 Final Decision Options

```text
Approved for Order
Need Recheck
Request Re-cleaning
Request Re-drying
Request Re-sorting
Reject Supplier Batch
Reject Supplier
```

---

## 13.8 QC Reasons

Jika result `Hold` atau `Rejected`, reason wajib diisi.

Reason options:

```text
Moisture too high
Foreign matter too high
Bad aroma
Color not match
Broken percentage too high
Mold found
Insect found
Packaging damaged
Quantity mismatch
Need re-cleaning
Need re-drying
Need re-sorting
Other
```

---

## 13.9 QC Photo Requirements

Setiap QC report minimal punya foto:

```text
Product close-up
Product inside bag/sack
Sample in hand
Scale/weight proof
Packaging condition
Supplier/gudang location
```

Photo fields:

```text
qc_report_id
photo_type
file_path
caption
uploaded_by
uploaded_at
```

---

# 14. Shipment Management — Basic MVP

## 14.1 Purpose

Pada MVP, shipment cukup berupa tracking basic di order detail.

---

## 14.2 Shipment Fields

```text
order_id
forwarder_name
shipping_line
container_number
seal_number
port_of_loading
port_of_destination
etd
eta
shipment_status
notes
created_at
updated_at
```

---

## 14.3 Shipment Status Options

```text
Not Started
Booking Process
Container Booked
Stuffing Scheduled
Stuffed
Customs Process
Shipped
Arrived
Completed
Delayed
Cancelled
```

---

# 15. Document Management — Basic MVP

## 15.1 Purpose

Pada MVP, document management cukup berupa checklist dan upload file.

---

## 15.2 Document Types

```text
Buyer Purchase Order
Sales Contract
Commercial Invoice
Packing List
Supplier Invoice
QC Report
Certificate of Origin
Phytosanitary Certificate
Fumigation Certificate
Bill of Lading
Insurance Document
Other
```

---

## 15.3 Document Fields

```text
order_id
document_type
document_number
file_path
status
uploaded_by
uploaded_at
notes
```

---

## 15.4 Document Status Options

```text
Not Required
Pending
In Progress
Uploaded
Verified
Rejected
```

---

# 16. Payment & Costing — Basic MVP

## 16.1 Purpose

Payment dan costing digunakan untuk menghitung margin order.

---

## 16.2 Cost Types

```text
Product Cost
Local Transport
Cleaning
Drying
Sorting
Packing
Warehouse
Labor
Document Cost
Forwarder
Trucking
Port Charge
Bank Charge
Other
```

---

## 16.3 Order Cost Fields

```text
order_id
cost_type
cost_description
amount
currency
exchange_rate
amount_base_currency
created_by
created_at
```

---

## 16.4 Payment Fields

```text
order_id
payment_type
payment_date
amount
currency
exchange_rate
payment_status
proof_file_path
notes
created_by
created_at
```

---

## 16.5 Payment Type Options

```text
Down Payment
Balance Payment
Full Payment
Refund
Other
```

---

## 16.6 Payment Status Options

```text
Pending
Received
Partial
Overdue
Cancelled
```

---

## 16.7 Margin Formula

```text
Revenue = total_selling_amount
COGS = total_buying_amount
Additional Cost = total_additional_cost
Gross Profit = Revenue - COGS - Additional Cost
Gross Margin % = Gross Profit / Revenue * 100
```

---

# 17. Database Draft

## 17.1 Core Tables

```text
users
roles
permissions
products
product_grades
suppliers
supplier_products
supplier_documents
supplier_scores
clients
client_contacts
client_follow_ups
quotations
quotation_items
orders
order_items
batches
qc_reports
qc_report_photos
shipments
documents
order_costs
payments
activity_logs
```

---

## 17.2 users

```sql
id
name
email
password
role_id
status
last_login_at
created_at
updated_at
```

---

## 17.3 roles

```sql
id
name
description
created_at
updated_at
```

---

## 17.4 products

```sql
id
product_code
product_name
category
scientific_name
origin_area
form
unit
status
notes
created_at
updated_at
```

---

## 17.5 product_grades

```sql
id
product_id
grade_code
grade_name
description
moisture_max
foreign_matter_max
broken_max
color_standard
aroma_standard
size_standard
is_active
created_at
updated_at
```

---

## 17.6 suppliers

```sql
id
supplier_code
supplier_name
supplier_type
pic_name
phone
email
address
city
province
country
latitude
longitude
monthly_capacity_kg
minimum_order_kg
payment_term
legal_status
nib_number
npwp_number
bank_name
bank_account_number
bank_account_name
status
notes
created_by
created_at
updated_at
```

---

## 17.7 supplier_products

```sql
id
supplier_id
product_id
grade_id
origin_area
price_per_kg
currency
minimum_order_kg
monthly_capacity_kg
lead_time_days
packaging_type
moisture_standard
foreign_matter_standard
notes
is_active
created_at
updated_at
```

---

## 17.8 clients

```sql
id
client_code
company_name
country
city
address
website
pic_name
pic_position
pic_email
pic_whatsapp
target_quantity_kg
target_price
currency
preferred_incoterm
preferred_payment_term
status
source
notes
created_by
created_at
updated_at
```

---

## 17.9 client_follow_ups

```sql
id
client_id
follow_up_date
follow_up_type
summary
next_action
next_follow_up_date
created_by
created_at
updated_at
```

---

## 17.10 quotations

```sql
id
quotation_code
client_id
quotation_date
valid_until
status
incoterm
payment_term
currency
notes
created_by
created_at
updated_at
```

---

## 17.11 quotation_items

```sql
id
quotation_id
product_id
grade_id
quantity_kg
unit_price
amount
notes
created_at
updated_at
```

---

## 17.12 orders

```sql
id
order_code
client_id
quotation_id
status
order_date
target_shipment_date
actual_shipment_date
incoterm
payment_term
currency
exchange_rate
total_selling_amount
total_buying_amount
total_additional_cost
gross_profit
gross_margin_percentage
risk_status
notes
created_by
created_at
updated_at
```

---

## 17.13 order_items

```sql
id
order_id
product_id
grade_id
supplier_id
quantity_kg
unit
selling_price_per_kg
buying_price_per_kg
selling_total
buying_total
estimated_margin
notes
created_at
updated_at
```

---

## 17.14 batches

```sql
id
batch_code
order_id
supplier_id
product_id
grade_id
quantity_kg
received_date
warehouse_location
qc_status
notes
created_by
created_at
updated_at
```

---

## 17.15 qc_reports

```sql
id
qc_code
batch_id
order_id
supplier_id
product_id
grade_id
checked_quantity_kg
sample_size_kg
moisture_percentage
foreign_matter_percentage
broken_percentage
color_condition
aroma_condition
size_condition
cleanliness_condition
packaging_condition
pest_insect_found
mold_found
result
hold_reason
rejected_reason
recommendation
checked_by
checked_at
reviewed_by
reviewed_at
final_decision
notes
created_at
updated_at
```

---

## 17.16 qc_report_photos

```sql
id
qc_report_id
photo_type
file_path
caption
uploaded_by
uploaded_at
created_at
updated_at
```

---

## 17.17 shipments

```sql
id
order_id
forwarder_name
shipping_line
container_number
seal_number
port_of_loading
port_of_destination
etd
eta
shipment_status
notes
created_at
updated_at
```

---

## 17.18 documents

```sql
id
order_id
document_type
document_number
file_path
status
uploaded_by
uploaded_at
notes
created_at
updated_at
```

---

## 17.19 order_costs

```sql
id
order_id
cost_type
cost_description
amount
currency
exchange_rate
amount_base_currency
created_by
created_at
updated_at
```

---

## 17.20 payments

```sql
id
order_id
payment_type
payment_date
amount
currency
exchange_rate
payment_status
proof_file_path
notes
created_by
created_at
updated_at
```

---

## 17.21 activity_logs

```sql
id
user_id
module_name
record_id
action
old_value
new_value
description
created_at
```

---

# 18. API Endpoint Draft

Even if the MVP uses Blade, prepare controller routes cleanly so it can become API later.

## 18.1 Auth

```text
POST /login
POST /logout
GET /profile
```

---

## 18.2 Dashboard

```text
GET /dashboard
GET /dashboard/summary
GET /dashboard/risk-alerts
GET /dashboard/supplier-performance
GET /dashboard/qc-summary
GET /dashboard/order-summary
```

---

## 18.3 Suppliers

```text
GET /suppliers
GET /suppliers/create
POST /suppliers
GET /suppliers/{id}
GET /suppliers/{id}/edit
PUT /suppliers/{id}
DELETE /suppliers/{id}
POST /suppliers/{id}/products
POST /suppliers/{id}/documents
GET /suppliers/{id}/qc-history
```

---

## 18.4 Clients

```text
GET /clients
GET /clients/create
POST /clients
GET /clients/{id}
GET /clients/{id}/edit
PUT /clients/{id}
DELETE /clients/{id}
POST /clients/{id}/follow-ups
GET /clients/{id}/quotations
GET /clients/{id}/orders
```

---

## 18.5 Products

```text
GET /products
POST /products
GET /products/{id}
PUT /products/{id}
DELETE /products/{id}
POST /products/{id}/grades
```

---

## 18.6 Quotations

```text
GET /quotations
GET /quotations/create
POST /quotations
GET /quotations/{id}
PUT /quotations/{id}
POST /quotations/{id}/items
POST /quotations/{id}/send
POST /quotations/{id}/convert-to-order
```

---

## 18.7 Orders

```text
GET /orders
GET /orders/create
POST /orders
GET /orders/{id}
GET /orders/{id}/edit
PUT /orders/{id}
POST /orders/{id}/items
POST /orders/{id}/assign-supplier
POST /orders/{id}/update-status
GET /orders/{id}/costing
GET /orders/{id}/documents
GET /orders/{id}/shipment
GET /orders/{id}/payment
```

---

## 18.8 QC Reports

```text
GET /qc-reports
GET /qc-reports/create
POST /qc-reports
GET /qc-reports/{id}
PUT /qc-reports/{id}
POST /qc-reports/{id}/photos
POST /qc-reports/{id}/review
GET /batches
POST /batches
GET /batches/{id}
```

---

## 18.9 Shipments

```text
GET /shipments
POST /shipments
GET /shipments/{id}
PUT /shipments/{id}
```

---

## 18.10 Documents

```text
GET /documents
POST /documents
GET /documents/{id}
PUT /documents/{id}
DELETE /documents/{id}
```

---

## 18.11 Costs & Payments

```text
POST /orders/{id}/costs
PUT /orders/{id}/costs/{cost_id}
DELETE /orders/{id}/costs/{cost_id}
POST /orders/{id}/payments
PUT /orders/{id}/payments/{payment_id}
DELETE /orders/{id}/payments/{payment_id}
```

---

# 19. UI Page Requirements

## 19.1 Dashboard Page

Components:

```text
Summary cards
Sales pipeline chart
Active order table
QC summary chart
Supplier ranking table
Risk alert list
Recent activity log
```

---

## 19.2 Supplier List Page

Features:

```text
Search supplier
Filter by status
Filter by city/province
Filter by product
Filter by supplier type
Supplier score badge
Action buttons: View, Edit, Delete
```

Columns:

```text
Supplier Code
Supplier Name
Type
Location
Products
Capacity
Status
Score
PIC
Action
```

---

## 19.3 Supplier Detail Page

Tabs:

```text
Profile
Products
Price History
QC History
Order History
Documents
Notes
```

---

## 19.4 Client List Page

Features:

```text
Search client
Filter by country
Filter by status
Filter by product interest
Filter by sales owner
```

Columns:

```text
Client Code
Company Name
Country
PIC
Interested Products
Pipeline Status
Target Quantity
Last Follow Up
Action
```

---

## 19.5 Client Detail Page

Tabs:

```text
Profile
Follow Up Timeline
Quotations
Orders
Payment History
Notes
```

---

## 19.6 Order List Page

Features:

```text
Search order
Filter by status
Filter by client
Filter by product
Filter by target shipment date
Filter by risk status
```

Columns:

```text
Order Code
Client
Country
Product
Quantity
Status
Target Shipment
Revenue
Margin
Risk
Action
```

---

## 19.7 Order Detail Page

Tabs:

```text
Overview
Items
Supplier
QC
Costing
Documents
Shipment
Payment
Activity Log
```

---

## 19.8 QC Report List Page

Features:

```text
Search QC code
Filter by result
Filter by supplier
Filter by product
Filter by date
Filter by reviewed status
```

Columns:

```text
QC Code
Batch Code
Supplier
Product
Checked Quantity
Moisture
Foreign Matter
Result
Final Decision
Checked By
Checked At
Action
```

---

# 20. Business Rules

## 20.1 Supplier Approval

```text
Supplier cannot be assigned to an order if status is not Approved or Active.
```

## 20.2 Order Status Rule

```text
Order cannot move to Ready to Ship if QC status is not Passed or Approved for Order.
```

## 20.3 QC Rule

```text
QC result Hold or Rejected must have reason.
```

## 20.4 Margin Rule

```text
If gross margin percentage is below configured threshold, show risk alert.
Default threshold: 10%.
```

## 20.5 Document Rule

```text
Order cannot move to Shipped if required documents are not Uploaded or Verified.
```

## 20.6 Payment Rule

```text
If payment due date has passed and payment status is not Received, show overdue alert.
```

## 20.7 Activity Log Rule

```text
Every create, update, delete, status change, approval, and rejection must be recorded in activity_logs.
```

---

# 21. Seed Data

## 21.1 Roles

```text
Owner
Admin Export
Procurement
Sales
QC Admin
Finance
```

---

## 21.2 Incoterms

```text
EXW
FCA
FAS
FOB
CFR
CIF
CPT
CIP
DAP
DPU
DDP
```

---

## 21.3 Payment Terms

```text
T/T Advance
T/T 30% DP 70% Before Shipment
T/T 50% DP 50% Before Shipment
L/C at Sight
D/P
D/A
Cash
Other
```

---

## 21.4 Ports

```text
Tanjung Emas Semarang
Tanjung Perak Surabaya
Tanjung Priok Jakarta
Belawan Medan
Makassar Port
```

---

## 21.5 Product Categories

```text
Spices
Herbs
Dried Agricultural Products
```

---

## 21.6 Example Products

```text
Clove
Cinnamon
Nutmeg
Mace
Black Pepper
White Pepper
Cardamom
Turmeric
Ginger
Vanilla
Star Anise
```

---

# 22. MVP Development Phases

## Phase 1 — Foundation

Build:

```text
Authentication
Roles
Dashboard layout
Master data
Product master
Supplier management
Client management
```

Acceptance criteria:

```text
User can login
Owner can access dashboard
Admin can CRUD products
Procurement can CRUD suppliers
Sales can CRUD clients
```

---

## Phase 2 — Transaction Core

Build:

```text
Quotation management
Order management
Order items
Supplier assignment
Order status tracking
```

Acceptance criteria:

```text
Sales can create quotation
Quotation can be converted to order
Admin can assign supplier to order item
Owner can see active orders in dashboard
```

---

## Phase 3 — QC & Batch

Build:

```text
Batch creation
QC report form
QC photo upload
QC result
Owner review decision
QC dashboard summary
```

Acceptance criteria:

```text
QC Admin can create batch
QC Admin can submit QC report
QC report can upload photos
Owner can review QC result
Order cannot move to Ready to Ship without passed QC
```

---

## Phase 4 — Costing, Document, Shipment Basic

Build:

```text
Order costing
Payment tracking
Document checklist
Shipment basic tracking
Margin calculation
Risk alert
```

Acceptance criteria:

```text
Finance can input costs
System calculates gross profit and margin
Admin can upload documents
Admin can update shipment status
Dashboard shows risk alerts
```

---

# 23. Future Mobile Integration Preparation

Even though MVP focuses on web, prepare the following for future mobile app:

```text
API-ready controllers
QC reports support photo uploads
Batch code is server generated
Activity logs store user and timestamp
Future fields for GPS latitude/longitude
Future fields for device_id
Future fields for sync_status
```

Add nullable fields in QC reports or QC photo metadata:

```text
latitude
longitude
device_id
submitted_from
sync_status
```

submitted_from options:

```text
web
mobile
```

sync_status options:

```text
synced
pending_sync
failed_sync
```

---

# 24. Coding Rules for Codex

When generating code, follow these rules:

1. Use clean MVC structure.
2. Use migrations for all database tables.
3. Use model relationships properly.
4. Use enums/constants for status values.
5. Do not hardcode status labels inside Blade repeatedly.
6. Use policy/middleware for role-based access.
7. Use activity log for key changes.
8. Use server-generated codes for supplier, client, quotation, order, batch, and QC.
9. Use validation request classes for forms.
10. Use pagination, search, and filters for list pages.
11. Use soft delete for master data.
12. Use file upload validation for documents and QC photos.
13. Keep future API/mobile integration in mind.

---

# 25. Recommended Laravel Structure

```text
app/
  Models/
    Supplier.php
    SupplierProduct.php
    Client.php
    ClientFollowUp.php
    Product.php
    ProductGrade.php
    Quotation.php
    QuotationItem.php
    Order.php
    OrderItem.php
    Batch.php
    QcReport.php
    QcReportPhoto.php
    Shipment.php
    Document.php
    OrderCost.php
    Payment.php

  Http/
    Controllers/
      DashboardController.php
      SupplierController.php
      ClientController.php
      ProductController.php
      QuotationController.php
      OrderController.php
      QcReportController.php
      BatchController.php
      ShipmentController.php
      DocumentController.php
      PaymentController.php
      OrderCostController.php

    Requests/
      SupplierRequest.php
      ClientRequest.php
      ProductRequest.php
      QuotationRequest.php
      OrderRequest.php
      QcReportRequest.php
      ShipmentRequest.php
      DocumentRequest.php
      PaymentRequest.php
      OrderCostRequest.php

  Services/
    CodeGeneratorService.php
    DashboardService.php
    MarginCalculationService.php
    SupplierScoreService.php
    RiskAlertService.php
    ActivityLogService.php

  Enums/
    SupplierStatus.php
    ClientStatus.php
    OrderStatus.php
    QcResult.php
    ShipmentStatus.php
    PaymentStatus.php
    DocumentStatus.php
```

---

# 26. Code Generator Rules

Generate codes automatically.

```text
Supplier Code: SUP-0001
Client Code: CLI-0001
Quotation Code: ARC-QTN-YYYYMM-0001
Order Code: ARC-ORD-YYYYMM-0001
Batch Code: ARC-{PRODUCT_CODE}-{YYYYMMDD}-{SUPPLIER_CODE}-B01
QC Code: ARC-QC-YYYYMMDD-0001
```

---

# 27. Risk Alert Logic

Create risk alert service.

Alert conditions:

```text
QC result = Rejected
QC result = Hold
Order target shipment date <= today + 7 days and QC not passed
Order status not updated for more than 3 days
Payment status = Overdue
Gross margin percentage < 10
Document required but not uploaded
Shipment status = Delayed
Supplier status = Hold or Blacklisted but assigned to active order
```

Risk level:

```text
Low
Medium
High
Critical
```

---

# 28. Report Requirements

## 28.1 Supplier Report

Filters:

```text
Date range
Product
Supplier status
City/province
QC result
```

Output columns:

```text
Supplier
Product
Capacity
Average Price
QC Pass Rate
Rejected Count
Order Count
Supplier Score
```

---

## 28.2 Client Report

Filters:

```text
Date range
Country
Pipeline status
Product interest
```

Output columns:

```text
Client
Country
Status
Total Quotation
Total Order
Total Revenue
Last Follow Up
Next Follow Up
```

---

## 28.3 Order Report

Filters:

```text
Date range
Status
Client
Product
Country
Incoterm
```

Output columns:

```text
Order Code
Client
Product
Quantity
Revenue
COGS
Additional Cost
Gross Profit
Gross Margin
Status
Shipment Date
```

---

## 28.4 QC Report

Filters:

```text
Date range
Supplier
Product
QC result
Final decision
```

Output columns:

```text
QC Code
Batch Code
Supplier
Product
Moisture
Foreign Matter
Broken
Result
Final Decision
Checked By
Checked Date
```

---

# 29. Non-Functional Requirements

## 29.1 Security

```text
Use authentication
Use role-based access control
Validate all inputs
Protect file uploads
Restrict document access by role
Use CSRF protection
Use secure password hashing
```

---

## 29.2 Performance

```text
Paginate large tables
Index foreign keys
Index status fields
Index code fields
Use eager loading for list pages
Compress uploaded photos later if needed
```

---

## 29.3 Auditability

```text
All status changes must be logged
All approvals must be logged
All rejections must be logged
All document uploads must be logged
All margin/cost changes must be logged
```

---

## 29.4 Scalability

```text
Prepare API structure for mobile app
Separate business logic into services
Use storage abstraction for files
Use database relations consistently
Avoid direct query duplication in controllers
```

---

# 30. Final MVP Acceptance Criteria

MVP is considered ready when:

```text
Owner can login and see dashboard summary
Supplier data can be created, updated, filtered, and viewed
Client data can be created, updated, filtered, and viewed
Quotation can be created and converted to order
Order can be created with product, supplier, quantity, and price
QC batch can be created for an order
QC report can be submitted with result and photos
Order margin can be calculated
Shipment basic status can be updated
Document checklist can be uploaded
Payment status can be updated
Risk alerts appear on dashboard
Activity logs are recorded for important actions
```

---

# 31. Build Priority for First Sprint

First sprint should focus only on:

```text
Authentication
Role setup
Dashboard shell
Product master
Supplier CRUD
Client CRUD
Order CRUD basic
```

Do not build advanced features before CRUD core is stable.

---

# 32. Instruction for AI Coding Assistant

Build this system incrementally.

Start with:

```text
1. Create database schema migrations
2. Create models and relationships
3. Create seeders for roles, products, incoterms, payment terms, ports
4. Create authentication and role middleware
5. Create dashboard layout
6. Create Supplier CRUD
7. Create Client CRUD
8. Create Product and Grade CRUD
9. Create Quotation CRUD
10. Create Order CRUD
11. Create Batch and QC Report module
12. Add costing, documents, shipment, payment
13. Add dashboard summary and risk alert
```

Do not skip database relationships.
Do not skip validation.
Do not skip activity logs.
Do not make the UI too complex in MVP.

