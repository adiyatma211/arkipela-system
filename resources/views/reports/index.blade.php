@extends('layouts.app')

@push('styles')
    <style>
        .reports-kpi-card .card-body {
            padding: 1.25rem 1.35rem;
        }

        .reports-kpi {
            display: flex;
            align-items: center;
            gap: 0.95rem;
            min-height: 108px;
        }

        .reports-kpi-icon {
            width: 50px;
            height: 50px;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            flex-shrink: 0;
        }

        .reports-kpi-icon i {
            font-size: 1.3rem;
            line-height: 1;
        }

        .reports-kpi-icon.blue {
            background: #57caeb;
        }

        .reports-kpi-icon.green {
            background: #5ddab4;
        }

        .reports-kpi-icon.purple {
            background: #9694ff;
        }

        .reports-kpi-icon.red {
            background: #ff7976;
        }

        .reports-kpi-value {
            font-size: 1.4rem;
            font-weight: 800;
            color: #25396f;
            line-height: 1.2;
            margin-bottom: 0.2rem;
        }
    </style>
@endpush

@section('content')
    <div class="page-content">
        @include('reports.partials.toolbar')

        <section class="row">
            @foreach ($summaryCards as $card)
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card reports-kpi-card">
                        <div class="card-body">
                            <div class="reports-kpi">
                                <div class="reports-kpi-icon {{ $card['color'] }}">
                                    <i class="bi {{ $card['icon'] }}"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block mb-1">{{ $card['label'] }}</small>
                                    <div class="reports-kpi-value">{{ $card['value'] }}</div>
                                    <small class="text-muted">{{ $card['hint'] }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </section>

        <section class="row">
            <div class="col-12 col-xl-7">
                <div class="card">
                    <div class="card-header">
                        <h4>Order Status Summary</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Status</th>
                                        <th>Total Orders</th>
                                        <th>Revenue</th>
                                        <th>Net Profit</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($statusSummary as $status)
                                        <tr>
                                            <td>{{ $status['label'] }}</td>
                                            <td>{{ number_format($status['count']) }}</td>
                                            <td>USD {{ number_format($status['revenue'], 2) }}</td>
                                            <td>USD {{ number_format($status['profit'], 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted">Belum ada data order.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-xl-5">
                <div class="card">
                    <div class="card-header">
                        <h4>Top Clients</h4>
                    </div>
                    <div class="card-body">
                        @forelse ($topClients as $client)
                            <div class="border rounded p-3 mb-3">
                                <div class="d-flex justify-content-between align-items-start gap-3">
                                    <div>
                                        <h6 class="mb-1">{{ $client['client'] }}</h6>
                                        <small class="text-muted">{{ number_format($client['orders']) }} order</small>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-semibold">USD {{ number_format($client['revenue'], 2) }}</div>
                                        <small class="text-muted">Profit USD {{ number_format($client['profit'], 2) }}</small>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted mb-0">Belum ada data client report.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </section>

        <section class="row">
            <div class="col-12 col-xl-8">
                <div class="card">
                    <div class="card-header">
                        <h4>Recent Orders</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Order Code</th>
                                        <th>Client</th>
                                        <th>Status</th>
                                        <th>Order Date</th>
                                        <th>Delivery</th>
                                        <th>Revenue</th>
                                        <th>Net Profit</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($recentOrders as $order)
                                        <tr>
                                            <td>{{ $order['order_code'] }}</td>
                                            <td>{{ $order['client'] }}</td>
                                            <td>{{ $order['status'] }}</td>
                                            <td>{{ $order['order_date'] }}</td>
                                            <td>{{ $order['delivery_date'] }}</td>
                                            <td>USD {{ number_format($order['revenue'], 2) }}</td>
                                            <td>USD {{ number_format($order['profit'], 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted">Belum ada recent order.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-xl-4">
                <div class="card">
                    <div class="card-header">
                        <h4>Top Products</h4>
                    </div>
                    <div class="card-body">
                        @forelse ($topProducts as $product)
                            <div class="d-flex justify-content-between align-items-center border rounded p-3 mb-3">
                                <div>
                                    <h6 class="mb-1">{{ $product['product'] }}</h6>
                                    <small class="text-muted">{{ number_format($product['lines']) }} line item</small>
                                </div>
                                <div class="text-end fw-semibold">
                                    {{ number_format($product['quantity_kg'], 2) }} kg
                                </div>
                            </div>
                        @empty
                            <p class="text-muted mb-0">Belum ada data product report.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
