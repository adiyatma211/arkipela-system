@extends('layouts.app')

@section('content')
    <div class="page-content">
        <section class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                            <div>
                                <h4 class="mb-1">SKU Master for {{ $product->product_name }}</h4>
                                <p class="text-muted mb-0">Kelola varian jual, UPC/GTIN, dan status retail sellable tanpa bergantung ke supplier.</p>
                            </div>
                            <div class="d-flex gap-2 flex-wrap">
                                <a href="{{ route('products.show', $product) }}" class="btn btn-light">Back to Product</a>
                                @if ($canManageProducts)
                                    <a href="{{ route('products.skus.create', $product) }}" class="btn btn-primary">Add SKU</a>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ route('products.skus.index', $product) }}" class="mb-4">
                            <div class="row g-3">
                                <div class="col-12 col-lg-6">
                                    <label class="form-label">Search</label>
                                    <input
                                        type="text"
                                        name="search"
                                        value="{{ $filters['search'] }}"
                                        class="form-control"
                                        placeholder="SKU code, variant, brand, barcode, GTIN"
                                    >
                                </div>
                                <div class="col-12 col-lg-3">
                                    <label class="form-label">Retail Sellable</label>
                                    <select name="retail" class="form-select">
                                        <option value="">All</option>
                                        <option value="1" @selected($filters['retail'] === '1')>Retail Ready</option>
                                        <option value="0" @selected($filters['retail'] === '0')>Bulk Only</option>
                                    </select>
                                </div>
                                <div class="col-12 col-lg-3">
                                    <label class="form-label">Active</label>
                                    <select name="active" class="form-select">
                                        <option value="">All</option>
                                        <option value="1" @selected($filters['active'] === '1')>Active</option>
                                        <option value="0" @selected($filters['active'] === '0')>Inactive</option>
                                    </select>
                                </div>
                                <div class="col-12 d-flex justify-content-end gap-2">
                                    <a href="{{ route('products.skus.index', $product) }}" class="btn btn-light">Reset</a>
                                    <button type="submit" class="btn btn-primary">Apply Filter</button>
                                </div>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-hover table-lg">
                                <thead>
                                    <tr>
                                        <th>SKU Code</th>
                                        <th>Variant</th>
                                        <th>Weight</th>
                                        <th>Barcode</th>
                                        <th>GTIN</th>
                                        <th>Retail</th>
                                        <th>Status</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($skus as $sku)
                                        <tr>
                                            <td class="font-semibold">{{ $sku->sku_code }}</td>
                                            <td>
                                                <div class="font-semibold">{{ $sku->variant_name }}</div>
                                                <small class="text-muted">{{ $sku->brand_name ?: 'No brand' }}</small>
                                            </td>
                                            <td>{{ $sku->net_weight ? number_format((float) $sku->net_weight, 2) . ' ' . $sku->weight_unit : '-' }}</td>
                                            <td>
                                                <div>{{ $sku->barcode_number ?: '-' }}</div>
                                                <small class="text-muted">{{ $sku->barcode_type ?: 'No barcode type' }}</small>
                                            </td>
                                            <td>{{ $sku->gtin ?: '-' }}</td>
                                            <td>
                                                <span class="badge {{ $sku->is_retail_sellable ? 'bg-success' : 'bg-secondary' }}">
                                                    {{ $sku->is_retail_sellable ? 'Retail Ready' : 'Bulk Only' }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge {{ $sku->is_active ? 'bg-primary' : 'bg-light-secondary' }}">
                                                    {{ $sku->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center gap-2 flex-wrap">
                                                    <a href="{{ route('product-skus.show', $sku) }}" class="btn btn-sm btn-light-primary">View</a>
                                                    @if ($canManageProducts)
                                                        <a href="{{ route('product-skus.edit', $sku) }}" class="btn btn-sm btn-light-warning">Edit</a>
                                                        <form action="{{ route('product-skus.destroy', $sku) }}" method="POST" onsubmit="return confirm('Delete this SKU?')">
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
                                            <td colspan="8" class="text-center text-muted py-5">
                                                Belum ada SKU untuk product ini. Tambahkan varian retail pertama untuk mulai menyimpan barcode.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $skus->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
