@extends('layouts.app')

@push('styles')
    <style>
        .report-order-table {
            min-width: 1900px;
        }

        .report-order-table td,
        .report-order-table th {
            white-space: nowrap;
            vertical-align: top;
        }

        .report-order-table .report-order-wrap {
            white-space: normal;
            min-width: 180px;
        }
    </style>
@endpush

@section('content')
    <div class="page-content">
        @include('reports.partials.toolbar')

        <section class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Order Report</h4>
                        <small class="text-muted">Format internal: owner dan tim operasional bisa lihat order, supplier, packaging summary, cost, margin, dokumen, dan risk dalam satu tabel.</small>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover report-order-table">
                                <thead>
                                    <tr>
                                        <th>Order Code</th>
                                        <th>PO Number</th>
                                        <th>Client</th>
                                        <th>Destination Country</th>
                                        <th>Destination Port</th>
                                        <th>Shipment</th>
                                        <th>Status</th>
                                        <th>Order Date</th>
                                        <th>Delivery Date</th>
                                        <th>Suppliers</th>
                                        <th>Products</th>
                                        <th>Qty KG</th>
                                        <th>Qty PCS</th>
                                        <th>Packages</th>
                                        <th>Revenue</th>
                                        <th>Buying</th>
                                        <th>Gross Profit</th>
                                        <th>Net Profit</th>
                                        <th>Gross Margin</th>
                                        <th>Net Margin</th>
                                        <th>Payment Term</th>
                                        <th>Incoterm</th>
                                        <th>Document</th>
                                        <th>Risk</th>
                                        <th>Notes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($rows as $row)
                                        <tr>
                                            <td>{{ $row['order_code'] }}</td>
                                            <td>{{ $row['po_number'] }}</td>
                                            <td>{{ $row['client'] }}</td>
                                            <td>{{ $row['destination_country'] }}</td>
                                            <td>{{ $row['destination_port'] }}</td>
                                            <td>{{ $row['shipment_mode'] }}</td>
                                            <td>{{ $row['status'] }}</td>
                                            <td>{{ $row['order_date'] }}</td>
                                            <td>{{ $row['delivery_date'] }}</td>
                                            <td class="report-order-wrap">{{ $row['suppliers'] }}</td>
                                            <td class="report-order-wrap">{{ $row['products'] }}</td>
                                            <td>{{ number_format($row['qty_kg'], 2) }}</td>
                                            <td>{{ number_format($row['qty_pcs']) }}</td>
                                            <td>{{ number_format($row['packages']) }}</td>
                                            <td>USD {{ number_format($row['revenue'], 2) }}</td>
                                            <td>USD {{ number_format($row['buying'], 2) }}</td>
                                            <td>USD {{ number_format($row['gross_profit'], 2) }}</td>
                                            <td>USD {{ number_format($row['net_profit'], 2) }}</td>
                                            <td>{{ number_format($row['gross_margin'], 2) }}%</td>
                                            <td>{{ number_format($row['net_margin'], 2) }}%</td>
                                            <td class="report-order-wrap">{{ $row['payment_term'] }}</td>
                                            <td>{{ $row['incoterm'] }}</td>
                                            <td>{{ $row['document_status'] }}</td>
                                            <td>{{ $row['risk_status'] }}</td>
                                            <td class="report-order-wrap">{{ $row['notes'] }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="25" class="text-center text-muted">Belum ada data order pada periode ini.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
