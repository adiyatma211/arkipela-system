@extends('layouts.app')

@section('content')
    <div class="page-content">
        <section class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                            <div>
                                <h4 class="mb-1">Packaging for {{ $productSku->variant_name }}</h4>
                                <p class="text-muted mb-0">Pisahkan retail unit, inner pack, case, dan pallet agar barcode POS dan distribution tidak tercampur.</p>
                            </div>
                            <div class="d-flex gap-2 flex-wrap">
                                <a href="{{ route('product-skus.show', $productSku) }}" class="btn btn-light">Back to SKU</a>
                                @if ($canManageProducts)
                                    <a href="{{ route('product-skus.packagings.create', $productSku) }}" class="btn btn-primary">Add Packaging</a>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-lg">
                                <thead>
                                    <tr>
                                        <th>Level</th>
                                        <th>Units / Pack</th>
                                        <th>Barcode</th>
                                        <th>Preview</th>
                                        <th>Dimensions</th>
                                        <th>Weight</th>
                                        <th>Default</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($productSku->packagings as $packaging)
                                        <tr>
                                            <td class="font-semibold">{{ strtoupper($packaging->level) }}</td>
                                            <td>{{ $packaging->units_per_pack ?: '-' }}</td>
                                            <td>
                                                <div>{{ $packaging->barcode_number ?: '-' }}</div>
                                                <small class="text-muted">{{ $packaging->barcode_type ?: 'No barcode type' }}</small>
                                            </td>
                                            <td>
                                                @if ($packaging->barcodeImageUrl())
                                                    <img src="{{ $packaging->barcodeImageUrl() }}" alt="{{ $packaging->barcode_number }}" class="img-fluid mb-2" style="max-height: 70px;">
                                                    <div class="d-flex gap-1 flex-wrap">
                                                        <a href="{{ route('product-packagings.barcode.download', [$packaging, 'png']) }}" class="btn btn-sm btn-light-primary">PNG</a>
                                                        <a href="{{ route('product-packagings.barcode.download', [$packaging, 'jpeg']) }}" class="btn btn-sm btn-light-secondary">JPEG</a>
                                                    </div>
                                                @else
                                                    <span class="text-muted small">No preview</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($packaging->length || $packaging->width || $packaging->height)
                                                    {{ number_format((float) $packaging->length, 2) }} x {{ number_format((float) $packaging->width, 2) }} x {{ number_format((float) $packaging->height, 2) }} {{ $packaging->dimension_unit }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                <div>Net: {{ $packaging->net_weight ? number_format((float) $packaging->net_weight, 2) : '-' }}</div>
                                                <small class="text-muted">Gross: {{ $packaging->gross_weight ? number_format((float) $packaging->gross_weight, 2) : '-' }}</small>
                                            </td>
                                            <td>
                                                <span class="badge {{ $packaging->is_default_for_level ? 'bg-success' : 'bg-light-secondary' }}">
                                                    {{ $packaging->is_default_for_level ? 'Default' : 'Optional' }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                @if ($canManageProducts)
                                                    <div class="d-flex justify-content-center gap-2 flex-wrap">
                                                        <a href="{{ route('product-packagings.edit', $packaging) }}" class="btn btn-sm btn-light-warning">Edit</a>
                                                        <form action="{{ route('product-packagings.destroy', $packaging) }}" method="POST" onsubmit="return confirm('Delete this packaging level?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-light-danger">Delete</button>
                                                        </form>
                                                    </div>
                                                @else
                                                    <span class="text-muted small">View only</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center text-muted py-5">
                                                Belum ada packaging hierarchy untuk SKU ini.
                                            </td>
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
