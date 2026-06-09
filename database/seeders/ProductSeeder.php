<?php

namespace Database\Seeders;

use App\Enums\ProductStatus;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ownerId = User::query()->where('email', 'owner@ArkipelaSpice.test')->value('id');

        $products = [
            ['product_code' => 'PRD-0001', 'product_name' => 'Clove', 'category' => 'Spices'],
            ['product_code' => 'PRD-0002', 'product_name' => 'Cinnamon', 'category' => 'Spices'],
            ['product_code' => 'PRD-0003', 'product_name' => 'Nutmeg', 'category' => 'Spices'],
            ['product_code' => 'PRD-0004', 'product_name' => 'Mace', 'category' => 'Spices'],
            ['product_code' => 'PRD-0005', 'product_name' => 'Black Pepper', 'category' => 'Spices'],
            ['product_code' => 'PRD-0006', 'product_name' => 'White Pepper', 'category' => 'Spices'],
            ['product_code' => 'PRD-0007', 'product_name' => 'Cardamom', 'category' => 'Spices'],
            ['product_code' => 'PRD-0008', 'product_name' => 'Turmeric', 'category' => 'Spices'],
            ['product_code' => 'PRD-0009', 'product_name' => 'Ginger', 'category' => 'Spices'],
            ['product_code' => 'PRD-0010', 'product_name' => 'Vanilla', 'category' => 'Spices'],
            ['product_code' => 'PRD-0011', 'product_name' => 'Star Anise', 'category' => 'Spices'],
        ];

        foreach ($products as $entry) {
            Product::query()->updateOrCreate(
                ['product_code' => $entry['product_code']],
                $entry + [
                    'default_unit' => 'KG',
                    'status' => ProductStatus::ACTIVE->value,
                    'created_by' => $ownerId,
                ],
            );
        }
    }
}
