@extends('layouts.app')

@section('content')
    <div class="page-content">
        <section class="row">
            <div class="col-12 col-lg-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <h4 class="mb-1">{{ $product->product_name }}</h4>
                            <small class="text-muted">{{ $product->product_code }}</small>
                        </div>
                        <div class="d-flex gap-2">
                            <span class="badge {{ $statusBadgeMap[$product->status] ?? 'bg-secondary' }}">
                                {{ $statusLabelMap[$product->status] ?? ucfirst($product->status) }}
                            </span>
                            @if ($canManageProducts)
                                <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-primary">Edit Product</a>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-lg-6">
                                <div class="mb-4">
                                    <small class="text-muted d-block mb-1">Category</small>
                                    <div class="font-semibold">{{ $product->category ?: '-' }}</div>
                                </div>
                            </div>
                            <div class="col-12 col-lg-6">
                                <div class="mb-4">
                                    <small class="text-muted d-block mb-1">Scientific Name</small>
                                    <div class="font-semibold">{{ $product->scientific_name ?: '-' }}</div>
                                </div>
                            </div>
                            <div class="col-12 col-lg-6">
                                <div class="mb-4">
                                    <small class="text-muted d-block mb-1">Origin Area</small>
                                    <div class="font-semibold">{{ $product->origin_area ?: '-' }}</div>
                                </div>
                            </div>
                            <div class="col-12 col-lg-6">
                                <div class="mb-4">
                                    <small class="text-muted d-block mb-1">Default Form</small>
                                    <div class="font-semibold">{{ $product->form ?: '-' }}</div>
                                </div>
                            </div>
                            <div class="col-12 col-lg-6">
                                <div class="mb-4">
                                    <small class="text-muted d-block mb-1">Default Unit</small>
                                    <div class="font-semibold">{{ $product->default_unit ?: '-' }}</div>
                                </div>
                            </div>
                            <div class="col-12 col-lg-6">
                                <div class="mb-4">
                                    <small class="text-muted d-block mb-1">Created By</small>
                                    <div class="font-semibold">{{ $product->creator?->name ?: '-' }}</div>
                                </div>
                            </div>
                            <div class="col-12 col-lg-6">
                                <div class="mb-4">
                                    <small class="text-muted d-block mb-1">Created At</small>
                                    <div class="font-semibold">{{ $product->created_at?->format('d M Y H:i') ?: '-' }}</div>
                                </div>
                            </div>
                            <div class="col-12 col-lg-6">
                                <div class="mb-4">
                                    <small class="text-muted d-block mb-1">Updated At</small>
                                    <div class="font-semibold">{{ $product->updated_at?->format('d M Y H:i') ?: '-' }}</div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div>
                                    <small class="text-muted d-block mb-1">Notes</small>
                                    <div class="font-semibold">{{ $product->notes ?: '-' }}</div>
                                </div>
                            </div>
                            <div class="col-12 mt-2">
                                <div class="border rounded-3 p-3">
                                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                                        <div>
                                            <small class="text-muted d-block">SKU Children</small>
                                            <div class="font-semibold">{{ $product->skus->count() }} SKU linked</div>
                                        </div>
                                        <div class="d-flex gap-2 flex-wrap">
                                            <a href="{{ route('products.skus.index', $product) }}" class="btn btn-sm btn-light-primary">View SKU List</a>
                                            @if ($canManageProducts)
                                                <a href="{{ route('products.skus.create', $product) }}" class="btn btn-sm btn-primary">Add SKU</a>
                                            @endif
                                        </div>
                                    </div>

                                    @if ($product->skus->isNotEmpty())
                                        <div class="table-responsive">
                                            <table class="table table-sm align-middle mb-0">
                                                <thead>
                                                    <tr>
                                                        <th>SKU Code</th>
                                                        <th>Variant</th>
                                                        <th>Barcode</th>
                                                        <th>Retail</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($product->skus->take(5) as $sku)
                                                        <tr>
                                                            <td>{{ $sku->sku_code }}</td>
                                                            <td>
                                                                <a href="{{ route('product-skus.show', $sku) }}">{{ $sku->variant_name }}</a>
                                                            </td>
                                                            <td>{{ $sku->barcode_number ?: '-' }}</td>
                                                            <td>{{ $sku->is_retail_sellable ? 'Yes' : 'No' }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        @if ($product->skus->count() > 5)
                                            <div class="text-muted small mt-2">Menampilkan 5 SKU pertama.</div>
                                        @endif
                                    @else
                                        <div class="text-muted">Belum ada SKU untuk product ini.</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h4>Master Snapshot</h4>
                    </div>
                    <div class="card-body">
                        <div class="list-group">
                            <div class="list-group-item">
                                <small class="text-muted d-block">Master Status</small>
                                <span class="font-semibold">{{ $statusLabelMap[$product->status] ?? '-' }}</span>
                            </div>
                            <div class="list-group-item">
                                <small class="text-muted d-block">Retail Readiness</small>
                                <span class="font-semibold">{{ $product->skus->isNotEmpty() ? 'SKU layer available' : 'Commodity master only' }}</span>
                            </div>
                            <div class="list-group-item">
                                <small class="text-muted d-block">Next Phase</small>
                                <span class="font-semibold">{{ $product->skus->isNotEmpty() ? 'Packaging hierarchy setup' : 'SKU and barcode setup' }}</span>
                            </div>
                        </div>

                        <div class="d-grid gap-2 mt-4">
                            @if ($canManageProducts)
                                <a href="{{ route('products.edit', $product) }}" class="btn btn-primary">Edit Product</a>
                            @endif
                            <a href="{{ route('products.index') }}" class="btn btn-light">Back to List</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
