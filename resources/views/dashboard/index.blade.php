@extends('layouts.app')

@push('styles')
    <style>
        .dashboard-kpi-card .card-body {
            padding: 1.35rem 1.4rem;
        }

        .dashboard-kpi {
            display: flex;
            align-items: center;
            gap: 1rem;
            min-height: 110px;
        }

        .dashboard-kpi-icon {
            width: 52px;
            height: 52px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .dashboard-kpi-icon i {
            width: 100%;
            height: 100%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 1.45rem;
            line-height: 1;
            vertical-align: 0;
        }

        .dashboard-kpi-icon i::before {
            display: block;
            line-height: 1;
            vertical-align: 0;
            transform: translateY(0);
        }

        .dashboard-kpi-icon.blue {
            background-color: #57caeb;
        }

        .dashboard-kpi-icon.purple {
            background-color: #9694ff;
        }

        .dashboard-kpi-icon.green {
            background-color: #5ddab4;
        }

        .dashboard-kpi-icon.red {
            background-color: #ff7976;
        }

        .dashboard-kpi-content {
            min-width: 0;
        }

        .dashboard-kpi-content h6 {
            margin-bottom: 0.35rem;
            font-size: 1.05rem;
            line-height: 1.35;
        }

        .dashboard-kpi-content .dashboard-kpi-value {
            margin-bottom: 0.15rem;
            font-size: 1.45rem;
            font-weight: 800;
            color: #25396f;
            line-height: 1.2;
        }
    </style>
@endpush

@section('content')
    <div class="page-content">
        <section class="row">
            <div class="col-12">
                <div class="row">
                    @foreach ($summaryCards as $card)
                        <div class="col-12 col-sm-6 col-lg-3 col-xl-3">
                            <div class="card dashboard-kpi-card">
                                <div class="card-body">
                                    <div class="dashboard-kpi">
                                        <div class="dashboard-kpi-icon {{ $card['color'] }}">
                                            <i class="bi {{ $card['icon'] }}"></i>
                                        </div>
                                        <div class="dashboard-kpi-content">
                                            <h6 class="text-muted font-semibold">{{ $card['label'] }}</h6>
                                            <div class="dashboard-kpi-value">{{ $card['value'] }}</div>
                                            <small class="text-muted">{{ $card['hint'] }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="row">
            <div class="col-12 col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h4>Sales Pipeline Summary</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach ($salesPipeline as $stage)
                                <div class="col-12 col-sm-6 col-lg-3">
                                    <div class="border rounded p-3 h-100">
                                        <small class="text-muted d-block mb-1">{{ $stage['label'] }}</small>
                                        <h4 class="mb-0">{{ $stage['count'] }}</h4>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h4>QC Summary</h4>
                    </div>
                    <div class="card-body">
                        @foreach ($qcSummary as $item)
                            <div class="d-flex justify-content-between align-items-center border rounded p-3 mb-3">
                                <div>
                                    <h6 class="mb-1">{{ $item['label'] }}</h6>
                                    <small class="text-muted">Batch / report status</small>
                                </div>
                                <span class="badge {{ $item['badge'] }}">{{ $item['count'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        <section class="row">
            <div class="col-12 col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h4>Active Order Summary</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-lg">
                                <thead>
                                    <tr>
                                        <th>Order Code</th>
                                        <th>Client</th>
                                        <th>Country</th>
                                        <th>Product</th>
                                        <th>Quantity</th>
                                        <th>Current Status</th>
                                        <th>Target Shipment</th>
                                        <th>Estimated Revenue</th>
                                        <th>Estimated Margin</th>
                                        <th>Risk Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($activeOrders as $order)
                                        <tr>
                                            <td>{{ $order['order_code'] }}</td>
                                            <td>{{ $order['client'] }}</td>
                                            <td>{{ $order['country'] }}</td>
                                            <td>{{ $order['product'] }}</td>
                                            <td>{{ $order['quantity'] }}</td>
                                            <td>{{ $order['status'] }}</td>
                                            <td>{{ $order['target_shipment_date'] }}</td>
                                            <td>{{ $order['estimated_revenue'] }}</td>
                                            <td>{{ $order['estimated_margin'] }}</td>
                                            <td>{{ $order['risk_status'] }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="10" class="text-center text-muted">
                                                Belum ada order aktif. Section ini akan otomatis menampilkan order berjalan setelah Sprint 4.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h4>Risk Alert</h4>
                    </div>
                    <div class="card-body">
                        @foreach ($riskAlerts as $alert)
                            <div class="border rounded p-3 mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="mb-0">{{ $alert['title'] }}</h6>
                                    <span class="badge {{ $alert['level'] === 'Critical' ? 'bg-light-danger' : ($alert['level'] === 'High' ? 'bg-light-warning' : 'bg-light-primary') }}">
                                        {{ $alert['level'] }}
                                    </span>
                                </div>
                                <small class="text-muted">{{ $alert['status'] }}</small>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        <section class="row">
            <div class="col-12 col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h4>Supplier Performance</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-lg">
                                <thead>
                                    <tr>
                                        <th>Supplier</th>
                                        <th>QC Pass Rate</th>
                                        <th>Average Price</th>
                                        <th>Supply Capacity</th>
                                        <th>Delivery Reliability</th>
                                        <th>Response Speed</th>
                                        <th>Total Orders</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($supplierPerformance as $supplier)
                                        <tr>
                                            <td>{{ $supplier['supplier'] }}</td>
                                            <td>{{ $supplier['qc_pass_rate'] }}</td>
                                            <td>{{ $supplier['average_price'] }}</td>
                                            <td>{{ $supplier['supply_capacity'] }}</td>
                                            <td>{{ $supplier['delivery_reliability'] }}</td>
                                            <td>{{ $supplier['response_speed'] }}</td>
                                            <td>{{ $supplier['total_orders_supplied'] }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted">
                                                Ranking supplier akan muncul setelah modul Supplier dan QC mulai menghasilkan data performa.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h4>Dashboard Reading Guide</h4>
                    </div>
                    <div class="card-body">
                        <div class="list-group">
                            <div class="list-group-item">Owner akan memantau order aktif, revenue pipeline, dan gross profit dari satu halaman.</div>
                            <div class="list-group-item">Sales pipeline akan terbaca dari modul Client Management / CRM.</div>
                            <div class="list-group-item">Active order summary akan terbaca dari modul Order Management.</div>
                            <div class="list-group-item">QC summary, payment, dan shipment akan diaktifkan penuh pada sprint lanjutan.</div>
                            <div class="list-group-item">Risk alert akan menjadi prioritas operasional agar owner cepat tahu titik masalah.</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
