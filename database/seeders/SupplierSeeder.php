<?php

namespace Database\Seeders;

use App\Enums\SupplierStatus;
use App\Enums\SupplierType;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ownerId = User::query()->where('email', 'owner@archipela.test')->value('id');

        $suppliers = [
            [
                'supplier_code' => 'SUP-0001',
                'supplier_name' => 'CV Maluku Spice Harvest',
                'supplier_type' => SupplierType::COLLECTOR->value,
                'pic_name' => 'Rizal Pattimura',
                'phone' => '+62 812-4100-1101',
                'email' => 'rizal@malukuspiceharvest.test',
                'address' => 'Jl. Pelabuhan Hitu No. 18',
                'city' => 'Ambon',
                'province' => 'Maluku',
                'country' => 'Indonesia',
                'products_summary' => 'Clove, Nutmeg, Mace',
                'monthly_capacity_kg' => 8500,
                'minimum_order_kg' => 500,
                'payment_term' => 'T/T 30% DP 70% after loading',
                'legal_status' => 'NIB complete, export partner ready',
                'status' => SupplierStatus::ACTIVE->value,
                'notes' => 'Strong clove supply during harvest season. Suitable for regular export orders.',
                'created_by' => $ownerId,
            ],
            [
                'supplier_code' => 'SUP-0002',
                'supplier_name' => 'Koperasi Rempah Banda',
                'supplier_type' => SupplierType::COOPERATIVE->value,
                'pic_name' => 'Mira Latuconsina',
                'phone' => '+62 813-5502-2204',
                'email' => 'mira@koperasibanda.test',
                'address' => 'Jl. Raya Banda Besar No. 7',
                'city' => 'Banda',
                'province' => 'Maluku',
                'country' => 'Indonesia',
                'products_summary' => 'Nutmeg, Mace',
                'monthly_capacity_kg' => 6200,
                'minimum_order_kg' => 300,
                'payment_term' => 'Cash before shipment',
                'legal_status' => 'Cooperative deed and local permits complete',
                'status' => SupplierStatus::APPROVED->value,
                'notes' => 'Good consistency for nutmeg grade export, ready for spot and monthly orders.',
                'created_by' => $ownerId,
            ],
            [
                'supplier_code' => 'SUP-0003',
                'supplier_name' => 'PT Sulawesi Aromatics',
                'supplier_type' => SupplierType::FACTORY->value,
                'pic_name' => 'Dian Mahendra',
                'phone' => '+62 811-4303-3305',
                'email' => 'dian@sulawesiaromatics.test',
                'address' => 'Kawasan Industri Bitung Blok C2',
                'city' => 'Bitung',
                'province' => 'North Sulawesi',
                'country' => 'Indonesia',
                'products_summary' => 'Cinnamon cut, Clove stem, White pepper',
                'monthly_capacity_kg' => 12000,
                'minimum_order_kg' => 1000,
                'payment_term' => 'T/T 50% DP 50% before dispatch',
                'legal_status' => 'Factory audited, food safety docs available',
                'status' => SupplierStatus::ACTIVE->value,
                'notes' => 'Useful for processed spice demand with stronger packaging capability.',
                'created_by' => $ownerId,
            ],
            [
                'supplier_code' => 'SUP-0004',
                'supplier_name' => 'UD Flores Spice Farm',
                'supplier_type' => SupplierType::FARMER->value,
                'pic_name' => 'Agus Kleden',
                'phone' => '+62 821-4411-4408',
                'email' => 'agus@floresspicefarm.test',
                'address' => 'Desa Ende Timur, Kecamatan Ndona',
                'city' => 'Ende',
                'province' => 'East Nusa Tenggara',
                'country' => 'Indonesia',
                'products_summary' => 'Vanilla bean, Cinnamon stick',
                'monthly_capacity_kg' => 2100,
                'minimum_order_kg' => 150,
                'payment_term' => 'Cash on delivery',
                'legal_status' => 'Farmer group with pending formal business permit',
                'status' => SupplierStatus::HOLD->value,
                'notes' => 'Quality promising, but document completeness still under review.',
                'created_by' => $ownerId,
            ],
            [
                'supplier_code' => 'SUP-0005',
                'supplier_name' => 'PT Java Spice Traders',
                'supplier_type' => SupplierType::TRADER->value,
                'pic_name' => 'Nadia Kusuma',
                'phone' => '+62 878-2200-5511',
                'email' => 'nadia@javaspicetraders.test',
                'address' => 'Jl. Margomulyo Permai No. 22',
                'city' => 'Surabaya',
                'province' => 'East Java',
                'country' => 'Indonesia',
                'products_summary' => 'Black pepper, White pepper, Turmeric',
                'monthly_capacity_kg' => 9000,
                'minimum_order_kg' => 750,
                'payment_term' => 'T/T 20% DP 80% before container release',
                'legal_status' => 'Trading company active and tax documents complete',
                'status' => SupplierStatus::CONTACTED->value,
                'notes' => 'Commercially responsive, sample stage not yet completed.',
                'created_by' => $ownerId,
            ],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::query()->updateOrCreate(
                ['supplier_code' => $supplier['supplier_code']],
                $supplier,
            );
        }
    }
}
