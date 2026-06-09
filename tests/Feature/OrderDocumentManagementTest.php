<?php

namespace Tests\Feature;

use App\Enums\OrderDocumentStatus;
use App\Enums\OrderDocumentType;
use App\Enums\OrderStatus;
use App\Enums\ProductStatus;
use App\Enums\SupplierApprovalStatus;
use App\Enums\SupplierStatus;
use App\Enums\SupplierType;
use App\Enums\UserRole;
use App\Models\Client;
use App\Models\Order;
use App\Models\Product;
use App\Models\Role;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderDocumentManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_mandatory_order_document_placeholders_when_order_is_stored(): void
    {
        $owner = $this->createOwnerUser();
        [$client, $supplier, $product] = $this->createClientAndSupplier($owner);

        $response = $this
            ->actingAs($owner)
            ->post(route('orders.store'), [
                'client_id' => $client->id,
                'destination_country' => 'Japan',
                'destination_port' => 'Tokyo',
                'shipment_mode' => 'FCL',
                'order_date' => '2026-06-07',
                'delivery_date' => '2026-06-20',
                'po_number' => 'PO-001',
                'currency' => 'USD',
                'incoterm' => 'FOB',
                'payment_term' => 'T/T',
                'status' => OrderStatus::CONFIRMED->value,
                'notes' => 'Core order',
                'items' => [
                    [
                        'supplier_id' => $supplier->id,
                        'product_id' => $product->id,
                        'specification' => 'Grade A',
                        'quantity_kg' => 1000,
                        'selling_price' => 12.5,
                        'buying_price' => 8.4,
                    ],
                ],
            ]);

        $order = Order::query()->firstOrFail();

        $response->assertRedirect(route('orders.show', $order));

        $this->assertDatabaseHas('documents', [
            'order_id' => $order->id,
            'document_type' => OrderDocumentType::COMMERCIAL_INVOICE->value,
            'status' => OrderDocumentStatus::DRAFT->value,
        ]);

        $this->assertDatabaseHas('documents', [
            'order_id' => $order->id,
            'document_type' => OrderDocumentType::PACKING_LIST->value,
            'status' => OrderDocumentStatus::DRAFT->value,
        ]);
    }

    public function test_order_detail_displays_mandatory_document_checklist(): void
    {
        $owner = $this->createOwnerUser();
        [$client, $supplier, $product] = $this->createClientAndSupplier($owner);

        $order = Order::query()->create([
            'order_code' => 'ORD-0001',
            'client_id' => $client->id,
            'destination_country' => 'Japan',
            'destination_port' => 'Tokyo',
            'shipment_mode' => 'FCL',
            'order_date' => now()->toDateString(),
            'currency' => 'USD',
            'payment_term' => 'T/T',
            'status' => OrderStatus::DRAFT->value,
            'subtotal_sales' => 1000,
            'subtotal_buying' => 700,
            'gross_profit' => 300,
            'gross_margin' => 30,
            'total_additional_cost' => 0,
            'net_profit' => 300,
            'net_margin' => 30,
            'created_by' => $owner->id,
        ]);

        $order->items()->create([
            'supplier_id' => $supplier->id,
            'product_id' => $product->id,
            'product_name' => 'Clove',
            'specification' => 'Grade A',
            'quantity_kg' => 100,
            'selling_price' => 10,
            'buying_price' => 7,
            'line_total_sales' => 1000,
            'line_total_buying' => 700,
            'line_profit' => 300,
        ]);

        $order->documents()->createMany([
            [
                'document_type' => OrderDocumentType::COMMERCIAL_INVOICE->value,
                'status' => OrderDocumentStatus::DRAFT->value,
            ],
            [
                'document_type' => OrderDocumentType::PACKING_LIST->value,
                'status' => OrderDocumentStatus::DRAFT->value,
            ],
        ]);

        $response = $this
            ->actingAs($owner)
            ->get(route('orders.show', $order));

        $response->assertOk();
        $response->assertSee('Mandatory shipping document checklist');
        $response->assertSee('Commercial Invoice');
        $response->assertSee('Packing List');
        $response->assertSee('Draft');
    }

    public function test_it_generates_commercial_invoice_snapshot_from_order(): void
    {
        $owner = $this->createOwnerUser();
        [$client, $supplier, $product] = $this->createClientAndSupplier($owner);

        $order = $this->createOrderWithItems($owner, $client, $supplier, $product);
        $document = $order->documents()->create([
            'document_type' => OrderDocumentType::COMMERCIAL_INVOICE->value,
            'status' => OrderDocumentStatus::DRAFT->value,
        ]);

        $response = $this
            ->actingAs($owner)
            ->patch(route('orders.documents.generate', [$order, $document]));

        $response->assertRedirect(route('orders.show', $order));

        $document->refresh();

        $this->assertSame(OrderDocumentStatus::GENERATED->value, $document->status);
        $this->assertSame($owner->id, $document->generated_by);
        $this->assertNotNull($document->generated_at);
        $this->assertStringStartsWith('CI-2026-', $document->document_number);
        $this->assertSame('Tokyo Spice Import', data_get($document->snapshot_payload, 'buyer.company_name'));
        $this->assertSame('Clove', data_get($document->snapshot_payload, 'items.0.product_name'));
    }

    public function test_it_previews_generated_packing_list_from_snapshot_payload(): void
    {
        $owner = $this->createOwnerUser();
        [$client, $supplier, $product] = $this->createClientAndSupplier($owner);

        $order = $this->createOrderWithItems($owner, $client, $supplier, $product);
        $document = $order->documents()->create([
            'document_type' => OrderDocumentType::PACKING_LIST->value,
            'document_number' => 'PL-2026-00001',
            'status' => OrderDocumentStatus::GENERATED->value,
            'generated_by' => $owner->id,
            'generated_at' => now(),
            'snapshot_payload' => [
                'document_type' => OrderDocumentType::PACKING_LIST->value,
                'document_label' => 'Packing List',
                'document_number' => 'PL-2026-00001',
                'generated_at' => now()->toIso8601String(),
                'seller' => [
                    'company_name' => 'ArkipelaSpice Web',
                    'country' => 'Indonesia',
                ],
                'buyer' => [
                    'company_name' => 'Tokyo Spice Import',
                    'country' => 'Japan',
                ],
                'order' => [
                    'order_code' => $order->order_code,
                    'destination_port' => 'Tokyo',
                    'shipment_mode' => 'FCL',
                    'notes' => 'Core order',
                ],
                'items' => [
                    [
                        'line_number' => 1,
                        'product_name' => 'Clove',
                        'specification' => 'Grade A',
                        'supplier_name' => 'PT Rempah Nusantara',
                        'quantity_kg' => 100,
                    ],
                ],
                'packing_summary' => [
                    'line_item_count' => 1,
                    'total_quantity_kg' => 100,
                    'destination_port' => 'Tokyo',
                    'notes' => 'Core order',
                ],
            ],
        ]);

        $response = $this
            ->actingAs($owner)
            ->get(route('orders.documents.preview', [$order, $document]));

        $response->assertOk();
        $response->assertSee('Packing List');
        $response->assertSee('PL-2026-00001');
        $response->assertSee('Tokyo Spice Import');
        $response->assertSee('Clove');
    }

    private function createOwnerUser(): User
    {
        $role = Role::query()->firstOrCreate(
            ['slug' => UserRole::OWNER->value],
            ['name' => 'Owner', 'description' => 'Owner role']
        );

        return User::factory()->create([
            'role_id' => $role->id,
        ]);
    }

    /**
     * @return array{0: Client, 1: Supplier, 2: Product}
     */
    private function createClientAndSupplier(User $owner): array
    {
        $product = $this->createProduct('Clove', 'PRD-T101');

        $client = Client::query()->create([
            'client_code' => 'CLI-0001',
            'company_name' => 'Tokyo Spice Import',
            'country' => 'Japan',
            'status' => 'active',
            'created_by' => $owner->id,
        ]);

        $supplier = Supplier::query()->create([
            'supplier_code' => 'SUP-0001',
            'supplier_name' => 'PT Rempah Nusantara',
            'supplier_type' => SupplierType::TRADER->value,
            'status' => SupplierStatus::APPROVED->value,
            'approval_status' => SupplierApprovalStatus::APPROVED->value,
            'created_by' => $owner->id,
            'submitted_by' => $owner->id,
            'submitted_at' => now(),
            'approved_by' => $owner->id,
            'approved_at' => now(),
        ]);

        $supplier->products()->create([
            'product_id' => $product->id,
            'product_name' => 'Clove',
            'monthly_capacity_kg' => 1000,
            'minimum_order_kg' => 100,
            'sort_order' => 0,
        ]);

        return [$client, $supplier, $product];
    }

    private function createOrderWithItems(User $owner, Client $client, Supplier $supplier, Product $product): Order
    {
        $order = Order::query()->create([
            'order_code' => 'ORD-0001',
            'client_id' => $client->id,
            'destination_country' => 'Japan',
            'destination_port' => 'Tokyo',
            'shipment_mode' => 'FCL',
            'order_date' => now()->toDateString(),
            'delivery_date' => now()->addDays(7)->toDateString(),
            'po_number' => 'PO-001',
            'currency' => 'USD',
            'incoterm' => 'FOB',
            'payment_term' => 'T/T',
            'status' => OrderStatus::CONFIRMED->value,
            'subtotal_sales' => 1250,
            'subtotal_buying' => 840,
            'gross_profit' => 410,
            'gross_margin' => 32.8,
            'total_additional_cost' => 0,
            'net_profit' => 410,
            'net_margin' => 32.8,
            'notes' => 'Core order',
            'created_by' => $owner->id,
        ]);

        $order->items()->create([
            'supplier_id' => $supplier->id,
            'product_id' => $product->id,
            'product_name' => 'Clove',
            'specification' => 'Grade A',
            'quantity_kg' => 100,
            'selling_price' => 12.5,
            'buying_price' => 8.4,
            'line_total_sales' => 1250,
            'line_total_buying' => 840,
            'line_profit' => 410,
        ]);

        return $order;
    }

    private function createProduct(string $name, string $code): Product
    {
        return Product::query()->create([
            'product_code' => $code,
            'product_name' => $name,
            'category' => 'Spices',
            'default_unit' => 'KG',
            'status' => ProductStatus::ACTIVE->value,
        ]);
    }
}
