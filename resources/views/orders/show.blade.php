@extends('layouts.app')

@section('content')
    <div class="page-content">
        <section class="row">
            <div class="col-12 col-lg-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <h4 class="mb-1">{{ $order->order_code }}</h4>
                            <small class="text-muted">{{ $order->client?->company_name ?: '-' }}</small>
                        </div>
                        <div class="d-flex gap-2">
                            <span class="badge {{ $statusBadgeMap[$order->status] ?? 'bg-secondary' }}">
                                {{ $statusLabelMap[$order->status] ?? ucfirst(str_replace('_', ' ', $order->status)) }}
                            </span>
                            <a href="{{ route('orders.edit', $order) }}" class="btn btn-sm btn-primary">Edit Order</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-lg-6">
                                <div class="mb-4">
                                    <small class="text-muted d-block mb-1">Client</small>
                                    <div class="font-semibold">{{ $order->client?->company_name ?: '-' }}</div>
                                    <small class="text-muted">{{ $order->client?->client_code ?: '-' }}</small>
                                </div>
                            </div>
                            <div class="col-12 col-lg-6">
                                <div class="mb-4">
                                    <small class="text-muted d-block mb-1">Created By</small>
                                    <div class="font-semibold">{{ $order->creator?->name ?: '-' }}</div>
                                </div>
                            </div>
                            <div class="col-12 col-lg-4">
                                <div class="mb-4">
                                    <small class="text-muted d-block mb-1">Destination Country</small>
                                    <div class="font-semibold">{{ $order->destination_country ?: '-' }}</div>
                                </div>
                            </div>
                            <div class="col-12 col-lg-4">
                                <div class="mb-4">
                                    <small class="text-muted d-block mb-1">Destination Port</small>
                                    <div class="font-semibold">{{ $order->destination_port ?: '-' }}</div>
                                </div>
                            </div>
                            <div class="col-12 col-lg-4">
                                <div class="mb-4">
                                    <small class="text-muted d-block mb-1">Shipment Mode</small>
                                    <div class="font-semibold">{{ $order->shipment_mode ?: '-' }}</div>
                                </div>
                            </div>
                            <div class="col-12 col-lg-4">
                                <div class="mb-4">
                                    <small class="text-muted d-block mb-1">Order Date</small>
                                    <div class="font-semibold">{{ optional($order->order_date)->format('d M Y') ?: '-' }}</div>
                                </div>
                            </div>
                            <div class="col-12 col-lg-4">
                                <div class="mb-4">
                                    <small class="text-muted d-block mb-1">Delivery Date</small>
                                    <div class="font-semibold">{{ optional($order->delivery_date)->format('d M Y') ?: '-' }}</div>
                                </div>
                            </div>
                            <div class="col-12 col-lg-4">
                                <div class="mb-4">
                                    <small class="text-muted d-block mb-1">PO Number</small>
                                    <div class="font-semibold">{{ $order->po_number ?: '-' }}</div>
                                </div>
                            </div>
                            <div class="col-12 col-lg-4">
                                <div class="mb-4">
                                    <small class="text-muted d-block mb-1">Currency</small>
                                    <div class="font-semibold">{{ $order->currency ?: '-' }}</div>
                                </div>
                            </div>
                            <div class="col-12 col-lg-4">
                                <div class="mb-4">
                                    <small class="text-muted d-block mb-1">Incoterm</small>
                                    <div class="font-semibold">{{ $order->incoterm ?: '-' }}</div>
                                </div>
                            </div>
                            <div class="col-12 col-lg-4">
                                <div class="mb-4">
                                    <small class="text-muted d-block mb-1">Payment Term</small>
                                    <div class="font-semibold">{{ $order->payment_term ?: '-' }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive mt-2">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Supplier</th>
                                        <th>Product</th>
                                        <th>Specification</th>
                                        <th>Qty (kg)</th>
                                        <th>Selling Price</th>
                                        <th>Product Cost / kg</th>
                                        <th>Unit Margin</th>
                                        <th>Gross Line Profit</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($order->items as $item)
                                        <tr>
                                            <td>{{ $item->supplier?->supplier_name ?: '-' }}</td>
                                            <td>{{ $item->product_name }}</td>
                                            <td>{{ $item->specification ?: '-' }}</td>
                                            <td>{{ number_format((float) $item->quantity_kg, 2) }}</td>
                                            <td>{{ $order->currency }} {{ number_format((float) $item->selling_price, 2) }}</td>
                                            <td>{{ $order->currency }} {{ number_format((float) $item->buying_price, 2) }}</td>
                                            <td>{{ $order->currency }} {{ number_format((float) $item->selling_price - (float) $item->buying_price, 2) }}</td>
                                            <td>{{ $order->currency }} {{ number_format((float) $item->line_profit, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            <small class="text-muted d-block mb-1">Notes</small>
                            <div class="font-semibold">{{ $order->notes ?: '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h4>Financial Snapshot</h4>
                    </div>
                    <div class="card-body">
                        <div class="list-group">
                            <div class="list-group-item">
                                <small class="text-muted d-block">Sales Total</small>
                                <span class="font-semibold">{{ $order->currency }} {{ number_format((float) $order->subtotal_sales, 2) }}</span>
                            </div>
                            <div class="list-group-item">
                                <small class="text-muted d-block">Product Cost Total</small>
                                <span class="font-semibold">{{ $order->currency }} {{ number_format((float) $order->subtotal_buying, 2) }}</span>
                            </div>
                            <div class="list-group-item">
                                <small class="text-muted d-block">Gross Profit</small>
                                <span class="font-semibold">{{ $order->currency }} {{ number_format((float) $order->gross_profit, 2) }}</span>
                            </div>
                            <div class="list-group-item">
                                <small class="text-muted d-block">Gross Margin</small>
                                <span class="font-semibold">{{ number_format((float) $order->gross_margin, 2) }}%</span>
                            </div>
                            <div class="list-group-item">
                                <small class="text-muted d-block">Local Logistics Cost</small>
                                <span class="font-semibold">{{ $order->currency }} {{ number_format((float) $order->local_logistics_cost, 2) }}</span>
                            </div>
                            <div class="list-group-item">
                                <small class="text-muted d-block">Export Document Cost</small>
                                <span class="font-semibold">{{ $order->currency }} {{ number_format((float) $order->export_document_cost, 2) }}</span>
                            </div>
                            <div class="list-group-item">
                                <small class="text-muted d-block">Forwarding Cost</small>
                                <span class="font-semibold">{{ $order->currency }} {{ number_format((float) $order->forwarding_cost, 2) }}</span>
                            </div>
                            <div class="list-group-item">
                                <small class="text-muted d-block">Freight Cost</small>
                                <span class="font-semibold">{{ $order->currency }} {{ number_format((float) $order->freight_cost, 2) }}</span>
                            </div>
                            <div class="list-group-item">
                                <small class="text-muted d-block">Insurance Cost</small>
                                <span class="font-semibold">{{ $order->currency }} {{ number_format((float) $order->insurance_cost, 2) }}</span>
                            </div>
                            <div class="list-group-item">
                                <small class="text-muted d-block">Compliance Cost</small>
                                <span class="font-semibold">{{ $order->currency }} {{ number_format((float) $order->compliance_cost, 2) }}</span>
                            </div>
                            <div class="list-group-item">
                                <small class="text-muted d-block">Destination Cost</small>
                                <span class="font-semibold">{{ $order->currency }} {{ number_format((float) $order->destination_cost, 2) }}</span>
                            </div>
                            <div class="list-group-item">
                                <small class="text-muted d-block">Misc Cost</small>
                                <span class="font-semibold">{{ $order->currency }} {{ number_format((float) $order->misc_cost, 2) }}</span>
                            </div>
                            <div class="list-group-item">
                                <small class="text-muted d-block">Total Export Costs</small>
                                <span class="font-semibold">{{ $order->currency }} {{ number_format((float) $order->total_additional_cost, 2) }}</span>
                            </div>
                            <div class="list-group-item">
                                <small class="text-muted d-block">Net Profit</small>
                                <span class="font-semibold">{{ $order->currency }} {{ number_format((float) $order->net_profit, 2) }}</span>
                            </div>
                            <div class="list-group-item">
                                <small class="text-muted d-block">Net Margin</small>
                                <span class="font-semibold">{{ number_format((float) $order->net_margin, 2) }}%</span>
                            </div>
                            <div class="list-group-item">
                                <small class="text-muted d-block">Confirmed At</small>
                                <span class="font-semibold">{{ optional($order->confirmed_at)->format('d M Y H:i') ?: '-' }}</span>
                            </div>
                        </div>

                        <div class="d-grid gap-2 mt-4">
                            <a href="{{ route('orders.edit', $order) }}" class="btn btn-primary">Edit Order</a>
                            <a href="{{ route('orders.index') }}" class="btn btn-light">Back to List</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
