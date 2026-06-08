<?php

namespace Database\Seeders;

use App\Enums\OrderStatus;
use App\Models\Client;
use App\Models\Order;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ownerId = User::query()->where('email', 'owner@archipela.test')->value('id');

        $clients = Client::query()
            ->pluck('id', 'client_code');

        $suppliers = Supplier::query()
            ->pluck('id', 'supplier_code');

        $orders = [
            [
                'order' => [
                    'order_code' => 'ORD-2026-0001',
                    'client_code' => 'CLI-0003',
                    'destination_country' => 'Turkey',
                    'destination_port' => 'Istanbul',
                    'shipment_mode' => 'FCL',
                    'order_date' => '2026-06-08',
                    'delivery_date' => '2026-07-15',
                    'po_number' => 'PO-IST-260608',
                    'currency' => 'USD',
                    'incoterm' => 'CFR Istanbul',
                    'payment_term' => 'T/T 20% DP 80% before shipment',
                    'status' => OrderStatus::DRAFT->value,
                    'local_logistics_cost' => 220.00,
                    'export_document_cost' => 140.00,
                    'forwarding_cost' => 185.00,
                    'freight_cost' => 640.00,
                    'insurance_cost' => 75.00,
                    'compliance_cost' => 60.00,
                    'destination_cost' => 0.00,
                    'misc_cost' => 35.00,
                    'notes' => 'Forwarder requested more complete packaging detail per pcs and per box.',
                    'created_by' => $ownerId,
                ],
                'items' => [
                    [
                        'supplier_code' => 'SUP-0005',
                        'item_code' => 'BP-IST-001',
                        'product_name' => 'Black pepper',
                        'hs_code' => '090411',
                        'specification' => 'Black pepper FAQ grade, cleaned, moisture max 13%, export packing.',
                        'quantity_kg' => 1200,
                        'quantity_pcs' => 240,
                        'quantity_unit' => 'PCS',
                        'pieces_per_package' => 2,
                        'package_count' => 120,
                        'package_type' => 'BOX',
                        'outer_package_type' => 'WOODEN_CRATE',
                        'length_cm' => 40,
                        'width_cm' => 40,
                        'height_cm' => 75,
                        'dimension_unit' => 'CM',
                        'net_weight_kg' => 1123.20,
                        'gross_weight_kg' => 1900.80,
                        'package_notes' => '2 pcs per box, then 6 boxes consolidated into one wooden outer crate.',
                        'selling_price' => 6.95,
                        'buying_price' => 5.80,
                    ],
                    [
                        'supplier_code' => 'SUP-0005',
                        'item_code' => 'WP-IST-002',
                        'product_name' => 'White pepper',
                        'hs_code' => '090412',
                        'specification' => 'White pepper cleaned, premium export grade, moisture max 12.5%.',
                        'quantity_kg' => 800,
                        'quantity_pcs' => 160,
                        'quantity_unit' => 'PCS',
                        'pieces_per_package' => 2,
                        'package_count' => 80,
                        'package_type' => 'BOX',
                        'outer_package_type' => 'PALLET',
                        'length_cm' => 38,
                        'width_cm' => 38,
                        'height_cm' => 68,
                        'dimension_unit' => 'CM',
                        'net_weight_kg' => 760.00,
                        'gross_weight_kg' => 1185.00,
                        'package_notes' => '2 pouches per carton, cartons palletized and stretch-wrapped.',
                        'selling_price' => 7.85,
                        'buying_price' => 6.35,
                    ],
                ],
            ],
            [
                'order' => [
                    'order_code' => 'ORD-2026-0002',
                    'client_code' => 'CLI-0001',
                    'destination_country' => 'United Arab Emirates',
                    'destination_port' => 'Jebel Ali',
                    'shipment_mode' => 'FCL',
                    'order_date' => '2026-05-28',
                    'delivery_date' => '2026-06-28',
                    'po_number' => 'PO-DXB-0528',
                    'currency' => 'USD',
                    'incoterm' => 'CIF Jebel Ali',
                    'payment_term' => 'T/T 30% advance 70% against copy documents',
                    'status' => OrderStatus::CONFIRMED->value,
                    'local_logistics_cost' => 350.00,
                    'export_document_cost' => 180.00,
                    'forwarding_cost' => 240.00,
                    'freight_cost' => 920.00,
                    'insurance_cost' => 110.00,
                    'compliance_cost' => 85.00,
                    'destination_cost' => 40.00,
                    'misc_cost' => 50.00,
                    'notes' => 'Monthly clove and nutmeg program for Dubai customer.',
                    'created_by' => $ownerId,
                ],
                'items' => [
                    [
                        'supplier_code' => 'SUP-0001',
                        'item_code' => 'CLV-DXB-001',
                        'product_name' => 'Clove',
                        'hs_code' => '090710',
                        'specification' => 'Hand-picked clove, sun dried, moisture max 13%, export quality.',
                        'quantity_kg' => 2500,
                        'quantity_pcs' => 500,
                        'quantity_unit' => 'PCS',
                        'pieces_per_package' => 5,
                        'package_count' => 100,
                        'package_type' => 'BAG',
                        'outer_package_type' => 'PALLET',
                        'length_cm' => 55,
                        'width_cm' => 35,
                        'height_cm' => 18,
                        'dimension_unit' => 'CM',
                        'net_weight_kg' => 2500.00,
                        'gross_weight_kg' => 2585.00,
                        'package_notes' => '25 kg inner bags stacked on fumigated pallets with corner protection.',
                        'selling_price' => 8.25,
                        'buying_price' => 6.95,
                    ],
                    [
                        'supplier_code' => 'SUP-0002',
                        'item_code' => 'NTM-DXB-002',
                        'product_name' => 'Nutmeg',
                        'hs_code' => '090811',
                        'specification' => 'Whole nutmeg, export sorted, premium aroma, moisture max 12%.',
                        'quantity_kg' => 1400,
                        'quantity_pcs' => 280,
                        'quantity_unit' => 'PCS',
                        'pieces_per_package' => 4,
                        'package_count' => 70,
                        'package_type' => 'BOX',
                        'outer_package_type' => 'PALLET',
                        'length_cm' => 42,
                        'width_cm' => 32,
                        'height_cm' => 30,
                        'dimension_unit' => 'CM',
                        'net_weight_kg' => 1365.00,
                        'gross_weight_kg' => 1490.00,
                        'package_notes' => '4 packs per carton, cartons arranged on export pallets and wrapped.',
                        'selling_price' => 10.60,
                        'buying_price' => 8.40,
                    ],
                ],
            ],
            [
                'order' => [
                    'order_code' => 'ORD-2026-0003',
                    'client_code' => 'CLI-0005',
                    'destination_country' => 'Netherlands',
                    'destination_port' => 'Rotterdam',
                    'shipment_mode' => 'LCL',
                    'order_date' => '2026-04-18',
                    'delivery_date' => '2026-05-22',
                    'po_number' => 'PO-RTM-0418',
                    'currency' => 'USD',
                    'incoterm' => 'CIF Rotterdam',
                    'payment_term' => 'T/T 30 days after BL copy',
                    'status' => OrderStatus::SHIPPED->value,
                    'local_logistics_cost' => 275.00,
                    'export_document_cost' => 165.00,
                    'forwarding_cost' => 210.00,
                    'freight_cost' => 780.00,
                    'insurance_cost' => 95.00,
                    'compliance_cost' => 140.00,
                    'destination_cost' => 55.00,
                    'misc_cost' => 42.00,
                    'notes' => 'Premium vanilla and clove mix for EU retail packaging program.',
                    'created_by' => $ownerId,
                ],
                'items' => [
                    [
                        'supplier_code' => 'SUP-0004',
                        'item_code' => 'VNL-RTM-001',
                        'product_name' => 'Vanilla bean',
                        'hs_code' => '090510',
                        'specification' => 'Grade A vanilla bean, vacuum-packed, moisture controlled for EU shipment.',
                        'quantity_kg' => 320,
                        'quantity_pcs' => 64,
                        'quantity_unit' => 'PCS',
                        'pieces_per_package' => 1,
                        'package_count' => 64,
                        'package_type' => 'VACUUM_BAG',
                        'outer_package_type' => 'BOX',
                        'length_cm' => 48,
                        'width_cm' => 28,
                        'height_cm' => 24,
                        'dimension_unit' => 'CM',
                        'net_weight_kg' => 320.00,
                        'gross_weight_kg' => 355.00,
                        'package_notes' => 'Each kilo vacuum-packed, then grouped into carton boxes with desiccant.',
                        'selling_price' => 14.75,
                        'buying_price' => 11.20,
                    ],
                    [
                        'supplier_code' => 'SUP-0001',
                        'item_code' => 'CLV-RTM-002',
                        'product_name' => 'Clove',
                        'hs_code' => '090710',
                        'specification' => 'Selected clove for premium blend, cleaned and export ready.',
                        'quantity_kg' => 950,
                        'quantity_pcs' => 190,
                        'quantity_unit' => 'PCS',
                        'pieces_per_package' => 5,
                        'package_count' => 38,
                        'package_type' => 'BOX',
                        'outer_package_type' => 'PALLET',
                        'length_cm' => 44,
                        'width_cm' => 34,
                        'height_cm' => 28,
                        'dimension_unit' => 'CM',
                        'net_weight_kg' => 950.00,
                        'gross_weight_kg' => 1018.00,
                        'package_notes' => '5 consumer packs per carton, cartons shrink-wrapped on pallets.',
                        'selling_price' => 8.95,
                        'buying_price' => 7.10,
                    ],
                ],
            ],
        ];

        foreach ($orders as $entry) {
            $orderPayload = $entry['order'];
            $itemPayloads = collect($entry['items'])
                ->map(function (array $item) use ($suppliers) {
                    $quantity = round((float) $item['quantity_kg'], 2);
                    $sellingPrice = round((float) $item['selling_price'], 2);
                    $buyingPrice = round((float) $item['buying_price'], 2);
                    $lineTotalSales = round($quantity * $sellingPrice, 2);
                    $lineTotalBuying = round($quantity * $buyingPrice, 2);

                    return [
                        'supplier_id' => $suppliers[$item['supplier_code']] ?? null,
                        'item_code' => $item['item_code'],
                        'product_name' => $item['product_name'],
                        'hs_code' => $item['hs_code'],
                        'specification' => $item['specification'],
                        'quantity_kg' => $quantity,
                        'quantity_pcs' => $item['quantity_pcs'],
                        'quantity_unit' => $item['quantity_unit'],
                        'pieces_per_package' => $item['pieces_per_package'],
                        'package_count' => $item['package_count'],
                        'package_type' => $item['package_type'],
                        'outer_package_type' => $item['outer_package_type'],
                        'length_cm' => round((float) $item['length_cm'], 2),
                        'width_cm' => round((float) $item['width_cm'], 2),
                        'height_cm' => round((float) $item['height_cm'], 2),
                        'dimension_unit' => $item['dimension_unit'],
                        'net_weight_kg' => round((float) $item['net_weight_kg'], 2),
                        'gross_weight_kg' => round((float) $item['gross_weight_kg'], 2),
                        'package_notes' => $item['package_notes'],
                        'selling_price' => $sellingPrice,
                        'buying_price' => $buyingPrice,
                        'line_total_sales' => $lineTotalSales,
                        'line_total_buying' => $lineTotalBuying,
                        'line_profit' => round($lineTotalSales - $lineTotalBuying, 2),
                    ];
                })
                ->all();

            $totals = $this->calculateTotals($itemPayloads, $orderPayload);
            $confirmedAt = in_array($orderPayload['status'], [
                OrderStatus::CONFIRMED->value,
                OrderStatus::PRODUCTION->value,
                OrderStatus::READY_TO_SHIP->value,
                OrderStatus::SHIPPED->value,
                OrderStatus::COMPLETED->value,
            ], true) ? $orderPayload['order_date'] . ' 10:00:00' : null;

            $order = Order::query()->updateOrCreate(
                ['order_code' => $orderPayload['order_code']],
                [
                    'client_id' => $clients[$orderPayload['client_code']] ?? null,
                    'destination_country' => $orderPayload['destination_country'],
                    'destination_port' => $orderPayload['destination_port'],
                    'shipment_mode' => $orderPayload['shipment_mode'],
                    'order_date' => $orderPayload['order_date'],
                    'delivery_date' => $orderPayload['delivery_date'],
                    'po_number' => $orderPayload['po_number'],
                    'currency' => $orderPayload['currency'],
                    'incoterm' => $orderPayload['incoterm'],
                    'payment_term' => $orderPayload['payment_term'],
                    'status' => $orderPayload['status'],
                    'local_logistics_cost' => $orderPayload['local_logistics_cost'],
                    'export_document_cost' => $orderPayload['export_document_cost'],
                    'forwarding_cost' => $orderPayload['forwarding_cost'],
                    'freight_cost' => $orderPayload['freight_cost'],
                    'insurance_cost' => $orderPayload['insurance_cost'],
                    'compliance_cost' => $orderPayload['compliance_cost'],
                    'destination_cost' => $orderPayload['destination_cost'],
                    'misc_cost' => $orderPayload['misc_cost'],
                    'subtotal_sales' => $totals['subtotal_sales'],
                    'subtotal_buying' => $totals['subtotal_buying'],
                    'gross_profit' => $totals['gross_profit'],
                    'gross_margin' => $totals['gross_margin'],
                    'total_additional_cost' => $totals['total_additional_cost'],
                    'net_profit' => $totals['net_profit'],
                    'net_margin' => $totals['net_margin'],
                    'confirmed_at' => $confirmedAt,
                    'notes' => $orderPayload['notes'],
                    'created_by' => $orderPayload['created_by'],
                ]
            );

            $order->items()->delete();
            $order->items()->createMany($itemPayloads);
        }
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     * @param  array<string, mixed>  $payload
     * @return array<string, float>
     */
    private function calculateTotals(array $items, array $payload): array
    {
        $subtotalSales = collect($items)->sum('line_total_sales');
        $subtotalBuying = collect($items)->sum('line_total_buying');
        $grossProfit = $subtotalSales - $subtotalBuying;
        $grossMargin = $subtotalSales > 0 ? ($grossProfit / $subtotalSales) * 100 : 0;
        $totalAdditionalCost = collect([
            $payload['local_logistics_cost'] ?? 0,
            $payload['export_document_cost'] ?? 0,
            $payload['forwarding_cost'] ?? 0,
            $payload['freight_cost'] ?? 0,
            $payload['insurance_cost'] ?? 0,
            $payload['compliance_cost'] ?? 0,
            $payload['destination_cost'] ?? 0,
            $payload['misc_cost'] ?? 0,
        ])->sum(fn ($value) => (float) $value);
        $netProfit = $grossProfit - $totalAdditionalCost;
        $netMargin = $subtotalSales > 0 ? ($netProfit / $subtotalSales) * 100 : 0;

        return [
            'subtotal_sales' => round($subtotalSales, 2),
            'subtotal_buying' => round($subtotalBuying, 2),
            'gross_profit' => round($grossProfit, 2),
            'gross_margin' => round($grossMargin, 2),
            'total_additional_cost' => round($totalAdditionalCost, 2),
            'net_profit' => round($netProfit, 2),
            'net_margin' => round($netMargin, 2),
        ];
    }
}
