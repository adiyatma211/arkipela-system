@php
    $seller = data_get($documentPayload, 'seller', []);
    $buyer = data_get($documentPayload, 'buyer', []);
    $orderData = data_get($documentPayload, 'order', []);
    $items = collect(data_get($documentPayload, 'items', []));
    $packingSummary = data_get($documentPayload, 'packing_summary', []);
    $generatedAt = data_get($documentPayload, 'generated_at');

    $formatDate = static function ($value, string $format = 'M d, Y') {
        if (blank($value)) {
            return '-';
        }

        try {
            return \Illuminate\Support\Carbon::parse($value)->format($format);
        } catch (\Throwable $exception) {
            return '-';
        }
    };

    $formatNumber = static fn ($value, int $decimals = 2) => number_format((float) $value, $decimals);
    $resolveText = static fn (...$values) => collect($values)->filter(fn ($value) => filled($value))->join(', ') ?: '-';

    $buyerAddressLines = collect([
        data_get($buyer, 'company_name'),
        data_get($buyer, 'address'),
        $resolveText(data_get($buyer, 'city'), data_get($buyer, 'country')) !== '-'
            ? $resolveText(data_get($buyer, 'city'), data_get($buyer, 'country'))
            : null,
        filled(data_get($buyer, 'pic_whatsapp')) ? 'Phone : ' . data_get($buyer, 'pic_whatsapp') : null,
        filled(data_get($buyer, 'pic_name')) ? 'PIC : ' . data_get($buyer, 'pic_name') : null,
    ])->filter()->values();

    $totalNetWeight = (float) data_get($packingSummary, 'total_net_weight_kg', data_get($packingSummary, 'total_quantity_kg', 0));
    $totalGrossWeight = (float) data_get($packingSummary, 'total_gross_weight_kg', $totalNetWeight);
    $totalPackages = (int) data_get($packingSummary, 'line_item_count', 0);
    $totalQuantityPcs = (int) data_get($packingSummary, 'total_quantity_pcs', 0);
    $totalPackageCount = (int) data_get($packingSummary, 'total_package_count', 0);
    $signatureName = $document->generator?->name ?: 'Export Manager';
@endphp

<div class="invoice-sheet">
    <h1 class="invoice-title mb-4">PACKING LIST</h1>

    <table class="invoice-table" style="border: 0;">
        <colgroup>
            <col style="width: 55%;">
            <col style="width: 45%;">
        </colgroup>
        <tr>
            <td style="border: 0; padding-left: 0;">
                <div class="invoice-product-title mb-1">Buyer :</div>
                <div class="invoice-buyer-cell">
                    @foreach ($buyerAddressLines as $line)
                        <div class="invoice-product-meta">{{ $line }}</div>
                    @endforeach
                </div>
            </td>
            <td style="border: 0; padding-right: 0;">
                <table class="invoice-table">
                    <colgroup>
                        <col style="width: 34%;">
                        <col style="width: 6%;">
                        <col style="width: 60%;">
                    </colgroup>
                    <tr>
                        <td>Number</td>
                        <td class="text-center">:</td>
                        <td>{{ data_get($documentPayload, 'document_number', '-') }}</td>
                    </tr>
                    <tr>
                        <td>Date</td>
                        <td class="text-center">:</td>
                        <td>{{ $formatDate($generatedAt) }}</td>
                    </tr>
                    <tr>
                        <td>Payment</td>
                        <td class="text-center">:</td>
                        <td>{{ data_get($orderData, 'payment_term', '-') }}</td>
                    </tr>
                    <tr>
                        <td>POL</td>
                        <td class="text-center">:</td>
                        <td>Indonesia</td>
                    </tr>
                    <tr>
                        <td>POD</td>
                        <td class="text-center">:</td>
                        <td>{{ data_get($orderData, 'destination_port', '-') }}</td>
                    </tr>
                    <tr>
                        <td>Contain</td>
                        <td class="text-center">:</td>
                        <td>{{ data_get($orderData, 'shipment_mode', '-') }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="invoice-section-gap"></div>

    <table class="invoice-table">
        <colgroup>
            <col style="width: 8%;">
            <col style="width: 38%;">
            <col style="width: 14%;">
            <col style="width: 10%;">
            <col style="width: 15%;">
            <col style="width: 15%;">
        </colgroup>
        <thead>
            <tr>
                <th rowspan="2" class="text-center">No.</th>
                <th rowspan="2">Description of goods</th>
                <th rowspan="2" class="text-center">Qty<br>Order</th>
                <th rowspan="2" class="text-center">Qty<br>Boxes</th>
                <th rowspan="2" class="text-center">Nett W<br>KGS</th>
                <th rowspan="2" class="text-center">Gross W<br>KGS</th>
            </tr>
            <tr>
                <th class="text-center"></th>
                <th class="text-center"></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $item)
                <tr>
                    <td class="text-center">{{ data_get($item, 'line_number', $loop->iteration) }}</td>
                    <td>
                        <div class="invoice-product-title">
                            {{ data_get($item, 'item_code') ?: data_get($item, 'product_name', '-') }}
                        </div>
                        <div class="invoice-product-meta">{{ data_get($item, 'product_name', '-') }}</div>
                        <div class="invoice-product-meta">{{ data_get($item, 'specification', '-') }}</div>
                        @if (filled(data_get($item, 'packaging_summary')))
                            <div class="invoice-product-meta invoice-muted">{{ data_get($item, 'packaging_summary') }}</div>
                        @endif
                    </td>
                    <td class="text-right">
                        {{ data_get($item, 'quantity_pcs') ? number_format((int) data_get($item, 'quantity_pcs')) . ' ' . data_get($item, 'quantity_unit', 'PCS') : $formatNumber(data_get($item, 'quantity_kg', 0), 2) . ' KG' }}
                    </td>
                    <td class="text-right">{{ data_get($item, 'package_count') ? number_format((int) data_get($item, 'package_count')) : '-' }}</td>
                    <td class="text-right">{{ $formatNumber(data_get($item, 'net_weight_kg', data_get($item, 'quantity_kg', 0)), 2) }}</td>
                    <td class="text-right">{{ $formatNumber(data_get($item, 'gross_weight_kg', data_get($item, 'quantity_kg', 0)), 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">No line items available.</td>
                </tr>
            @endforelse
            @for ($spacer = $items->count(); $spacer < 5; $spacer++)
                <tr>
                    <td>&nbsp;</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            @endfor
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2" class="text-right"><strong>Total</strong></td>
                <td class="text-right"><strong>{{ $totalQuantityPcs > 0 ? number_format($totalQuantityPcs) . ' PCS' : $formatNumber($totalNetWeight, 2) . ' KG' }}</strong></td>
                <td class="text-right"><strong>{{ $totalPackageCount > 0 ? number_format($totalPackageCount) : '-' }}</strong></td>
                <td class="text-right"><strong>{{ $formatNumber($totalNetWeight, 2) }}</strong></td>
                <td class="text-right"><strong>{{ $formatNumber($totalGrossWeight, 2) }}</strong></td>
            </tr>
        </tfoot>
    </table>

    <div class="mt-4">
        <div>Description :</div>
        <div class="ms-4">- {{ $totalQuantityPcs > 0 ? number_format($totalQuantityPcs) . ' PCS total order quantity' : $formatNumber($totalNetWeight, 2) . ' KG total shipment weight' }}</div>
        <div class="ms-4">- {{ $totalPackageCount > 0 ? number_format($totalPackageCount) . ' package(s)' : $totalPackages . ' line item(s)' }} in this packing list</div>
        <div class="ms-4">- Total CBM {{ $formatNumber(data_get($packingSummary, 'total_cbm', 0), 4) }}</div>
    </div>

    <div class="mt-4">
        <div><strong>Total packing = {{ $totalPackageCount > 0 ? number_format($totalPackageCount) . ' package(s)' : $totalPackages . ' package line(s)' }}</strong></div>
    </div>

    <div class="mt-4">
        <div>Vessel : {{ data_get($orderData, 'shipment_mode', '-') }}</div>
        <div>On board : {{ $formatDate(data_get($orderData, 'delivery_date') ?: $generatedAt) }}</div>
    </div>

    <div class="mt-5" style="width: 220px; margin-left: auto; text-align: center;">
        <div class="mb-5">Regards,</div>
        <div style="border-bottom: 1px dotted #555; height: 55px;"></div>
        <div class="mt-2">{{ $signatureName }}</div>
    </div>
</div>
