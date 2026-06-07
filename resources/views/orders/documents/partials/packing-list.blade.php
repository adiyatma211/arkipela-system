@php
    $seller = data_get($documentPayload, 'seller', []);
    $buyer = data_get($documentPayload, 'buyer', []);
    $orderData = data_get($documentPayload, 'order', []);
    $items = collect(data_get($documentPayload, 'items', []));
    $packingSummary = data_get($documentPayload, 'packing_summary', []);
    $generatedAt = data_get($documentPayload, 'generated_at');
@endphp

<div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
    <div>
        <p class="text-uppercase text-muted small mb-2">Packing List</p>
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
        <small class="text-muted d-block mb-2">Consignor</small>
        <div class="fw-semibold">{{ data_get($seller, 'company_name', '-') }}</div>
        <div>{{ data_get($seller, 'country', '-') }}</div>
    </div>
    <div class="document-preview-meta-card">
        <small class="text-muted d-block mb-2">Consignee</small>
        <div class="fw-semibold">{{ data_get($buyer, 'company_name', '-') }}</div>
        <div>{{ data_get($buyer, 'address', '-') }}</div>
        <div>{{ trim(collect([data_get($buyer, 'city'), data_get($buyer, 'country')])->filter()->join(', ')) ?: '-' }}</div>
    </div>
    <div class="document-preview-meta-card">
        <small class="text-muted d-block mb-2">Shipment Reference</small>
        <div>Order: {{ data_get($orderData, 'order_code', '-') }}</div>
        <div>PO: {{ data_get($orderData, 'po_number', '-') }}</div>
        <div>Delivery Date: {{ data_get($orderData, 'delivery_date') ? \Illuminate\Support\Carbon::parse(data_get($orderData, 'delivery_date'))->format('d M Y') : '-' }}</div>
        <div>Mode: {{ data_get($orderData, 'shipment_mode', '-') }}</div>
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
                <th>Destination</th>
                <th>Remarks</th>
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
                    <td>{{ data_get($orderData, 'destination_port', '-') }}</td>
                    <td>{{ data_get($orderData, 'notes', '-') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="4" class="text-end">Total Quantity</th>
                <th class="text-end">{{ number_format((float) data_get($packingSummary, 'total_quantity_kg', 0), 2) }}</th>
                <th colspan="2"></th>
            </tr>
        </tfoot>
    </table>
</div>

<div class="document-preview-meta-grid">
    <div class="document-preview-meta-card">
        <small class="text-muted d-block mb-2">Packing Summary</small>
        <div>Total Lines: {{ data_get($packingSummary, 'line_item_count', 0) }}</div>
        <div>Total Qty: {{ number_format((float) data_get($packingSummary, 'total_quantity_kg', 0), 2) }} kg</div>
        <div>Destination Port: {{ data_get($packingSummary, 'destination_port', '-') }}</div>
    </div>
    <div class="document-preview-meta-card">
        <small class="text-muted d-block mb-2">Operational Note</small>
        <div>Package count, gross weight, dan dimensions belum dikelola di order core saat ini.</div>
        <div class="mt-2">Sprint berikutnya bisa menambahkan packaging breakdown bila field fisiknya sudah tersedia.</div>
    </div>
    <div class="document-preview-meta-card">
        <small class="text-muted d-block mb-2">Additional Notes</small>
        <div>{{ data_get($packingSummary, 'notes', 'No notes provided.') }}</div>
    </div>
</div>
