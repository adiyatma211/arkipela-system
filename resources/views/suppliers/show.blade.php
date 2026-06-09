@extends('layouts.app')

@section('content')
    @php
        $products = $supplier->resolvedProducts();
        $productsSummary = $supplier->resolvedProductsSummary();
        $totalCapacity = $supplier->resolvedMonthlyCapacityKg();
        $minimumOrder = $supplier->resolvedMinimumOrderKg();
        $photos = $supplier->photos;
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
                        <div class="d-flex gap-2 flex-wrap justify-content-end">
                            <span class="badge {{ $approvalBadgeMap[$supplier->approval_status] ?? 'bg-secondary' }}">
                                {{ $approvalLabelMap[$supplier->approval_status] ?? ucfirst(str_replace('_', ' ', $supplier->approval_status)) }}
                            </span>
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
                                    <small class="text-muted d-block mb-2">Photo Evidence</small>
                                    @if ($photos->isNotEmpty())
                                        <div class="row g-3">
                                            @foreach ($photos as $photo)
                                                <div class="col-12 col-md-6 col-xl-4">
                                                    <div class="border rounded-3 h-100 overflow-hidden">
                                                        <img
                                                            src="{{ $photo->photoUrl() }}"
                                                            alt="{{ $photoTypeLabelMap[$photo->photo_type] ?? $photo->photo_type }}"
                                                            class="w-100 image-preview-trigger"
                                                            style="height: 220px; object-fit: cover;"
                                                            data-image-preview-trigger
                                                            data-preview-src="{{ $photo->photoUrl() }}"
                                                            data-preview-title="{{ $photoTypeLabelMap[$photo->photo_type] ?? $photo->photo_type }}"
                                                            data-preview-caption="{{ $photo->caption ?: 'Tanpa caption' }}"
                                                        >
                                                        <div class="p-3">
                                                            <div class="fw-semibold mb-1">{{ $photoTypeLabelMap[$photo->photo_type] ?? $photo->photo_type }}</div>
                                                            <div class="text-muted small">{{ $photo->caption ?: 'Tanpa caption' }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="font-semibold">-</div>
                                    @endif
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
                                                        <th>SKU / Variant</th>
                                                        <th>Monthly Capacity</th>
                                                        <th>Minimum Order</th>
                                                        <th>Lead Time</th>
                                                        <th>Packaging</th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($products as $product)
                                                        <tr>
                                                            <td>{{ $product->product_name }}</td>
                                                            <td>{{ $product->skuLabel() ?: '-' }}</td>
                                                            <td>{{ $product->monthly_capacity_kg ? number_format((float) $product->monthly_capacity_kg, 0) . ' kg' : '-' }}</td>
                                                            <td>{{ $product->minimum_order_kg ? number_format((float) $product->minimum_order_kg, 0) . ' kg' : '-' }}</td>
                                                            <td>{{ $product->lead_time_days ? $product->lead_time_days . ' days' : '-' }}</td>
                                                            <td>{{ $product->packaging_type ?: '-' }}</td>
                                                            <td>
                                                                <span class="badge {{ ($product->is_active ?? true) ? 'bg-light-success' : 'bg-light-secondary' }}">
                                                                    {{ ($product->is_active ?? true) ? 'Active' : 'Inactive' }}
                                                                </span>
                                                            </td>
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
                                <span class="font-semibold">Supplier must be owner-approved and have operational status Approved or Active before it can be used in orders.</span>
                            </div>
                            <div class="list-group-item">
                                <small class="text-muted d-block">Data Completeness</small>
                                <span class="font-semibold">{{ $supplier->email && $supplier->phone && $products->isNotEmpty() && $photos->isNotEmpty() ? 'Good' : 'Needs enrichment' }}</span>
                            </div>
                            <div class="list-group-item">
                                <small class="text-muted d-block">Approval Status</small>
                                <span class="font-semibold">{{ $approvalLabelMap[$supplier->approval_status] ?? '-' }}</span>
                                <div class="text-muted small mt-1">
                                    @if ($supplier->approval_status === 'approved')
                                        Approved by {{ $supplier->approver?->name ?: '-' }}{{ $supplier->approved_at ? ' on ' . $supplier->approved_at->format('d M Y H:i') : '' }}
                                    @elseif ($supplier->approval_status === 'rejected')
                                        Rejected by {{ $supplier->rejector?->name ?: '-' }}{{ $supplier->rejected_at ? ' on ' . $supplier->rejected_at->format('d M Y H:i') : '' }}
                                    @else
                                        Submitted by {{ $supplier->submitter?->name ?: $supplier->creator?->name ?: '-' }}{{ $supplier->submitted_at ? ' on ' . $supplier->submitted_at->format('d M Y H:i') : '' }}
                                    @endif
                                </div>
                            </div>
                            <div class="list-group-item">
                                <small class="text-muted d-block">Status</small>
                                <span class="font-semibold">{{ $statusLabelMap[$supplier->status] ?? '-' }}</span>
                            </div>
                            @if ($supplier->rejection_reason)
                                <div class="list-group-item">
                                    <small class="text-muted d-block">Rejection Reason</small>
                                    <span class="font-semibold">{{ $supplier->rejection_reason }}</span>
                                </div>
                            @endif
                        </div>

                        <div class="d-grid gap-2 mt-4">
                            @if ($canApproveSupplier && $supplier->approval_status === 'pending')
                                <form action="{{ route('suppliers.approve', $supplier) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-success w-100">Approve Supplier</button>
                                </form>
                                <form action="{{ route('suppliers.reject', $supplier) }}" method="POST" class="d-grid gap-2">
                                    @csrf
                                    @method('PATCH')
                                    <textarea name="rejection_reason" rows="3" class="form-control @error('rejection_reason') is-invalid @enderror" placeholder="Tuliskan alasan reject agar procurement bisa revisi.">{{ old('rejection_reason') }}</textarea>
                                    @error('rejection_reason')<div class="text-danger small">{{ $message }}</div>@enderror
                                    <button type="submit" class="btn btn-danger w-100">Reject Supplier</button>
                                </form>
                            @endif
                            @if ($canSubmitSupplier && in_array($supplier->approval_status, ['rejected', 'pending'], true))
                                <form action="{{ route('suppliers.submit', $supplier) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-warning w-100">
                                        {{ $supplier->approval_status === 'rejected' ? 'Resubmit for Approval' : 'Refresh Submission Timestamp' }}
                                    </button>
                                </form>
                            @endif
                            <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-primary">Edit Supplier</a>
                            <a href="{{ route('suppliers.index') }}" class="btn btn-light">Back to List</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    @include('partials.image-preview-modal')
@endsection
