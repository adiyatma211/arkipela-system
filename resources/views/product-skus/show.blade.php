@extends('layouts.app')

@section('content')
    <div class="page-content">
        <section class="row">
            <div class="col-12 col-lg-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <h4 class="mb-1">{{ $productSku->variant_name }}</h4>
                            <small class="text-muted">{{ $productSku->sku_code }}</small>
                        </div>
                        <div class="d-flex gap-2 flex-wrap">
                            <span class="badge {{ $productSku->is_retail_sellable ? 'bg-success' : 'bg-secondary' }}">
                                {{ $productSku->is_retail_sellable ? 'Retail Ready' : 'Bulk Only' }}
                            </span>
                            <span class="badge {{ $productSku->is_active ? 'bg-primary' : 'bg-light-secondary' }}">
                                {{ $productSku->is_active ? 'Active' : 'Inactive' }}
                            </span>
                            @if ($canManageProducts)
                                <a href="{{ route('product-skus.edit', $productSku) }}" class="btn btn-sm btn-primary">Edit SKU</a>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-lg-6">
                                <div class="mb-4">
                                    <small class="text-muted d-block mb-1">Parent Product</small>
                                    <div class="font-semibold">
                                        <a href="{{ route('products.show', $productSku->product) }}">{{ $productSku->product?->product_name ?: '-' }}</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-lg-6">
                                <div class="mb-4">
                                    <small class="text-muted d-block mb-1">Brand Name</small>
                                    <div class="font-semibold">{{ $productSku->brand_name ?: '-' }}</div>
                                </div>
                            </div>
                            <div class="col-12 col-lg-4">
                                <div class="mb-4">
                                    <small class="text-muted d-block mb-1">Net Weight</small>
                                    <div class="font-semibold">{{ $productSku->net_weight ? number_format((float) $productSku->net_weight, 2) : '-' }}</div>
                                </div>
                            </div>
                            <div class="col-12 col-lg-4">
                                <div class="mb-4">
                                    <small class="text-muted d-block mb-1">Weight Unit</small>
                                    <div class="font-semibold">{{ $productSku->weight_unit ?: '-' }}</div>
                                </div>
                            </div>
                            <div class="col-12 col-lg-4">
                                <div class="mb-4">
                                    <small class="text-muted d-block mb-1">Sellable Unit</small>
                                    <div class="font-semibold">{{ $productSku->sellable_unit ?: '-' }}</div>
                                </div>
                            </div>
                            <div class="col-12 col-lg-4">
                                <div class="mb-4">
                                    <small class="text-muted d-block mb-1">Barcode Type</small>
                                    <div class="font-semibold">{{ $productSku->barcode_type ?: '-' }}</div>
                                </div>
                            </div>
                            <div class="col-12 col-lg-4">
                                <div class="mb-4">
                                    <small class="text-muted d-block mb-1">GTIN</small>
                                    <div class="font-semibold">{{ $productSku->gtin ?: '-' }}</div>
                                </div>
                            </div>
                            <div class="col-12 col-lg-4">
                                <div class="mb-4">
                                    <small class="text-muted d-block mb-1">UPC / EAN</small>
                                    <div class="font-semibold">{{ $productSku->upc ?: ($productSku->ean ?: '-') }}</div>
                                </div>
                            </div>
                            <div class="col-12 col-lg-4">
                                <div class="mb-4">
                                    <small class="text-muted d-block mb-1">Barcode Number</small>
                                    <div class="font-semibold">{{ $productSku->barcode_number ?: '-' }}</div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-4">
                                    @include('products.partials.barcode-preview', [
                                        'barcodeImageUrl' => $productSku->barcodeImageUrl(),
                                        'barcodeType' => $productSku->barcode_type,
                                        'barcodeValue' => $productSku->barcode_number,
                                        'barcodeLabel' => 'Retail Barcode Preview',
                                        'barcodeDownloadPngUrl' => route('product-skus.barcode.download', [$productSku, 'png']),
                                        'barcodeDownloadJpegUrl' => route('product-skus.barcode.download', [$productSku, 'jpeg']),
                                    ])
                                </div>
                            </div>
                            <div class="col-12 col-lg-6">
                                <div class="mb-4">
                                    <small class="text-muted d-block mb-1">Created By</small>
                                    <div class="font-semibold">{{ $productSku->creator?->name ?: '-' }}</div>
                                </div>
                            </div>
                            <div class="col-12 col-lg-6">
                                <div class="mb-4">
                                    <small class="text-muted d-block mb-1">Created At</small>
                                    <div class="font-semibold">{{ $productSku->created_at?->format('d M Y H:i') ?: '-' }}</div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div>
                                    <small class="text-muted d-block mb-1">Notes</small>
                                    <div class="font-semibold">{{ $productSku->notes ?: '-' }}</div>
                                </div>
                            </div>
                            <div class="col-12 mt-2">
                                <div class="border rounded-3 p-3">
                                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                                        <div>
                                            <small class="text-muted d-block">Packaging Hierarchy</small>
                                            <div class="font-semibold">{{ $productSku->packagings->count() }} level linked</div>
                                        </div>
                                        <div class="d-flex gap-2 flex-wrap">
                                            <a href="{{ route('product-skus.packagings.index', $productSku) }}" class="btn btn-sm btn-light-primary">View Packaging</a>
                                            @if ($canManageProducts)
                                                <a href="{{ route('product-skus.packagings.create', $productSku) }}" class="btn btn-sm btn-primary">Add Packaging</a>
                                            @endif
                                        </div>
                                    </div>

                                    @if ($productSku->packagings->isNotEmpty())
                                        <div class="table-responsive">
                                            <table class="table table-sm align-middle mb-0">
                                                <thead>
                                                    <tr>
                                                        <th>Level</th>
                                                        <th>Barcode</th>
                                                        <th>Units</th>
                                                        <th>Default</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($productSku->packagings->take(5) as $packaging)
                                                        <tr>
                                                            <td>{{ strtoupper($packaging->level) }}</td>
                                                            <td>{{ $packaging->barcode_number ?: '-' }}</td>
                                                            <td>{{ $packaging->units_per_pack ?: '-' }}</td>
                                                            <td>{{ $packaging->is_default_for_level ? 'Yes' : 'No' }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="text-muted">Belum ada packaging level untuk SKU ini.</div>
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
                        <h4>SKU Snapshot</h4>
                    </div>
                    <div class="card-body">
                        <div class="list-group">
                            <div class="list-group-item">
                                <small class="text-muted d-block">Retail Positioning</small>
                                <span class="font-semibold">{{ $productSku->is_retail_sellable ? 'POS-ready candidate' : 'Bulk-only candidate' }}</span>
                            </div>
                            <div class="list-group-item">
                                <small class="text-muted d-block">Barcode Availability</small>
                                <span class="font-semibold">{{ $productSku->barcode_image_path ? 'Preview generated' : ($productSku->barcode_number ? 'Digits assigned' : 'No barcode yet') }}</span>
                            </div>
                            <div class="list-group-item">
                                <small class="text-muted d-block">Packaging Coverage</small>
                                <span class="font-semibold">{{ $productSku->packagings->isNotEmpty() ? $productSku->packagings->count() . ' level configured' : 'No packaging yet' }}</span>
                            </div>
                        </div>

                        <div class="d-grid gap-2 mt-4">
                            @if ($canManageProducts)
                                <a href="{{ route('product-skus.edit', $productSku) }}" class="btn btn-primary">Edit SKU</a>
                            @endif
                            <a href="{{ route('products.skus.index', $productSku->product) }}" class="btn btn-light">Back to SKU List</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
