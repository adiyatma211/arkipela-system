@extends('layouts.app')

@section('content')
    @php
        $products = $supplier->resolvedProducts();
        $productsSummary = $supplier->resolvedProductsSummary();
        $totalCapacity = $supplier->resolvedMonthlyCapacityKg();
        $minimumOrder = $supplier->resolvedMinimumOrderKg();
    @endphp
    <div class="page-content">
        <section class="row">
            <div class="col-12 col-lg-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <h4 class="mb-1">{{ $supplier->supplier_name }}</h4>
                            <small class="text-muted">{{ $supplier->supplier_code }}</small>
                        </div>
                        <div class="d-flex gap-2">
                            <span class="badge {{ $statusBadgeMap[$supplier->status] ?? 'bg-secondary' }}">
                                {{ $statusLabelMap[$supplier->status] ?? ucfirst(str_replace('_', ' ', $supplier->status)) }}
                            </span>
                            <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-sm btn-primary">Edit Supplier</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-lg-6">
                                <div class="mb-4">
                                    <small class="text-muted d-block mb-1">Supplier Type</small>
                                    <div class="font-semibold">{{ $typeLabelMap[$supplier->supplier_type] ?? '-' }}</div>
                                </div>
                            </div>
                            <div class="col-12 col-lg-6">
                                <div class="mb-4">
                                    <small class="text-muted d-block mb-1">Products Supplied</small>
                                    <div class="font-semibold">{{ $productsSummary ?: '-' }}</div>
                                </div>
                            </div>
                            <div class="col-12 col-lg-6">
                                <div class="mb-4">
                                    <small class="text-muted d-block mb-1">PIC Name</small>
                                    <div class="font-semibold">{{ $supplier->pic_name ?: '-' }}</div>
                                </div>
                            </div>
                            <div class="col-12 col-lg-6">
                                <div class="mb-4">
                                    <small class="text-muted d-block mb-1">Phone</small>
                                    <div class="font-semibold">{{ $supplier->phone ?: '-' }}</div>
                                </div>
                            </div>
                            <div class="col-12 col-lg-6">
                                <div class="mb-4">
                                    <small class="text-muted d-block mb-1">Email</small>
                                    <div class="font-semibold">{{ $supplier->email ?: '-' }}</div>
                                </div>
                            </div>
                            <div class="col-12 col-lg-6">
                                <div class="mb-4">
                                    <small class="text-muted d-block mb-1">Created By</small>
                                    <div class="font-semibold">{{ $supplier->creator?->name ?: '-' }}</div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-4">
                                    <small class="text-muted d-block mb-1">Address</small>
                                    <div class="font-semibold">{{ $supplier->address ?: '-' }}</div>
                                </div>
                            </div>
                            <div class="col-12 col-lg-4">
                                <div class="mb-4">
                                    <small class="text-muted d-block mb-1">City</small>
                                    <div class="font-semibold">{{ $supplier->city ?: '-' }}</div>
                                </div>
                            </div>
                            <div class="col-12 col-lg-4">
                                <div class="mb-4">
                                    <small class="text-muted d-block mb-1">Province</small>
                                    <div class="font-semibold">{{ $supplier->province ?: '-' }}</div>
                                </div>
                            </div>
                            <div class="col-12 col-lg-4">
                                <div class="mb-4">
                                    <small class="text-muted d-block mb-1">Country</small>
                                    <div class="font-semibold">{{ $supplier->country ?: '-' }}</div>
                                </div>
                            </div>
                            <div class="col-12 col-lg-6">
                                <div class="mb-4">
                                    <small class="text-muted d-block mb-1">Monthly Capacity</small>
                                    <div class="font-semibold">{{ $totalCapacity !== null ? number_format($totalCapacity, 0) . ' kg' : '-' }}</div>
                                </div>
                            </div>
                            <div class="col-12 col-lg-6">
                                <div class="mb-4">
                                    <small class="text-muted d-block mb-1">Minimum Order</small>
                                    <div class="font-semibold">{{ $minimumOrder !== null ? number_format($minimumOrder, 0) . ' kg' : '-' }}</div>
                                </div>
                            </div>
                            <div class="col-12 col-lg-6">
                                <div class="mb-4">
                                    <small class="text-muted d-block mb-1">Payment Term</small>
                                    <div class="font-semibold">{{ $supplier->payment_term ?: '-' }}</div>
                                </div>
                            </div>
                            <div class="col-12 col-lg-6">
                                <div class="mb-4">
                                    <small class="text-muted d-block mb-1">Legal Status</small>
                                    <div class="font-semibold">{{ $supplier->legal_status ?: '-' }}</div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-4">
                                    <small class="text-muted d-block mb-2">Product Breakdown</small>
                                    @if ($products->isNotEmpty())
                                        <div class="table-responsive">
                                            <table class="table table-sm mb-0">
                                                <thead>
                                                    <tr>
                                                        <th>Product</th>
                                                        <th>Monthly Capacity</th>
                                                        <th>Minimum Order</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($products as $product)
                                                        <tr>
                                                            <td>{{ $product->product_name }}</td>
                                                            <td>{{ $product->monthly_capacity_kg ? number_format((float) $product->monthly_capacity_kg, 0) . ' kg' : '-' }}</td>
                                                            <td>{{ $product->minimum_order_kg ? number_format((float) $product->minimum_order_kg, 0) . ' kg' : '-' }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="font-semibold">-</div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-12">
                                <div>
                                    <small class="text-muted d-block mb-1">Notes</small>
                                    <div class="font-semibold">{{ $supplier->notes ?: '-' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h4>Qualification Snapshot</h4>
                    </div>
                    <div class="card-body">
                        <div class="list-group">
                            <div class="list-group-item">
                                <small class="text-muted d-block">Approval Rule</small>
                                <span class="font-semibold">Only Approved or Active suppliers can be used in orders later.</span>
                            </div>
                            <div class="list-group-item">
                                <small class="text-muted d-block">Data Completeness</small>
                                <span class="font-semibold">{{ $supplier->email && $supplier->phone && $products->isNotEmpty() ? 'Good' : 'Needs enrichment' }}</span>
                            </div>
                            <div class="list-group-item">
                                <small class="text-muted d-block">Status</small>
                                <span class="font-semibold">{{ $statusLabelMap[$supplier->status] ?? '-' }}</span>
                            </div>
                        </div>

                        <div class="d-grid gap-2 mt-4">
                            <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-primary">Edit Supplier</a>
                            <a href="{{ route('suppliers.index') }}" class="btn btn-light">Back to List</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
