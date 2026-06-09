@php
    $seller = data_get($documentPayload, 'seller', []);
    $buyer = data_get($documentPayload, 'buyer', []);
    $orderData = data_get($documentPayload, 'order', []);
    $items = collect(data_get($documentPayload, 'items', []));
    $totals = data_get($documentPayload, 'totals', []);
    $photoAttachments = collect(data_get($documentPayload, 'photo_attachments', []))
        ->flatMap(function ($attachment) {
            return collect(data_get($attachment, 'photos', []))->map(function ($photo, $index) use ($attachment) {
                return [
                    'line_number' => data_get($attachment, 'line_number'),
                    'item_code' => data_get($attachment, 'item_code'),
                    'product_name' => data_get($attachment, 'product_name'),
                    'supplier_name' => data_get($attachment, 'supplier_name'),
                    'caption' => data_get($photo, 'caption'),
                    'url' => data_get($photo, 'url'),
                    'photo_type' => data_get($photo, 'photo_type'),
                    'photo_index' => $index + 1,
                ];
            });
        })
        ->filter(fn ($photo) => filled(data_get($photo, 'url')))
        ->values();
    $currency = data_get($orderData, 'currency', 'USD');
    $generatedAt = data_get($documentPayload, 'generated_at');
    $companyLogo = public_path('assetes/logo/logo.png');

    $formatDate = static function ($value, string $format = 'd M Y') {
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

    $sellerAddress = $resolveText(
        data_get($seller, 'address'),
        data_get($seller, 'city'),
        data_get($seller, 'country')
    );

    $buyerAddress = $resolveText(
        data_get($buyer, 'address'),
        data_get($buyer, 'city'),
        data_get($buyer, 'country')
    );

    $buyerContact = collect([
        data_get($buyer, 'pic_name'),
        data_get($buyer, 'pic_email'),
        data_get($buyer, 'pic_whatsapp'),
    ])->filter()->join(' | ');

    $termsText = collect([
        data_get($orderData, 'incoterm'),
        data_get($orderData, 'destination_port'),
    ])->filter()->join(' ');

    $signatureName = $document->generator?->name ?: data_get($seller, 'company_name', 'Authorized Signature');
    $pageQuantity = $formatNumber(data_get($totals, 'total_quantity_kg', 0), 2);
    $grandTotal = $formatNumber(data_get($totals, 'subtotal_sales', 0), 2);
    $totalQuantityPcs = (int) data_get($totals, 'total_quantity_pcs', 0);
    $totalPackageCount = (int) data_get($totals, 'total_package_count', 0);
    $appendixPages = $photoAttachments->chunk(4)->values();
    $totalPages = 1 + $appendixPages->count();
@endphp

<div class="invoice-sheet">
    <table class="invoice-table">
        <colgroup>
            <col style="width: 34%;">
            <col style="width: 32%;">
            <col style="width: 20%;">
            <col style="width: 14%;">
        </colgroup>
        <tr>
            <td rowspan="2">
                <span class="invoice-cell-label">Exporter</span>
                <div class="invoice-company">{{ data_get($seller, 'company_name', config('app.name', 'ArkipelaSpice Web')) }}</div>
                <div class="invoice-product-meta">{{ $sellerAddress }}</div>
            </td>
            <td rowspan="2" class="text-center text-top">
                @if (file_exists($companyLogo))
                    <img src="{{ asset('assetes/logo/logo.png') }}" alt="Company Logo" class="invoice-logo mb-2">
                @endif
                <h1 class="invoice-title">COMMERCIAL INVOICE</h1>
            </td>
            <td colspan="2">
                <span class="invoice-cell-label">Pages</span>
                1 of {{ $totalPages }}
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <span class="invoice-cell-label">Invoice Number &amp; Date</span>
                {{ data_get($documentPayload, 'document_number', '-') }}<br>
                {{ $formatDate($generatedAt) }}
            </td>
        </tr>
    </table>

    <div class="invoice-section-gap"></div>

    <table class="invoice-table">
        <colgroup>
            <col style="width: 25%;">
            <col style="width: 25%;">
            <col style="width: 25%;">
            <col style="width: 25%;">
        </colgroup>
        <tr>
            <td>
                <span class="invoice-cell-label">Invoice Reference</span>
                {{ data_get($orderData, 'order_code', '-') }}
            </td>
            <td>
                <span class="invoice-cell-label">PO Number</span>
                {{ data_get($orderData, 'po_number', '-') }}
            </td>
            <td>
                <span class="invoice-cell-label">Buyer Reference</span>
                {{ data_get($buyer, 'client_code', '-') }}
            </td>
            <td rowspan="2" class="invoice-buyer-cell">
                <span class="invoice-cell-label">Consignee / Buyer</span>
                <div class="invoice-product-title">{{ data_get($buyer, 'company_name', '-') }}</div>
                <div class="invoice-product-meta">{{ $buyerAddress }}</div>
                @if ($buyerContact)
                    <div class="invoice-product-meta"><strong>Contact:</strong> {{ $buyerContact }}</div>
                @endif
            </td>
        </tr>
        <tr>
            <td>
                <span class="invoice-cell-label">Order Date</span>
                {{ $formatDate(data_get($orderData, 'order_date')) }}
            </td>
            <td>
                <span class="invoice-cell-label">Delivery Date</span>
                {{ $formatDate(data_get($orderData, 'delivery_date')) }}
            </td>
            <td>
                <span class="invoice-cell-label">Currency</span>
                {{ $currency }}
            </td>
        </tr>
    </table>

    <div class="invoice-section-gap"></div>

    <table class="invoice-table">
        <colgroup>
            <col style="width: 15%;">
            <col style="width: 18%;">
            <col style="width: 17%;">
            <col style="width: 18%;">
            <col style="width: 15%;">
            <col style="width: 17%;">
        </colgroup>
        <tr>
            <td>
                <span class="invoice-cell-label">Method of Dispatch</span>
                Sea
            </td>
            <td>
                <span class="invoice-cell-label">Type of Shipment</span>
                {{ data_get($orderData, 'shipment_mode', '-') }}
            </td>
            <td>
                <span class="invoice-cell-label">Country of Origin</span>
                {{ data_get($seller, 'country', 'Indonesia') }}
            </td>
            <td>
                <span class="invoice-cell-label">Country of Final Destination</span>
                {{ data_get($orderData, 'destination_country', data_get($buyer, 'country', '-')) }}
            </td>
            <td>
                <span class="invoice-cell-label">Port of Loading</span>
                Indonesia
            </td>
            <td>
                <span class="invoice-cell-label">Port of Discharge</span>
                {{ data_get($orderData, 'destination_port', '-') }}
            </td>
        </tr>
        <tr>
            <td>
                <span class="invoice-cell-label">Vessel / Flight</span>
                {{ data_get($orderData, 'shipment_mode', '-') }}
            </td>
            <td>
                <span class="invoice-cell-label">Voyage No</span>
                {{ data_get($orderData, 'order_code', '-') }}
            </td>
            <td colspan="2">
                <span class="invoice-cell-label">Terms / Method of Payment</span>
                {{ $termsText ?: '-' }} / {{ data_get($orderData, 'payment_term', '-') }}
            </td>
            <td>
                <span class="invoice-cell-label">Date of Departure</span>
                {{ $formatDate(data_get($orderData, 'delivery_date') ?: data_get($orderData, 'order_date')) }}
            </td>
            <td>
                <span class="invoice-cell-label">Delivery Notes</span>
                {{ data_get($orderData, 'notes', '-') }}
            </td>
        </tr>
    </table>

    <div class="invoice-section-gap"></div>

    <table class="invoice-table">
        <colgroup>
            <col style="width: 11%;">
            <col style="width: 36%;">
            <col style="width: 12%;">
            <col style="width: 11%;">
            <col style="width: 10%;">
            <col style="width: 10%;">
            <col style="width: 10%;">
        </colgroup>
        <thead>
            <tr>
                <th>Product Code</th>
                <th>Description of Goods</th>
                <th>HS Code</th>
                <th class="text-right">Quantity</th>
                <th class="text-center">Unit</th>
                <th class="text-right">Unit Price</th>
                <th class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $item)
                <tr>
                    <td>{{ data_get($item, 'item_code') ?: 'ITEM-' . str_pad((string) data_get($item, 'line_number', $loop->iteration), 2, '0', STR_PAD_LEFT) }}</td>
                    <td>
                        <div class="invoice-product-title">{{ data_get($item, 'product_name', '-') }}</div>
                        @if (filled(data_get($item, 'variant_name')))
                            <div class="invoice-product-meta">Variant: {{ data_get($item, 'variant_name') }}</div>
                        @endif
                        @if (filled(data_get($item, 'barcode_number')))
                            <div class="invoice-product-meta invoice-muted">Barcode: {{ data_get($item, 'barcode_number') }}</div>
                        @endif
                        <div class="invoice-product-meta">{{ data_get($item, 'specification', '-') }}</div>
                        @if (filled(data_get($item, 'packaging_summary')))
                            <div class="invoice-product-meta invoice-muted">Packing: {{ data_get($item, 'packaging_summary') }}</div>
                        @endif
                        <div class="invoice-product-meta invoice-muted">Weight: {{ $formatNumber(data_get($item, 'quantity_kg', 0), 2) }} KG</div>
                    </td>
                    <td>{{ data_get($item, 'hs_code', '-') }}</td>
                    <td class="text-right">{{ data_get($item, 'quantity_pcs') ? number_format((int) data_get($item, 'quantity_pcs')) : $formatNumber(data_get($item, 'quantity_kg', 0), 2) }}</td>
                    <td class="text-center">{{ data_get($item, 'quantity_pcs') ? data_get($item, 'quantity_unit', 'PCS') : 'KG' }}</td>
                    <td class="text-right">{{ $formatNumber(data_get($item, 'selling_price', 0), 2) }}</td>
                    <td class="text-right">{{ $formatNumber(data_get($item, 'line_total_sales', 0), 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">No line items available.</td>
                </tr>
            @endforelse
            @for ($spacer = $items->count(); $spacer < 8; $spacer++)
                <tr>
                    <td>&nbsp;</td>
                    <td></td>
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
                <td colspan="3" class="text-right"><strong>Total This Page</strong></td>
                <td class="text-right"><strong>{{ $totalQuantityPcs > 0 ? number_format($totalQuantityPcs) : $pageQuantity }}</strong></td>
                <td class="text-center"><strong>{{ $totalQuantityPcs > 0 ? 'PCS' : 'KG' }}</strong></td>
                <td></td>
                <td class="text-right"><strong>{{ $grandTotal }}</strong></td>
            </tr>
            <tr>
                <td colspan="3" class="text-right"><strong>Consignment Total</strong></td>
                <td class="text-right"><strong>{{ $totalQuantityPcs > 0 ? number_format($totalQuantityPcs) : $pageQuantity }}</strong></td>
                <td class="text-center"><strong>{{ $totalQuantityPcs > 0 ? 'PCS' : 'KG' }}</strong></td>
                <td></td>
                <td class="text-right"><strong>{{ $grandTotal }}</strong></td>
            </tr>
        </tfoot>
    </table>

    <div class="invoice-section-gap"></div>

    <table class="invoice-table">
        <colgroup>
            <col style="width: 48%;">
            <col style="width: 37%;">
            <col style="width: 15%;">
        </colgroup>
        <tr>
            <td rowspan="4">
                <span class="invoice-cell-label">Additional Info / Seller Notes</span>
                <div><strong>Seller:</strong> {{ data_get($seller, 'company_name', config('app.name', 'Arkipela Web')) }}</div>
                <div><strong>Origin:</strong> {{ data_get($seller, 'country', 'Indonesia') }}</div>
                <div><strong>Buyer:</strong> {{ data_get($buyer, 'company_name', '-') }}</div>
                <div><strong>Total Qty:</strong> {{ $totalQuantityPcs > 0 ? number_format($totalQuantityPcs) . ' PCS' : $pageQuantity . ' KG' }}</div>
                <div><strong>Total Packages:</strong> {{ $totalPackageCount > 0 ? number_format($totalPackageCount) : '-' }}</div>
                <div><strong>Total N.W / G.W:</strong> {{ $formatNumber(data_get($totals, 'total_net_weight_kg', 0), 2) }} / {{ $formatNumber(data_get($totals, 'total_gross_weight_kg', 0), 2) }} KG</div>
                <div><strong>Total CBM:</strong> {{ $formatNumber(data_get($totals, 'total_cbm', 0), 4) }}</div>
                <div><strong>Bank Details:</strong> Available upon request.</div>
                @if (filled(data_get($orderData, 'notes')))
                    <div class="mt-2"><strong>Remarks:</strong> {{ data_get($orderData, 'notes') }}</div>
                @endif
            </td>
            <td>
                <span class="invoice-cell-label">TOTAL</span>
                {{ $currency }} {{ $grandTotal }}
            </td>
            <td>
                <span class="invoice-cell-label">Currency</span>
                {{ $currency }}
            </td>
        </tr>
        <tr>
            <td>
                <span class="invoice-cell-label">Incoterms</span>
                {{ data_get($orderData, 'incoterm', '-') }}
            </td>
            <td>
                <span class="invoice-cell-label">Destination</span>
                {{ data_get($orderData, 'destination_port', '-') }}
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <span class="invoice-cell-label">Name of Authorized Signatory</span>
                {{ $signatureName }}
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <span class="invoice-cell-label">Signature</span>
                <div class="invoice-signature-box">{{ $signatureName }}</div>
            </td>
        </tr>
    </table>
</div>

@foreach ($appendixPages as $appendixPageIndex => $appendixPhotos)
    <div class="invoice-page-break"></div>
    <div class="invoice-appendix-sheet">
        <div class="invoice-appendix-title">Attachment - Product Photos</div>
        <div class="invoice-appendix-subtitle">
            {{ data_get($documentPayload, 'document_number', '-') }} |
            Page {{ $appendixPageIndex + 2 }} of {{ $totalPages }}
        </div>

        <div class="invoice-photo-grid">
            @foreach ($appendixPhotos as $photo)
                <div class="invoice-photo-card">
                    <img src="{{ data_get($photo, 'url') }}" alt="{{ data_get($photo, 'product_name', 'Product Photo') }}"
                        class="invoice-photo-thumb">
                    <div class="invoice-photo-title">
                        {{ data_get($photo, 'product_name', '-') }}
                        @if (filled(data_get($photo, 'variant_name')))
                            - {{ data_get($photo, 'variant_name') }}
                        @endif
                        @if (filled(data_get($photo, 'item_code')))
                            ({{ data_get($photo, 'item_code') }})
                        @endif
                    </div>
                    <div class="invoice-photo-meta">
                        Supplier: {{ data_get($photo, 'supplier_name', '-') }}<br>
                        Line: {{ data_get($photo, 'line_number', '-') }} |
                        Photo {{ data_get($photo, 'photo_index', '-') }}<br>
                        @if (filled(data_get($photo, 'barcode_number')))
                            Barcode: {{ data_get($photo, 'barcode_number') }}<br>
                        @endif
                        @if (filled(data_get($photo, 'caption')))
                            Caption: {{ data_get($photo, 'caption') }}
                        @else
                            Product photo attachment
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endforeach
