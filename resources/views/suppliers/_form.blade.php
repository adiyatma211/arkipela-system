@csrf
@if ($formMethod !== 'POST')
    @method($formMethod)
@endif
@php
    $productRows = old('products', $productRows ?? [[
        'product_id' => null,
        'product_sku_id' => null,
        'monthly_capacity_kg' => null,
        'minimum_order_kg' => null,
        'lead_time_days' => null,
        'packaging_type' => '',
        'is_active' => true,
        'notes' => '',
    ]]);
    $photoRows = old('photos', []);
    $nextPhotoIndex = $photoRows !== []
        ? (collect(array_keys($photoRows))->max() + 1)
        : 0;
    $existingPhotos = $supplier->relationLoaded('photos')
        ? $supplier->photos
        : collect();
    $productOptions = collect($productOptions ?? []);
    $productSkuMap = $productSkuMap ?? [];
@endphp

<div class="row">
    <div class="col-12 col-lg-6">
        <div class="mb-3">
            <label for="supplier_name" class="form-label">Supplier Name</label>
            <input type="text" id="supplier_name" name="supplier_name" class="form-control @error('supplier_name') is-invalid @enderror" value="{{ old('supplier_name', $supplier->supplier_name) }}" required>
            @error('supplier_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="col-12 col-lg-6">
        <div class="mb-3">
            <label for="supplier_type" class="form-label">Supplier Type</label>
            <select id="supplier_type" name="supplier_type" class="form-select @error('supplier_type') is-invalid @enderror" required>
                <option value="">Select supplier type</option>
                @foreach ($typeOptions as $option)
                    <option value="{{ $option['value'] }}" @selected(old('supplier_type', $supplier->supplier_type) === $option['value'])>{{ $option['label'] }}</option>
                @endforeach
            </select>
            @error('supplier_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="col-12 col-lg-6">
        <div class="mb-3">
            <label for="pic_name" class="form-label">PIC Name</label>
            <input type="text" id="pic_name" name="pic_name" class="form-control @error('pic_name') is-invalid @enderror" value="{{ old('pic_name', $supplier->pic_name) }}">
            @error('pic_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="col-12 col-lg-6">
        <div class="mb-3">
            <label for="phone" class="form-label">Phone</label>
            <input type="text" id="phone" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $supplier->phone) }}">
            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="col-12 col-lg-6">
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $supplier->email) }}">
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="col-12 col-lg-6">
        <div class="mb-3">
            <label class="form-label">Owner Approval</label>
            <div class="border rounded-3 px-3 py-2">
                <span class="badge {{ $approvalBadgeMap[old('approval_status', $supplier->approval_status ?: 'pending')] ?? 'bg-secondary' }}">
                    {{ $approvalLabelMap[old('approval_status', $supplier->approval_status ?: 'pending')] ?? 'Pending Approval' }}
                </span>
                <div class="form-text mt-2 mb-0">
                    Supplier baru atau perubahan data penting akan menunggu approval owner sebelum dipakai penuh di sistem.
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-6">
        <div class="mb-3">
            <label for="status" class="form-label">Operational Status</label>
            @if ($canManageOperationalStatus)
                <select id="status" name="status" class="form-select @error('status') is-invalid @enderror" required>
                    @foreach ($statusOptions as $option)
                        <option value="{{ $option['value'] }}" @selected(old('status', $supplier->status) === $option['value'])>{{ $option['label'] }}</option>
                    @endforeach
                </select>
                @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
            @else
                <input type="hidden" name="status" value="{{ old('status', $supplier->status ?: 'prospect') }}">
                <div class="border rounded-3 px-3 py-2">
                    <div class="fw-semibold">{{ $statusLabelMap[old('status', $supplier->status ?: 'prospect')] ?? 'Prospect' }}</div>
                    <div class="form-text mt-2 mb-0">
                        Hanya owner yang bisa mengubah status operasional supplier.
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="col-12">
        <div class="mb-3">
            <label for="address" class="form-label">Address</label>
            <textarea id="address" name="address" rows="3" class="form-control @error('address') is-invalid @enderror">{{ old('address', $supplier->address) }}</textarea>
            @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="col-12 col-lg-4">
        <div class="mb-3">
            <label for="city" class="form-label">City</label>
            <input type="text" id="city" name="city" class="form-control @error('city') is-invalid @enderror" value="{{ old('city', $supplier->city) }}">
            @error('city')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="col-12 col-lg-4">
        <div class="mb-3">
            <label for="province" class="form-label">Province</label>
            <input type="text" id="province" name="province" class="form-control @error('province') is-invalid @enderror" value="{{ old('province', $supplier->province) }}">
            @error('province')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="col-12 col-lg-4">
        <div class="mb-3">
            <label for="country" class="form-label">Country</label>
            <input type="text" id="country" name="country" class="form-control @error('country') is-invalid @enderror" value="{{ old('country', $supplier->country ?: 'Indonesia') }}">
            @error('country')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="col-12">
        <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
                <label class="form-label mb-0">Supplier Catalog</label>
                <button type="button" class="btn btn-sm btn-light-primary" data-product-add>Add Product</button>
            </div>
            <div class="border rounded-3 p-3">
                <div id="supplier-products-wrapper" data-next-index="{{ count($productRows) }}">
                    @foreach ($productRows as $index => $productRow)
                        @php
                            $selectedProductId = (string) data_get($productRow, 'product_id');
                            $selectedSkuId = (string) data_get($productRow, 'product_sku_id');
                            $skuOptions = collect($productSkuMap[$selectedProductId] ?? []);
                        @endphp
                        <div class="border rounded-3 p-3 mb-3 supplier-product-row" data-product-row>
                            <div class="row g-3">
                                <div class="col-12 col-xl-4">
                                    <label class="form-label">Product Master</label>
                                    <select
                                        name="products[{{ $index }}][product_id]"
                                        class="form-select js-product-master-select @error("products.$index.product_id") is-invalid @enderror"
                                        required
                                    >
                                        <option value="">Select product</option>
                                        @foreach ($productOptions as $productOption)
                                            <option value="{{ $productOption->id }}" @selected($selectedProductId === (string) $productOption->id)>
                                                {{ $productOption->product_name }} ({{ $productOption->product_code }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error("products.$index.product_id")<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-12 col-xl-4">
                                    <label class="form-label">SKU / Variant</label>
                                    <select
                                        name="products[{{ $index }}][product_sku_id]"
                                        class="form-select js-product-sku-select @error("products.$index.product_sku_id") is-invalid @enderror"
                                        data-selected-sku="{{ $selectedSkuId }}"
                                        @disabled($selectedProductId === '')
                                    >
                                        <option value="">{{ $selectedProductId !== '' ? 'Optional SKU linkage' : 'Select product first' }}</option>
                                        @foreach ($skuOptions as $skuOption)
                                            <option value="{{ $skuOption['id'] }}" @selected($selectedSkuId === (string) $skuOption['id'])>
                                                {{ $skuOption['label'] }}
                                                @if (! empty($skuOption['barcode_number']))
                                                    | {{ $skuOption['barcode_number'] }}
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    @error("products.$index.product_sku_id")<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-12 col-xl-2">
                                    <label class="form-label">Lead Time (days)</label>
                                    <input
                                        type="number"
                                        step="1"
                                        min="0"
                                        name="products[{{ $index }}][lead_time_days]"
                                        class="form-control @error("products.$index.lead_time_days") is-invalid @enderror"
                                        value="{{ data_get($productRow, 'lead_time_days') }}"
                                    >
                                    @error("products.$index.lead_time_days")<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-12 col-xl-2">
                                    <label class="form-label">Status</label>
                                    <div class="form-check form-switch border rounded-3 px-3 py-2">
                                        <input
                                            type="hidden"
                                            name="products[{{ $index }}][is_active]"
                                            value="0"
                                        >
                                        <input
                                            type="checkbox"
                                            name="products[{{ $index }}][is_active]"
                                            value="1"
                                            class="form-check-input"
                                            @checked(filter_var(data_get($productRow, 'is_active', true), FILTER_VALIDATE_BOOL))
                                        >
                                        <label class="form-check-label ms-2">Active</label>
                                    </div>
                                </div>
                                <div class="col-12 col-xl-3">
                                    <label class="form-label">Monthly Capacity (kg)</label>
                                    <input
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        name="products[{{ $index }}][monthly_capacity_kg]"
                                        class="form-control @error("products.$index.monthly_capacity_kg") is-invalid @enderror"
                                        value="{{ data_get($productRow, 'monthly_capacity_kg') }}"
                                    >
                                    @error("products.$index.monthly_capacity_kg")<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-12 col-xl-3">
                                    <label class="form-label">Minimum Order (kg)</label>
                                    <input
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        name="products[{{ $index }}][minimum_order_kg]"
                                        class="form-control @error("products.$index.minimum_order_kg") is-invalid @enderror"
                                        value="{{ data_get($productRow, 'minimum_order_kg') }}"
                                    >
                                    @error("products.$index.minimum_order_kg")<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-12 col-xl-3">
                                    <label class="form-label">Packaging Type</label>
                                    <input
                                        type="text"
                                        name="products[{{ $index }}][packaging_type]"
                                        class="form-control @error("products.$index.packaging_type") is-invalid @enderror"
                                        value="{{ data_get($productRow, 'packaging_type') }}"
                                        placeholder="Box, Jar, Bulk Bag"
                                    >
                                    @error("products.$index.packaging_type")<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-12 col-xl-2">
                                    <label class="form-label">Action</label>
                                    <button type="button" class="btn btn-light-danger w-100" data-product-remove>Remove</button>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Notes</label>
                                    <textarea
                                        name="products[{{ $index }}][notes]"
                                        rows="2"
                                        class="form-control @error("products.$index.notes") is-invalid @enderror"
                                        placeholder="Catalog remark, MOQ note, preferred packaging, or retail readiness note"
                                    >{{ data_get($productRow, 'notes') }}</textarea>
                                    @error("products.$index.notes")<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                @error('products')<div class="text-danger small">{{ $message }}</div>@enderror
                <div class="form-text mt-2">
                    Supplier sekarang link ke product master. SKU bersifat optional jika supplier hanya support product level, belum sampai varian retail tertentu.
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-6">
        <div class="mb-3">
            <label for="payment_term" class="form-label">Payment Term</label>
            <input type="text" id="payment_term" name="payment_term" class="form-control @error('payment_term') is-invalid @enderror" value="{{ old('payment_term', $supplier->payment_term) }}" placeholder="Example: Cash, T/T 30% DP 70%">
            @error('payment_term')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="col-12 col-lg-6">
        <div class="mb-3">
            <label for="legal_status" class="form-label">Legal Status</label>
            <input type="text" id="legal_status" name="legal_status" class="form-control @error('legal_status') is-invalid @enderror" value="{{ old('legal_status', $supplier->legal_status) }}" placeholder="Example: NIB complete, informal supplier">
            @error('legal_status')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="col-12">
        <div class="mb-3">
            <label for="notes" class="form-label">Notes</label>
            <textarea id="notes" name="notes" rows="4" class="form-control @error('notes') is-invalid @enderror">{{ old('notes', $supplier->notes) }}</textarea>
            @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="col-12">
        <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
                <label class="form-label mb-0">Supplier Photo Evidence</label>
                <button type="button" class="btn btn-sm btn-light-primary" data-photo-add>Add Photo</button>
            </div>

            @if ($existingPhotos->isNotEmpty())
                <div class="border rounded-3 p-3 mb-3">
                    <div class="fw-semibold mb-3">Existing Photos</div>
                    <div class="row g-3">
                        @foreach ($existingPhotos as $photo)
                            <div class="col-12 col-md-6 col-xl-3">
                                <div class="border rounded-3 h-100 overflow-hidden">
                                    <img
                                        src="{{ $photo->photoUrl() }}"
                                        alt="{{ $photoTypeLabelMap[$photo->photo_type] ?? $photo->photo_type }}"
                                        class="w-100 image-preview-trigger"
                                        style="height: 180px; object-fit: cover;"
                                        data-image-preview-trigger
                                        data-preview-src="{{ $photo->photoUrl() }}"
                                        data-preview-title="{{ $photoTypeLabelMap[$photo->photo_type] ?? $photo->photo_type }}"
                                        data-preview-caption="{{ $photo->caption ?: 'Tanpa caption' }}"
                                    >
                                    <div class="p-3">
                                        <div class="fw-semibold mb-1">{{ $photoTypeLabelMap[$photo->photo_type] ?? $photo->photo_type }}</div>
                                        <div class="text-muted small mb-2">{{ $photo->caption ?: 'Tanpa caption' }}</div>
                                        <div class="form-check">
                                            <input
                                                type="checkbox"
                                                id="delete_photo_{{ $photo->id }}"
                                                name="existing_photos_to_delete[]"
                                                value="{{ $photo->id }}"
                                                class="form-check-input"
                                                @checked(in_array((string) $photo->id, collect(old('existing_photos_to_delete', []))->map(fn ($value) => (string) $value)->all(), true))
                                            >
                                            <label for="delete_photo_{{ $photo->id }}" class="form-check-label">Hapus foto ini</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @error('existing_photos_to_delete')<div class="text-danger small mt-2">{{ $message }}</div>@enderror
                    @error('existing_photos_to_delete.*')<div class="text-danger small mt-2">{{ $message }}</div>@enderror
                </div>
            @endif

            <div class="border rounded-3 p-3">
                <div id="supplier-photos-wrapper" data-next-index="{{ $nextPhotoIndex }}">
                    @foreach ($photoRows as $index => $photoRow)
                        <div class="border rounded-3 p-3 mb-3 supplier-photo-row" data-photo-row>
                            <div class="row g-3 align-items-end">
                                <div class="col-12 col-lg-4">
                                    <label class="form-label">Photo File</label>
                                    <input type="file" name="photos[{{ $index }}][file]" class="form-control @error("photos.$index.file") is-invalid @enderror" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
                                    @error("photos.$index.file")<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-12 col-lg-3">
                                    <label class="form-label">Photo Type</label>
                                    <select name="photos[{{ $index }}][photo_type]" class="form-select @error("photos.$index.photo_type") is-invalid @enderror">
                                        <option value="">Select photo type</option>
                                        @foreach ($photoOptions as $option)
                                            <option value="{{ $option['value'] }}" @selected(data_get($photoRow, 'photo_type') === $option['value'])>{{ $option['label'] }}</option>
                                        @endforeach
                                    </select>
                                    @error("photos.$index.photo_type")<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-12 col-lg-4">
                                    <label class="form-label">Caption</label>
                                    <input type="text" name="photos[{{ $index }}][caption]" class="form-control @error("photos.$index.caption") is-invalid @enderror" value="{{ data_get($photoRow, 'caption') }}" placeholder="Example: Front warehouse view">
                                    @error("photos.$index.caption")<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-12 col-lg-1">
                                    <button type="button" class="btn btn-light-danger w-100" data-photo-remove>Remove</button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                @error('photos')<div class="text-danger small">{{ $message }}</div>@enderror
                <div class="form-text mt-2">
                    Format: JPG, JPEG, PNG, WEBP. Maksimal 5 MB per file. Jika validasi gagal, file perlu dipilih ulang.
                </div>
            </div>
        </div>
    </div>
</div>

<div class="d-flex justify-content-end gap-2">
    <a href="{{ route('suppliers.index') }}" class="btn btn-light">Cancel</a>
    <button type="submit" class="btn btn-primary">{{ $submitLabel }}</button>
</div>

<template id="supplier-product-template">
    <div class="border rounded-3 p-3 mb-3 supplier-product-row" data-product-row>
        <div class="row g-3">
            <div class="col-12 col-xl-4">
                <label class="form-label">Product Master</label>
                <select name="products[__INDEX__][product_id]" class="form-select js-product-master-select" required>
                    <option value="">Select product</option>
                    @foreach ($productOptions as $productOption)
                        <option value="{{ $productOption->id }}">{{ $productOption->product_name }} ({{ $productOption->product_code }})</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-xl-4">
                <label class="form-label">SKU / Variant</label>
                <select name="products[__INDEX__][product_sku_id]" class="form-select js-product-sku-select" disabled>
                    <option value="">Select product first</option>
                </select>
            </div>
            <div class="col-12 col-xl-2">
                <label class="form-label">Lead Time (days)</label>
                <input type="number" step="1" min="0" name="products[__INDEX__][lead_time_days]" class="form-control">
            </div>
            <div class="col-12 col-xl-2">
                <label class="form-label">Status</label>
                <div class="form-check form-switch border rounded-3 px-3 py-2">
                    <input type="hidden" name="products[__INDEX__][is_active]" value="0">
                    <input type="checkbox" name="products[__INDEX__][is_active]" value="1" class="form-check-input" checked>
                    <label class="form-check-label ms-2">Active</label>
                </div>
            </div>
            <div class="col-12 col-xl-3">
                <label class="form-label">Monthly Capacity (kg)</label>
                <input type="number" step="0.01" min="0" name="products[__INDEX__][monthly_capacity_kg]" class="form-control">
            </div>
            <div class="col-12 col-xl-3">
                <label class="form-label">Minimum Order (kg)</label>
                <input type="number" step="0.01" min="0" name="products[__INDEX__][minimum_order_kg]" class="form-control">
            </div>
            <div class="col-12 col-xl-3">
                <label class="form-label">Packaging Type</label>
                <input type="text" name="products[__INDEX__][packaging_type]" class="form-control" placeholder="Box, Jar, Bulk Bag">
            </div>
            <div class="col-12 col-xl-2">
                <label class="form-label">Action</label>
                <button type="button" class="btn btn-light-danger w-100" data-product-remove>Remove</button>
            </div>
            <div class="col-12">
                <label class="form-label">Notes</label>
                <textarea name="products[__INDEX__][notes]" rows="2" class="form-control" placeholder="Catalog remark, MOQ note, preferred packaging, or retail readiness note"></textarea>
            </div>
        </div>
    </div>
</template>

<template id="supplier-photo-template">
    <div class="border rounded-3 p-3 mb-3 supplier-photo-row" data-photo-row>
        <div class="row g-3 align-items-end">
            <div class="col-12 col-lg-4">
                <label class="form-label">Photo File</label>
                <input type="file" name="photos[__INDEX__][file]" class="form-control" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
            </div>
            <div class="col-12 col-lg-3">
                <label class="form-label">Photo Type</label>
                <select name="photos[__INDEX__][photo_type]" class="form-select">
                    <option value="">Select photo type</option>
                    @foreach ($photoOptions as $option)
                        <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-lg-4">
                <label class="form-label">Caption</label>
                <input type="text" name="photos[__INDEX__][caption]" class="form-control" placeholder="Example: Front warehouse view">
            </div>
            <div class="col-12 col-lg-1">
                <button type="button" class="btn btn-light-danger w-100" data-photo-remove>Remove</button>
            </div>
        </div>
    </div>
</template>

@include('partials.image-preview-modal')

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const productSkuMap = @json($productSkuMap);

            const buildSkuOptions = function (productId, selectedSkuId = '') {
                const skuOptions = productSkuMap[String(productId || '')] || [];

                if (!skuOptions.length) {
                    return `<option value="">${productId ? 'No SKU linked' : 'Select product first'}</option>`;
                }

                const selectedValue = String(selectedSkuId || '');

                return '<option value="">Optional SKU linkage</option>' + skuOptions.map(function (skuOption) {
                    const selected = String(skuOption.id) === selectedValue ? ' selected' : '';
                    const barcode = skuOption.barcode_number ? ` | ${skuOption.barcode_number}` : '';
                    return `<option value="${skuOption.id}"${selected}>${skuOption.label}${barcode}</option>`;
                }).join('');
            };

            const syncSkuSelect = function (row, selectedSkuId = '') {
                const productSelect = row.querySelector('.js-product-master-select');
                const skuSelect = row.querySelector('.js-product-sku-select');

                if (!productSelect || !skuSelect) {
                    return;
                }

                const productId = productSelect.value;
                skuSelect.innerHTML = buildSkuOptions(productId, selectedSkuId);
                skuSelect.disabled = !productId;

                if (selectedSkuId) {
                    skuSelect.value = String(selectedSkuId);
                }
            };

            const setupDynamicRows = function (options) {
                const wrapper = document.getElementById(options.wrapperId);
                const template = document.getElementById(options.templateId);
                const addButton = document.querySelector(options.addButtonSelector);

                if (!wrapper || !template || !addButton) {
                    return;
                }

                const createRow = function (index) {
                    return template.innerHTML.replaceAll('__INDEX__', String(index));
                };

                const ensureMinimumRows = function () {
                    if (!options.ensureOneRow) {
                        return;
                    }

                    if (wrapper.querySelectorAll(options.rowSelector).length === 0) {
                        const index = Number(wrapper.dataset.nextIndex || 0);
                        wrapper.insertAdjacentHTML('beforeend', createRow(index));
                        wrapper.dataset.nextIndex = String(index + 1);
                    }
                };

                addButton.addEventListener('click', function () {
                    const index = Number(wrapper.dataset.nextIndex || 0);
                    wrapper.insertAdjacentHTML('beforeend', createRow(index));
                    wrapper.dataset.nextIndex = String(index + 1);

                    if (options.wrapperId === 'supplier-products-wrapper') {
                        const rows = wrapper.querySelectorAll('[data-product-row]');
                        const latestRow = rows[rows.length - 1];
                        syncSkuSelect(latestRow);
                    }
                });

                wrapper.addEventListener('click', function (event) {
                    const removeButton = event.target.closest(options.removeButtonSelector);

                    if (!removeButton) {
                        return;
                    }

                    removeButton.closest(options.rowSelector)?.remove();
                    ensureMinimumRows();
                });
            };

            document.querySelectorAll('[data-product-row]').forEach(function (row) {
                const skuSelect = row.querySelector('.js-product-sku-select');
                syncSkuSelect(row, skuSelect?.dataset.selectedSku || skuSelect?.value || '');
            });

            document.addEventListener('change', function (event) {
                if (event.target.matches('.js-product-master-select')) {
                    syncSkuSelect(event.target.closest('[data-product-row]'));
                }
            });

            setupDynamicRows({
                wrapperId: 'supplier-products-wrapper',
                templateId: 'supplier-product-template',
                addButtonSelector: '[data-product-add]',
                removeButtonSelector: '[data-product-remove]',
                rowSelector: '[data-product-row]',
                ensureOneRow: true,
            });

            setupDynamicRows({
                wrapperId: 'supplier-photos-wrapper',
                templateId: 'supplier-photo-template',
                addButtonSelector: '[data-photo-add]',
                removeButtonSelector: '[data-photo-remove]',
                rowSelector: '[data-photo-row]',
                ensureOneRow: false,
            });
        });
    </script>
@endpush
