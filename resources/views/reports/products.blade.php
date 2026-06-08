@extends('layouts.app')

@section('content')
    <div class="page-content">
        @include('reports.partials.toolbar')

        <section class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Product Report</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Line Items</th>
                                        <th>Quantity (kg)</th>
                                        <th>Sales</th>
                                        <th>Buying</th>
                                        <th>Profit</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($rows as $row)
                                        <tr>
                                            <td>{{ $row['product'] }}</td>
                                            <td>{{ number_format($row['lines']) }}</td>
                                            <td>{{ number_format($row['quantity_kg'], 2) }}</td>
                                            <td>USD {{ number_format($row['sales'], 2) }}</td>
                                            <td>USD {{ number_format($row['buying'], 2) }}</td>
                                            <td>USD {{ number_format($row['profit'], 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">Belum ada data product pada periode ini.</td>
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
