@extends('layouts.app')

@section('content')
    @php
        $skuCount = $product->skus->count();
        $barcodeReadyCount = $product->skus->filter(fn ($sku) => filled($sku->barcode_number))->count();
        $packagingConfiguredCount = $product->skus->filter(fn ($sku) => $sku->packagings->isNotEmpty())->count();
        $firstIncompleteStep = $skuCount === 0 ? 'sku' : ($packagingConfiguredCount === 0 ? 'packaging' : 'product');
    @endphp

    <div class="page-content">
        <section class="row">
            <div class="col-12 col-xl-8">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                            <div>
                                <h4 class="mb-1">{{ $product->product_name }}</h4>
                                <small class="text-muted">{{ $product->product_code }}</small>
                                <p class="text-muted mb-0 mt-2">
                                    Workspace tunggal untuk menuntaskan product, SKU, barcode retail, dan packaging secara berurutan.
                                </p>
                            </div>
                            <div class="d-flex gap-2 flex-wrap">
                                <span class="badge {{ $statusBadgeMap[$product->status] ?? 'bg-secondary' }}">
                                    {{ $statusLabelMap[$product->status] ?? ucfirst($product->status) }}
                                </span>
                                @if ($canManageProducts)
                                    <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-primary">Edit Product</a>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row g-3 mb-4">
                            <div class="col-12 col-md-4">
                                <div class="border rounded-3 p-3 h-100">
                                    <small class="text-muted d-block mb-1">Step 1</small>
                                    <div class="fw-semibold">Product Master</div>
                                    <div class="small text-muted">{{ $product->product_name }}</div>
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="border rounded-3 p-3 h-100">
                                    <small class="text-muted d-block mb-1">Step 2</small>
                                    <div class="fw-semibold">SKU & Barcode</div>
                                    <div class="small text-muted">{{ $barcodeReadyCount }}/{{ $skuCount }} SKU punya barcode</div>
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="border rounded-3 p-3 h-100">
                                    <small class="text-muted d-block mb-1">Step 3</small>
                                    <div class="fw-semibold">Packaging</div>
                                    <div class="small text-muted">{{ $packagingConfiguredCount }} SKU sudah punya packaging</div>
                                </div>
                            </div>
                        </div>

                        <div class="accordion" id="productWorkspaceAccordion">
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingProductMaster">
                                    <button
                                        class="accordion-button {{ $firstIncompleteStep !== 'product' ? 'collapsed' : '' }}"
                                        type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#collapseProductMaster"
                                        aria-expanded="{{ $firstIncompleteStep === 'product' ? 'true' : 'false' }}"
                                        aria-controls="collapseProductMaster"
                                    >
                                        1. Product Master
                                    </button>
                                </h2>
                                <div
                                    id="collapseProductMaster"
                                    class="accordion-collapse collapse {{ $firstIncompleteStep === 'product' ? 'show' : '' }}"
                                    aria-labelledby="headingProductMaster"
                                    data-bs-parent="#productWorkspaceAccordion"
                                >
                                    <div class="accordion-body">
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
                                                <small class="text-muted d-block mb-1">Notes</small>
                                                <div class="font-semibold">{{ $product->notes ?: '-' }}</div>
                                            </div>
                                            @if ($canManageProducts)
                                                <div class="col-12 mt-3">
                                                    <a href="{{ route('products.edit', $product) }}" class="btn btn-primary">Update Product Master</a>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingSkuBarcode">
                                    <button
                                        class="accordion-button {{ $firstIncompleteStep !== 'sku' ? 'collapsed' : '' }}"
                                        type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#collapseSkuBarcode"
                                        aria-expanded="{{ $firstIncompleteStep === 'sku' ? 'true' : 'false' }}"
                                        aria-controls="collapseSkuBarcode"
                                    >
                                        2. SKU & Barcode
                                    </button>
                                </h2>
                                <div
                                    id="collapseSkuBarcode"
                                    class="accordion-collapse collapse {{ $firstIncompleteStep === 'sku' ? 'show' : '' }}"
                                    aria-labelledby="headingSkuBarcode"
                                    data-bs-parent="#productWorkspaceAccordion"
                                >
                                    <div class="accordion-body">
                                        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-3">
                                            <div>
                                                <div class="fw-semibold">Kelola varian jual dan barcode retail</div>
                                                <p class="text-muted small mb-0">
                                                    Semua SKU product ini terlihat langsung di sini, jadi user tidak perlu lompat ke banyak halaman hanya untuk cek progress.
                                                </p>
                                            </div>
                                            <div class="d-flex gap-2 flex-wrap">
                                                <a href="{{ route('products.skus.index', $product) }}" class="btn btn-sm btn-light-primary">Open Full SKU List</a>
                                                @if ($canManageProducts)
                                                    <a href="{{ route('products.skus.create', $product) }}" class="btn btn-sm btn-primary">Add SKU & Barcode</a>
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
                                                            <th>Packaging</th>
                                                            <th class="text-end">Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($product->skus as $sku)
                                                            <tr>
                                                                <td class="font-semibold">{{ $sku->sku_code }}</td>
                                                                <td>
                                                                    <div class="fw-semibold">{{ $sku->variant_name }}</div>
                                                                    <small class="text-muted">{{ $sku->brand_name ?: 'No brand' }}</small>
                                                                </td>
                                                                <td>
                                                                    <div>{{ $sku->barcode_number ?: '-' }}</div>
                                                                    <small class="text-muted">{{ $sku->barcode_type ?: 'No type yet' }}</small>
                                                                </td>
                                                                <td>
                                                                    <span class="badge {{ $sku->is_retail_sellable ? 'bg-success' : 'bg-secondary' }}">
                                                                        {{ $sku->is_retail_sellable ? 'Retail Ready' : 'Bulk Only' }}
                                                                    </span>
                                                                </td>
                                                                <td>{{ $sku->packagings->count() }} level</td>
                                                                <td class="text-end">
                                                                    <div class="d-flex justify-content-end gap-2 flex-wrap">
                                                                        <a href="{{ route('product-skus.show', $sku) }}" class="btn btn-sm btn-light-primary">View</a>
                                                                        @if ($canManageProducts)
                                                                            <a href="{{ route('product-skus.edit', $sku) }}" class="btn btn-sm btn-light-warning">Edit</a>
                                                                        @endif
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @else
                                            <div class="border rounded-3 p-3 bg-light-subtle">
                                                <div class="fw-semibold mb-1">Belum ada SKU untuk product ini.</div>
                                                <div class="text-muted small mb-3">
                                                    Mulai dari satu SKU retail dulu. Setelah itu barcode dan packaging akan jauh lebih mudah diikuti.
                                                </div>
                                                @if ($canManageProducts)
                                                    <a href="{{ route('products.skus.create', $product) }}" class="btn btn-primary">Create First SKU & Barcode</a>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingPackaging">
                                    <button
                                        class="accordion-button {{ $firstIncompleteStep !== 'packaging' ? 'collapsed' : '' }}"
                                        type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#collapsePackaging"
                                        aria-expanded="{{ $firstIncompleteStep === 'packaging' ? 'true' : 'false' }}"
                                        aria-controls="collapsePackaging"
                                    >
                                        3. Packaging Hierarchy
                                    </button>
                                </h2>
                                <div
                                    id="collapsePackaging"
                                    class="accordion-collapse collapse {{ $firstIncompleteStep === 'packaging' ? 'show' : '' }}"
                                    aria-labelledby="headingPackaging"
                                    data-bs-parent="#productWorkspaceAccordion"
                                >
                                    <div class="accordion-body">
                                        @if ($product->skus->isEmpty())
                                            <div class="border rounded-3 p-3 bg-light-subtle">
                                                <div class="fw-semibold mb-1">Packaging menunggu SKU.</div>
                                                <div class="text-muted small">Buat SKU lebih dulu, lalu setiap SKU bisa punya level packaging sendiri.</div>
                                            </div>
                                        @else
                                            <div class="row g-3">
                                                @foreach ($product->skus as $sku)
                                                    <div class="col-12">
                                                        <div class="border rounded-3 p-3">
                                                            <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-3">
                                                                <div>
                                                                    <div class="fw-semibold">{{ $sku->variant_name }}</div>
                                                                    <div class="text-muted small">
                                                                        {{ $sku->sku_code }}@if($sku->barcode_number) | {{ $sku->barcode_number }}@endif
                                                                    </div>
                                                                </div>
                                                                <div class="d-flex gap-2 flex-wrap">
                                                                    <a href="{{ route('product-skus.packagings.index', $sku) }}" class="btn btn-sm btn-light-primary">View Packaging</a>
                                                                    @if ($canManageProducts)
                                                                        <a href="{{ route('product-skus.packagings.create', $sku) }}" class="btn btn-sm btn-primary">Add Packaging</a>
                                                                    @endif
                                                                </div>
                                                            </div>

                                                            @if ($sku->packagings->isNotEmpty())
                                                                <div class="table-responsive">
                                                                    <table class="table table-sm align-middle mb-0">
                                                                        <thead>
                                                                            <tr>
                                                                                <th>Level</th>
                                                                                <th>Units</th>
                                                                                <th>Barcode</th>
                                                                                <th>Default</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            @foreach ($sku->packagings as $packaging)
                                                                                <tr>
                                                                                    <td>{{ strtoupper($packaging->level) }}</td>
                                                                                    <td>{{ $packaging->units_per_pack ?: '-' }}</td>
                                                                                    <td>{{ $packaging->barcode_number ?: '-' }}</td>
                                                                                    <td>{{ $packaging->is_default_for_level ? 'Yes' : 'No' }}</td>
                                                                                </tr>
                                                                            @endforeach
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            @else
                                                                <div class="text-muted small">
                                                                    SKU ini belum punya packaging level. Tambahkan `each`, `inner`, `case`, atau `pallet` sesuai kebutuhan.
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-xl-4">
                <div class="card">
                    <div class="card-header">
                        <h4>Workspace Snapshot</h4>
                    </div>
                    <div class="card-body">
                        <div class="list-group">
                            <div class="list-group-item">
                                <small class="text-muted d-block">Master Status</small>
                                <span class="font-semibold">{{ $statusLabelMap[$product->status] ?? '-' }}</span>
                            </div>
                            <div class="list-group-item">
                                <small class="text-muted d-block">SKU Coverage</small>
                                <span class="font-semibold">{{ $skuCount }} SKU linked</span>
                            </div>
                            <div class="list-group-item">
                                <small class="text-muted d-block">Barcode Coverage</small>
                                <span class="font-semibold">{{ $barcodeReadyCount }} SKU dengan barcode</span>
                            </div>
                            <div class="list-group-item">
                                <small class="text-muted d-block">Packaging Coverage</small>
                                <span class="font-semibold">{{ $packagingConfiguredCount }} SKU dengan packaging</span>
                            </div>
                            <div class="list-group-item">
                                <small class="text-muted d-block">Suggested Next Step</small>
                                <span class="font-semibold">
                                    {{ $skuCount === 0 ? 'Create first SKU and barcode' : ($packagingConfiguredCount === 0 ? 'Add packaging hierarchy' : 'Review and refine data') }}
                                </span>
                            </div>
                        </div>

                        <div class="d-grid gap-2 mt-4">
                            @if ($canManageProducts)
                                <a href="{{ route('products.skus.create', $product) }}" class="btn btn-primary">Add SKU & Barcode</a>
                            @endif
                            <a href="{{ route('products.index') }}" class="btn btn-light">Back to List</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
