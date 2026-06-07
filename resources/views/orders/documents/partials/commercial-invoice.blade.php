@php
    $seller = data_get($documentPayload, 'seller', []);
    $buyer = data_get($documentPayload, 'buyer', []);
    $orderData = data_get($documentPayload, 'order', []);
    $items = collect(data_get($documentPayload, 'items', []));
    $totals = data_get($documentPayload, 'totals', []);
    $currency = data_get($orderData, 'currency', 'USD');
    $generatedAt = data_get($documentPayload, 'generated_at');
@endphp

<div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
    <div>
        <p class="text-uppercase text-muted small mb-2">Commercial Invoice</p>
        <h2 class="mb-2">{{ data_get($documentPayload, 'document_number', '-') }}</h2>
        <div class="text-muted">Generated {{ $generatedAt ? \Illuminate\Support\Carbon::parse($generatedAt)->format('d M Y H:i') : '-' }}</div>
    </div>
    <div class="text-end">
        <h4 class="mb-1">{{ data_get($seller, 'company_name', '-') }}</h4>
        <div>{{ data_get($seller, 'country', '-') }}</div>
    </div>
</div>

<div class="document-preview-meta-grid mb-4">
    <div class="document-preview-meta-card">
        <small class="text-muted d-block mb-2">Seller</small>
        <div class="fw-semibold">{{ data_get($seller, 'company_name', '-') }}</div>
        <div>{{ data_get($seller, 'country', '-') }}</div>
    </div>
    <div class="document-preview-meta-card">
        <small class="text-muted d-block mb-2">Buyer</small>
        <div class="fw-semibold">{{ data_get($buyer, 'company_name', '-') }}</div>
        <div>{{ data_get($buyer, 'address', '-') }}</div>
        <div>{{ trim(collect([data_get($buyer, 'city'), data_get($buyer, 'country')])->filter()->join(', ')) ?: '-' }}</div>
        <div class="mt-2">PIC: {{ data_get($buyer, 'pic_name', '-') }}</div>
    </div>
    <div class="document-preview-meta-card">
        <small class="text-muted d-block mb-2">Shipment Reference</small>
        <div>Order: {{ data_get($orderData, 'order_code', '-') }}</div>
        <div>PO: {{ data_get($orderData, 'po_number', '-') }}</div>
        <div>Order Date: {{ data_get($orderData, 'order_date') ? \Illuminate\Support\Carbon::parse(data_get($orderData, 'order_date'))->format('d M Y') : '-' }}</div>
        <div>Incoterm: {{ data_get($orderData, 'incoterm', '-') }}</div>
        <div>Payment Term: {{ data_get($orderData, 'payment_term', '-') }}</div>
    </div>
</div>

<div class="table-responsive mb-4">
    <table class="table table-bordered document-preview-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Product</th>
                <th>Specification</th>
                <th>Supplier</th>
                <th class="text-end">Qty (kg)</th>
                <th class="text-end">Unit Price</th>
                <th class="text-end">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($items as $item)
                <tr>
                    <td>{{ data_get($item, 'line_number') }}</td>
                    <td>{{ data_get($item, 'product_name', '-') }}</td>
                    <td>{{ data_get($item, 'specification', '-') }}</td>
                    <td>{{ data_get($item, 'supplier_name', '-') }}</td>
                    <td class="text-end">{{ number_format((float) data_get($item, 'quantity_kg', 0), 2) }}</td>
                    <td class="text-end">{{ $currency }} {{ number_format((float) data_get($item, 'selling_price', 0), 2) }}</td>
                    <td class="text-end">{{ $currency }} {{ number_format((float) data_get($item, 'line_total_sales', 0), 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="4" class="text-end">Total</th>
                <th class="text-end">{{ number_format((float) data_get($totals, 'total_quantity_kg', 0), 2) }}</th>
                <th></th>
                <th class="text-end">{{ $currency }} {{ number_format((float) data_get($totals, 'subtotal_sales', 0), 2) }}</th>
            </tr>
        </tfoot>
    </table>
</div>

<div class="document-preview-meta-grid">
    <div class="document-preview-meta-card">
        <small class="text-muted d-block mb-2">Destination</small>
        <div>{{ data_get($orderData, 'destination_port', '-') }}</div>
        <div>{{ data_get($orderData, 'destination_country', '-') }}</div>
        <div>Mode: {{ data_get($orderData, 'shipment_mode', '-') }}</div>
    </div>
    <div class="document-preview-meta-card">
        <small class="text-muted d-block mb-2">Commercial Summary</small>
        <div>Subtotal Sales: {{ $currency }} {{ number_format((float) data_get($totals, 'subtotal_sales', 0), 2) }}</div>
        <div>Gross Profit: {{ $currency }} {{ number_format((float) data_get($totals, 'gross_profit', 0), 2) }}</div>
        <div>Net Profit: {{ $currency }} {{ number_format((float) data_get($totals, 'net_profit', 0), 2) }}</div>
    </div>
    <div class="document-preview-meta-card">
        <small class="text-muted d-block mb-2">Notes</small>
        <div>{{ data_get($orderData, 'notes', 'No notes provided.') }}</div>
    </div>
</div>
