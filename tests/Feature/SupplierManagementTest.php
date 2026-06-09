<?php

namespace Tests\Feature;

use App\Enums\ProductStatus;
use App\Enums\SupplierApprovalStatus;
use App\Enums\SupplierStatus;
use App\Enums\SupplierType;
use App\Enums\UserPermission;
use App\Enums\UserRole;
use App\Models\Permission;
use App\Models\Product;
use App\Models\Role;
use App\Models\Supplier;
use App\Models\SupplierPhoto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SupplierManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_procurement_supplier_creation_is_saved_as_pending_owner_approval(): void
    {
        $user = $this->createProcurementUser();
        $clove = $this->createProduct('Clove', 'PRD-T001');
        $nutmeg = $this->createProduct('Nutmeg', 'PRD-T002');
        Storage::fake('supplier-photos');

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
                        'product_id' => $clove->id,
                        'monthly_capacity_kg' => 1200,
                        'minimum_order_kg' => 200,
                    ],
                    [
                        'product_id' => $nutmeg->id,
                        'monthly_capacity_kg' => 900,
                        'minimum_order_kg' => 150,
                    ],
                ],
                'photos' => [
                    [
                        'file' => UploadedFile::fake()->image('location.jpg', 800, 600)->size(1024),
                        'photo_type' => 'location',
                        'caption' => 'Front office',
                    ],
                    [
                        'file' => UploadedFile::fake()->image('product.jpg', 800, 600)->size(1024),
                        'photo_type' => 'product',
                        'caption' => 'Clove sample',
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
            'status' => SupplierStatus::PROSPECT->value,
            'approval_status' => SupplierApprovalStatus::PENDING->value,
            'submitted_by' => $user->id,
        ]);

        $this->assertDatabaseHas('supplier_products', [
            'supplier_id' => $supplier->id,
            'product_id' => $clove->id,
            'product_name' => 'Clove',
            'monthly_capacity_kg' => 1200,
            'minimum_order_kg' => 200,
            'sort_order' => 0,
        ]);

        $this->assertDatabaseHas('supplier_products', [
            'supplier_id' => $supplier->id,
            'product_id' => $nutmeg->id,
            'product_name' => 'Nutmeg',
            'monthly_capacity_kg' => 900,
            'minimum_order_kg' => 150,
            'sort_order' => 1,
        ]);

        $photo = SupplierPhoto::query()
            ->where('supplier_id', $supplier->id)
            ->where('photo_type', 'location')
            ->firstOrFail();

        $this->assertDatabaseHas('supplier_photos', [
            'supplier_id' => $supplier->id,
            'photo_type' => 'product',
            'caption' => 'Clove sample',
        ]);

        Storage::disk('supplier-photos')->assertExists($photo->file_path);
    }

    public function test_owner_can_approve_pending_supplier(): void
    {
        $owner = $this->createOwnerUser();
        $procurement = $this->createProcurementUser();

        $supplier = Supplier::query()->create([
            'supplier_code' => 'SUP-9999',
            'supplier_name' => 'Supplier Pending',
            'supplier_type' => SupplierType::COLLECTOR->value,
            'status' => SupplierStatus::PROSPECT->value,
            'approval_status' => SupplierApprovalStatus::PENDING->value,
            'created_by' => $procurement->id,
            'submitted_by' => $procurement->id,
            'submitted_at' => now(),
            'products_summary' => 'Old Product',
            'monthly_capacity_kg' => 500,
            'minimum_order_kg' => 100,
        ]);

        $response = $this
            ->actingAs($owner)
            ->patch(route('suppliers.approve', $supplier));

        $response->assertRedirect(route('suppliers.show', $supplier));

        $this->assertDatabaseHas('suppliers', [
            'id' => $supplier->id,
            'approval_status' => SupplierApprovalStatus::APPROVED->value,
            'approved_by' => $owner->id,
        ]);
    }

    public function test_non_owner_update_of_approved_supplier_resubmits_for_owner_approval(): void
    {
        $user = $this->createProcurementUser();
        $owner = $this->createOwnerUser();
        $clove = $this->createProduct('Clove', 'PRD-T003');
        $cinnamon = $this->createProduct('Cinnamon', 'PRD-T004');
        $mace = $this->createProduct('Mace', 'PRD-T005');
        Storage::fake('supplier-photos');

        $supplier = Supplier::query()->create([
            'supplier_code' => 'SUP-9999',
            'supplier_name' => 'Supplier Lama',
            'supplier_type' => SupplierType::COLLECTOR->value,
            'status' => SupplierStatus::APPROVED->value,
            'approval_status' => SupplierApprovalStatus::APPROVED->value,
            'created_by' => $user->id,
            'submitted_by' => $user->id,
            'submitted_at' => now()->subDay(),
            'approved_by' => $owner->id,
            'approved_at' => now()->subDay(),
            'products_summary' => 'Clove',
            'monthly_capacity_kg' => 500,
            'minimum_order_kg' => 100,
        ]);

        $supplier->products()->createMany([
            [
                'product_id' => $clove->id,
                'product_name' => 'Clove',
                'monthly_capacity_kg' => 500,
                'minimum_order_kg' => 100,
                'sort_order' => 0,
            ],
        ]);

        $oldPhotoPath = UploadedFile::fake()
            ->image('old-warehouse.jpg', 800, 600)
            ->store("suppliers/{$supplier->id}/photos", 'supplier-photos');

        $existingPhoto = $supplier->photos()->create([
            'photo_type' => 'warehouse',
            'file_path' => $oldPhotoPath,
            'caption' => 'Old warehouse',
            'sort_order' => 0,
            'uploaded_by' => $user->id,
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
                'status' => SupplierStatus::ACTIVE->value,
                'payment_term' => 'T/T 30%',
                'legal_status' => 'Updated',
                'notes' => 'Updated',
                'products' => [
                    [
                        'product_id' => $cinnamon->id,
                        'monthly_capacity_kg' => 700,
                        'minimum_order_kg' => 120,
                    ],
                    [
                        'product_id' => $mace->id,
                        'monthly_capacity_kg' => 300,
                        'minimum_order_kg' => 80,
                    ],
                ],
                'existing_photos_to_delete' => [$existingPhoto->id],
                'photos' => [
                    [
                        'file' => UploadedFile::fake()->image('legal.jpg', 800, 600)->size(1024),
                        'photo_type' => 'legal_document',
                        'caption' => 'Updated legal document',
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
            'status' => SupplierStatus::APPROVED->value,
            'approval_status' => SupplierApprovalStatus::PENDING->value,
            'submitted_by' => $user->id,
        ]);

        $this->assertDatabaseMissing('supplier_products', [
            'supplier_id' => $supplier->id,
            'product_name' => 'Clove',
        ]);

        $this->assertDatabaseHas('supplier_products', [
            'supplier_id' => $supplier->id,
            'product_id' => $cinnamon->id,
            'product_name' => 'Cinnamon',
        ]);

        $this->assertDatabaseHas('supplier_products', [
            'supplier_id' => $supplier->id,
            'product_id' => $mace->id,
            'product_name' => 'Mace',
        ]);

        $this->assertDatabaseMissing('supplier_photos', [
            'id' => $existingPhoto->id,
        ]);

        $this->assertDatabaseHas('supplier_photos', [
            'supplier_id' => $supplier->id,
            'photo_type' => 'legal_document',
            'caption' => 'Updated legal document',
        ]);

        Storage::disk('supplier-photos')->assertMissing($oldPhotoPath);
        Storage::disk('supplier-photos')->assertExists(
            SupplierPhoto::query()
                ->where('supplier_id', $supplier->id)
                ->where('photo_type', 'legal_document')
                ->firstOrFail()
                ->file_path
        );
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

    private function createProcurementUser(): User
    {
        $role = Role::query()->firstOrCreate(
            ['slug' => UserRole::PROCUREMENT->value],
            ['name' => 'Procurement', 'description' => 'Procurement role']
        );

        $permission = Permission::query()->firstOrCreate(
            ['slug' => UserPermission::SUPPLIERS_MANAGE->value],
            [
                'name' => UserPermission::SUPPLIERS_MANAGE->label(),
                'module' => UserPermission::SUPPLIERS_MANAGE->module(),
                'description' => UserPermission::SUPPLIERS_MANAGE->description(),
            ]
        );

        $role->permissions()->syncWithoutDetaching([$permission->id]);

        return User::factory()->create([
            'role_id' => $role->id,
        ]);
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
