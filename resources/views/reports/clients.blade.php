@extends('layouts.app')

@section('content')
    <div class="page-content">
        @include('reports.partials.toolbar')

        <section class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Client Report</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Client</th>
                                        <th>Total Orders</th>
                                        <th>Revenue</th>
                                        <th>Net Profit</th>
                                        <th>Average Revenue / Order</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($rows as $row)
                                        <tr>
                                            <td>{{ $row['client'] }}</td>
                                            <td>{{ number_format($row['orders']) }}</td>
                                            <td>USD {{ number_format($row['revenue'], 2) }}</td>
                                            <td>USD {{ number_format($row['profit'], 2) }}</td>
                                            <td>USD {{ number_format($row['average_revenue'], 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">Belum ada data client pada periode ini.</td>
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
