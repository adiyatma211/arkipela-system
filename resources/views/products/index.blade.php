@extends('layouts.app')

@section('content')
    <div class="page-content">
        <section class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                            <div>
                                <h4 class="mb-1">Product Master</h4>
                                <p class="text-muted mb-0">Kelola commodity master yang menjadi fondasi supplier mapping dan retail SKU berikutnya.</p>
                            </div>
                            @if ($canManageProducts)
                                <a href="{{ route('products.create') }}" class="btn btn-primary">Add Product</a>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ route('products.index') }}" class="mb-4">
                            <div class="row g-3">
                                <div class="col-12 col-lg-5">
                                    <label class="form-label">Search</label>
                                    <input
                                        type="text"
                                        name="search"
                                        value="{{ $filters['search'] }}"
                                        class="form-control"
                                        placeholder="Code, product, scientific name, origin"
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
                                <div class="col-12 col-lg-4">
                                    <label class="form-label">Category</label>
                                    <input
                                        type="text"
                                        name="category"
                                        value="{{ $filters['category'] }}"
                                        class="form-control"
                                        placeholder="Spices, Herbs"
                                    >
                                </div>
                                <div class="col-12 d-flex justify-content-end gap-2">
                                    <a href="{{ route('products.index') }}" class="btn btn-light">Reset</a>
                                    <button type="submit" class="btn btn-primary">Apply Filter</button>
                                </div>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-hover table-lg">
                                <thead>
                                    <tr>
                                        <th>Product Code</th>
                                        <th>Product</th>
                                        <th>Category</th>
                                        <th>Origin</th>
                                        <th>Form</th>
                                        <th>Unit</th>
                                        <th>Status</th>
                                        <th>Created By</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($products as $product)
                                        <tr>
                                            <td class="font-semibold">{{ $product->product_code }}</td>
                                            <td>
                                                <div class="font-semibold">{{ $product->product_name }}</div>
                                                <small class="text-muted">{{ $product->scientific_name ?: 'No scientific name' }}</small>
                                            </td>
                                            <td>{{ $product->category ?: '-' }}</td>
                                            <td>{{ $product->origin_area ?: '-' }}</td>
                                            <td>{{ $product->form ?: '-' }}</td>
                                            <td>{{ $product->default_unit ?: '-' }}</td>
                                            <td>
                                                <span class="badge {{ $statusBadgeMap[$product->status] ?? 'bg-secondary' }}">
                                                    {{ $statusLabelMap[$product->status] ?? ucfirst($product->status) }}
                                                </span>
                                            </td>
                                            <td>{{ $product->creator?->name ?: '-' }}</td>
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center gap-2 flex-wrap">
                                                    <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-light-primary">View</a>
                                                    @if ($canManageProducts)
                                                        <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-light-warning">Edit</a>
                                                        <form action="{{ route('products.destroy', $product) }}" method="POST" onsubmit="return confirm('Delete this product?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-light-danger">Delete</button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center text-muted py-5">
                                                Belum ada product master. Tambahkan commodity pertama sebelum supplier linkage dan SKU retail dibangun.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $products->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
