<?php

namespace Database\Seeders;

use App\Enums\ClientStatus;
use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ownerId = User::query()->where('email', 'owner@archipela.test')->value('id');

        $clients = [
            [
                'client_code' => 'CLI-0001',
                'company_name' => 'Dubai Spice Souk LLC',
                'country' => 'United Arab Emirates',
                'city' => 'Dubai',
                'address' => 'Al Ras Spice Market, Deira',
                'website' => 'https://dubaispicesouk.example',
                'pic_name' => 'Ahmed Al Mansoori',
                'pic_position' => 'Procurement Manager',
                'pic_email' => 'ahmed@dubaispicesouk.example',
                'pic_whatsapp' => '+971-50-110-2201',
                'interested_products' => 'Clove, Nutmeg, Cinnamon',
                'target_quantity_kg' => 6000,
                'target_price' => 8.25,
                'currency' => 'USD',
                'preferred_incoterm' => 'CIF Jebel Ali',
                'preferred_payment_term' => 'T/T 30% advance 70% against copy documents',
                'status' => ClientStatus::NEGOTIATION->value,
                'source' => 'Gulfood referral',
                'notes' => 'Warm prospect with active discussion on monthly clove program.',
                'created_by' => $ownerId,
            ],
            [
                'client_code' => 'CLI-0002',
                'company_name' => 'Mumbai Global Seasoning Pvt Ltd',
                'country' => 'India',
                'city' => 'Mumbai',
                'address' => 'Andheri East Industrial Estate',
                'website' => 'https://mumbaiglobalseasoning.example',
                'pic_name' => 'Priya Menon',
                'pic_position' => 'Import Buyer',
                'pic_email' => 'priya@mumbaiglobalseasoning.example',
                'pic_whatsapp' => '+91-98-3000-4412',
                'interested_products' => 'Nutmeg, Mace',
                'target_quantity_kg' => 3500,
                'target_price' => 10.40,
                'currency' => 'USD',
                'preferred_incoterm' => 'FOB Surabaya',
                'preferred_payment_term' => 'LC at sight',
                'status' => ClientStatus::QUOTATION_SENT->value,
                'source' => 'Inbound website lead',
                'notes' => 'Quotation submitted, waiting buyer internal approval.',
                'created_by' => $ownerId,
            ],
            [
                'client_code' => 'CLI-0003',
                'company_name' => 'Istanbul Herb Trading',
                'country' => 'Turkey',
                'city' => 'Istanbul',
                'address' => 'Fatih Commercial District',
                'website' => 'https://istanbulherbtrading.example',
                'pic_name' => 'Kerem Yildiz',
                'pic_position' => 'Owner',
                'pic_email' => 'kerem@istanbulherbtrading.example',
                'pic_whatsapp' => '+90-533-220-1188',
                'interested_products' => 'Black pepper, White pepper',
                'target_quantity_kg' => 2500,
                'target_price' => 6.95,
                'currency' => 'USD',
                'preferred_incoterm' => 'CFR Istanbul',
                'preferred_payment_term' => 'T/T 20% DP 80% before shipment',
                'status' => ClientStatus::LEAD->value,
                'source' => 'Trade show contact',
                'notes' => 'Fresh lead, initial company introduction completed.',
                'created_by' => $ownerId,
            ],
            [
                'client_code' => 'CLI-0004',
                'company_name' => 'Cairo Food Ingredients Co',
                'country' => 'Egypt',
                'city' => 'Cairo',
                'address' => 'Nasr City Food Ingredient Cluster',
                'website' => 'https://cairofoodingredients.example',
                'pic_name' => 'Mona Fathi',
                'pic_position' => 'Senior Buyer',
                'pic_email' => 'mona@cairofoodingredients.example',
                'pic_whatsapp' => '+20-100-550-6677',
                'interested_products' => 'Cinnamon, Turmeric',
                'target_quantity_kg' => 4200,
                'target_price' => 4.85,
                'currency' => 'USD',
                'preferred_incoterm' => 'FOB Jakarta',
                'preferred_payment_term' => 'T/T 50% advance 50% before shipment',
                'status' => ClientStatus::ACTIVE_BUYER->value,
                'source' => 'Existing partner referral',
                'notes' => 'Client already validated and ready for repeat orders.',
                'created_by' => $ownerId,
            ],
            [
                'client_code' => 'CLI-0005',
                'company_name' => 'Rotterdam Natural Spices BV',
                'country' => 'Netherlands',
                'city' => 'Rotterdam',
                'address' => 'Waalhaven Import Terminal Area',
                'website' => 'https://rotterdamnaturalspices.example',
                'pic_name' => 'Sophie van Dijk',
                'pic_position' => 'Category Manager',
                'pic_email' => 'sophie@rotterdamnaturalspices.example',
                'pic_whatsapp' => '+31-6-2200-4488',
                'interested_products' => 'Vanilla bean, Clove',
                'target_quantity_kg' => 1800,
                'target_price' => 14.75,
                'currency' => 'USD',
                'preferred_incoterm' => 'CIF Rotterdam',
                'preferred_payment_term' => 'T/T 30 days after BL copy',
                'status' => ClientStatus::REPEAT_BUYER->value,
                'source' => 'Direct outreach',
                'notes' => 'Good fit for premium spice program and recurring EU demand.',
                'created_by' => $ownerId,
            ],
        ];

        foreach ($clients as $client) {
            Client::query()->updateOrCreate(
                ['client_code' => $client['client_code']],
                $client,
            );
        }
    }
}
