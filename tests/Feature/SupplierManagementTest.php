<?php

namespace Tests\Feature;

use App\Enums\SupplierStatus;
use App\Enums\SupplierType;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupplierManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_stores_multiple_products_for_a_supplier(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('suppliers.store'), [
                'supplier_name' => 'PT Multi Produk Nusantara',
                'supplier_type' => SupplierType::TRADER->value,
                'pic_name' => 'Sari',
                'phone' => '+62 812-0000-0000',
                'email' => 'sari@multi.test',
                'address' => 'Jl. Test No. 1',
                'city' => 'Surabaya',
                'province' => 'East Java',
                'country' => 'Indonesia',
                'status' => SupplierStatus::ACTIVE->value,
                'payment_term' => 'Cash',
                'legal_status' => 'Complete',
                'notes' => 'Ready',
                'products' => [
                    [
                        'product_name' => 'Clove',
                        'monthly_capacity_kg' => 1200,
                        'minimum_order_kg' => 200,
                    ],
                    [
                        'product_name' => 'Nutmeg',
                        'monthly_capacity_kg' => 900,
                        'minimum_order_kg' => 150,
                    ],
                ],
            ]);

        $supplier = Supplier::query()->firstOrFail();

        $response->assertRedirect(route('suppliers.show', $supplier));

        $this->assertDatabaseHas('suppliers', [
            'id' => $supplier->id,
            'supplier_name' => 'PT Multi Produk Nusantara',
            'products_summary' => 'Clove, Nutmeg',
            'monthly_capacity_kg' => 2100,
            'minimum_order_kg' => 150,
        ]);

        $this->assertDatabaseHas('supplier_products', [
            'supplier_id' => $supplier->id,
            'product_name' => 'Clove',
            'monthly_capacity_kg' => 1200,
            'minimum_order_kg' => 200,
            'sort_order' => 0,
        ]);

        $this->assertDatabaseHas('supplier_products', [
            'supplier_id' => $supplier->id,
            'product_name' => 'Nutmeg',
            'monthly_capacity_kg' => 900,
            'minimum_order_kg' => 150,
            'sort_order' => 1,
        ]);
    }

    public function test_it_replaces_supplier_products_on_update(): void
    {
        $user = User::factory()->create();

        $supplier = Supplier::query()->create([
            'supplier_code' => 'SUP-9999',
            'supplier_name' => 'Supplier Lama',
            'supplier_type' => SupplierType::COLLECTOR->value,
            'status' => SupplierStatus::PROSPECT->value,
            'created_by' => $user->id,
            'products_summary' => 'Old Product',
            'monthly_capacity_kg' => 500,
            'minimum_order_kg' => 100,
        ]);

        $supplier->products()->createMany([
            [
                'product_name' => 'Old Product',
                'monthly_capacity_kg' => 500,
                'minimum_order_kg' => 100,
                'sort_order' => 0,
            ],
        ]);

        $response = $this
            ->actingAs($user)
            ->put(route('suppliers.update', $supplier), [
                'supplier_name' => 'Supplier Baru',
                'supplier_type' => SupplierType::FACTORY->value,
                'pic_name' => 'Dewi',
                'phone' => '+62 813-0000-0000',
                'email' => 'dewi@supplier.test',
                'address' => 'Jl. Update No. 2',
                'city' => 'Bandung',
                'province' => 'West Java',
                'country' => 'Indonesia',
                'status' => SupplierStatus::APPROVED->value,
                'payment_term' => 'T/T 30%',
                'legal_status' => 'Updated',
                'notes' => 'Updated',
                'products' => [
                    [
                        'product_name' => 'Cinnamon',
                        'monthly_capacity_kg' => 700,
                        'minimum_order_kg' => 120,
                    ],
                    [
                        'product_name' => 'Mace',
                        'monthly_capacity_kg' => 300,
                        'minimum_order_kg' => 80,
                    ],
                ],
            ]);

        $response->assertRedirect(route('suppliers.show', $supplier));

        $this->assertDatabaseHas('suppliers', [
            'id' => $supplier->id,
            'supplier_name' => 'Supplier Baru',
            'products_summary' => 'Cinnamon, Mace',
            'monthly_capacity_kg' => 1000,
            'minimum_order_kg' => 80,
        ]);

        $this->assertDatabaseMissing('supplier_products', [
            'supplier_id' => $supplier->id,
            'product_name' => 'Old Product',
        ]);

        $this->assertDatabaseHas('supplier_products', [
            'supplier_id' => $supplier->id,
            'product_name' => 'Cinnamon',
        ]);

        $this->assertDatabaseHas('supplier_products', [
            'supplier_id' => $supplier->id,
            'product_name' => 'Mace',
        ]);
    }
}
