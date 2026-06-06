@extends('layouts.app')

@section('content')
    <div class="page-content">
        <section class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                            <div>
                                <h4 class="mb-1">Order Management</h4>
                                <p class="text-muted mb-0">Pantau order client, cost sheet ekspor, gross profit, dan net export profit per transaksi.</p>
                            </div>
                            <a href="{{ route('orders.create') }}" class="btn btn-primary">Create Order</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ route('orders.index') }}" class="mb-4">
                            <div class="row g-3">
                                <div class="col-12 col-lg-4">
                                    <label class="form-label">Search</label>
                                    <input
                                        type="text"
                                        name="search"
                                        value="{{ $filters['search'] }}"
                                        class="form-control"
                                        placeholder="Order code, PO number, client"
                                    >
                                </div>
                                <div class="col-12 col-lg-3">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-select">
                                        <option value="">All status</option>
                                        @foreach ($statusOptions as $option)
                                            <option value="{{ $option['value'] }}" @selected($filters['status'] === $option['value'])>
                                                {{ $option['label'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12 col-lg-3">
                                    <label class="form-label">Client</label>
                                    <select name="client_id" class="form-select">
                                        <option value="">All clients</option>
                                        @foreach ($clientOptions as $clientOption)
                                            <option value="{{ $clientOption->id }}" @selected($filters['client_id'] === (string) $clientOption->id)>
                                                {{ $clientOption->company_name }} ({{ $clientOption->client_code }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12 col-lg-2 d-flex align-items-end gap-2">
                                    <button type="submit" class="btn btn-primary w-100">Apply</button>
                                    <a href="{{ route('orders.index') }}" class="btn btn-light w-100">Reset</a>
                                </div>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-hover table-lg">
                                <thead>
                                    <tr>
                                        <th>Order</th>
                                        <th>Client</th>
                                        <th>Destination</th>
                                        <th>Items</th>
                                        <th>Sales Total</th>
                                        <th>Product Cost</th>
                                        <th>Gross Profit</th>
                                        <th>Net Profit</th>
                                        <th>Status</th>
                                        <th>Schedule</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($orders as $order)
                                        <tr>
                                            <td>
                                                <div class="font-semibold">{{ $order->order_code }}</div>
                                                <small class="text-muted">{{ $order->po_number ?: 'No PO number' }}</small>
                                            </td>
                                            <td>
                                                <div class="font-semibold">{{ $order->client?->company_name ?: '-' }}</div>
                                                <small class="text-muted">{{ $order->client?->client_code ?: '-' }}</small>
                                            </td>
                                            <td>
                                                <div>{{ $order->destination_country ?: '-' }}</div>
                                                <small class="text-muted">{{ $order->destination_port ?: ($order->shipment_mode ?: 'No destination detail') }}</small>
                                            </td>
                                            <td>
                                                <div>{{ $order->items->count() }} item(s)</div>
                                                <small class="text-muted">{{ $order->items->pluck('product_name')->take(2)->implode(', ') ?: 'No product' }}</small>
                                            </td>
                                            <td>{{ $order->currency }} {{ number_format((float) $order->subtotal_sales, 2) }}</td>
                                            <td>{{ $order->currency }} {{ number_format((float) $order->subtotal_buying, 2) }}</td>
                                            <td>
                                                <div>{{ $order->currency }} {{ number_format((float) $order->gross_profit, 2) }}</div>
                                                <small class="text-muted">{{ number_format((float) $order->gross_margin, 2) }}%</small>
                                            </td>
                                            <td>
                                                <div>{{ $order->currency }} {{ number_format((float) $order->net_profit, 2) }}</div>
                                                <small class="text-muted">{{ number_format((float) $order->net_margin, 2) }}%</small>
                                            </td>
                                            <td>
                                                <span class="badge {{ $statusBadgeMap[$order->status] ?? 'bg-secondary' }}">
                                                    {{ $statusLabelMap[$order->status] ?? ucfirst(str_replace('_', ' ', $order->status)) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div>{{ optional($order->order_date)->format('d M Y') ?: '-' }}</div>
                                                <small class="text-muted">
                                                    Delivery {{ optional($order->delivery_date)->format('d M Y') ?: 'TBD' }}
                                                </small>
                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center gap-2 flex-wrap">
                                                    <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-light-primary">View</a>
                                                    <a href="{{ route('orders.edit', $order) }}" class="btn btn-sm btn-light-warning">Edit</a>
                                                    <form action="{{ route('orders.destroy', $order) }}" method="POST" onsubmit="return confirm('Delete this order?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-light-danger">Delete</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="10" class="text-center text-muted py-5">
                                                Belum ada order. Buat order pertama untuk mulai menghitung revenue pipeline dan net export profit.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $orders->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
