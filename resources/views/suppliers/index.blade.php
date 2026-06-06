@extends('layouts.app')

@section('content')
    <div class="page-content">
        <section class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                            <div>
                                <h4 class="mb-1">Supplier List</h4>
                                <p class="text-muted mb-0">Monitor supplier sourcing, approval status, dan kapasitas supply.</p>
                            </div>
                            <a href="{{ route('suppliers.create') }}" class="btn btn-primary">Add Supplier</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ route('suppliers.index') }}" class="mb-4">
                            <div class="row">
                                <div class="col-12 col-lg-4">
                                    <label class="form-label">Search</label>
                                    <input
                                        type="text"
                                        name="search"
                                        value="{{ $filters['search'] }}"
                                        class="form-control"
                                        placeholder="Code, supplier name, PIC, city"
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
                                    <label class="form-label">Supplier Type</label>
                                    <select name="supplier_type" class="form-select">
                                        <option value="">All types</option>
                                        @foreach ($typeOptions as $option)
                                            <option value="{{ $option['value'] }}" @selected($filters['supplier_type'] === $option['value'])>
                                                {{ $option['label'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12 col-lg-2 d-flex align-items-end gap-2">
                                    <button type="submit" class="btn btn-primary w-100">Apply</button>
                                    <a href="{{ route('suppliers.index') }}" class="btn btn-light w-100">Reset</a>
                                </div>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-hover table-lg">
                                <thead>
                                    <tr>
                                        <th>Supplier Code</th>
                                        <th>Supplier Name</th>
                                        <th>Type</th>
                                        <th>Location</th>
                                        <th>Products</th>
                                        <th>Capacity</th>
                                        <th>Status</th>
                                        <th>PIC</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($suppliers as $supplier)
                                        <tr>
                                            <td class="font-semibold">{{ $supplier->supplier_code }}</td>
                                            <td>
                                                <div class="font-semibold">{{ $supplier->supplier_name }}</div>
                                                <small class="text-muted">{{ $supplier->email ?: 'No email' }}</small>
                                            </td>
                                            <td>{{ $typeLabelMap[$supplier->supplier_type] ?? '-' }}</td>
                                            <td>
                                                <div>{{ $supplier->city ?: '-' }}</div>
                                                <small class="text-muted">{{ $supplier->province ?: $supplier->country }}</small>
                                            </td>
                                            <td>{{ $supplier->products_summary ?: '-' }}</td>
                                            <td>{{ $supplier->monthly_capacity_kg ? number_format((float) $supplier->monthly_capacity_kg, 0) . ' kg' : '-' }}</td>
                                            <td>
                                                <span class="badge {{ $statusBadgeMap[$supplier->status] ?? 'bg-secondary' }}">
                                                    {{ $statusLabelMap[$supplier->status] ?? ucfirst(str_replace('_', ' ', $supplier->status)) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div>{{ $supplier->pic_name ?: '-' }}</div>
                                                <small class="text-muted">{{ $supplier->phone ?: 'No phone' }}</small>
                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center gap-2 flex-wrap">
                                                    <a href="{{ route('suppliers.show', $supplier) }}" class="btn btn-sm btn-light-primary">View</a>
                                                    <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-sm btn-light-warning">Edit</a>
                                                    <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST" onsubmit="return confirm('Delete this supplier?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-light-danger">Delete</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center text-muted py-5">
                                                Belum ada supplier. Tambahkan supplier pertama untuk mulai membangun database sourcing Archipela.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $suppliers->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
