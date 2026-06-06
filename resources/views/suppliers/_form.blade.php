@csrf
@if ($formMethod !== 'POST')
    @method($formMethod)
@endif
@php
    $productRows = old('products', $productRows ?? [[
        'product_name' => '',
        'monthly_capacity_kg' => null,
        'minimum_order_kg' => null,
    ]]);
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
            <label for="status" class="form-label">Status</label>
            <select id="status" name="status" class="form-select @error('status') is-invalid @enderror" required>
                @foreach ($statusOptions as $option)
                    <option value="{{ $option['value'] }}" @selected(old('status', $supplier->status) === $option['value'])>{{ $option['label'] }}</option>
                @endforeach
            </select>
            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
                <label class="form-label mb-0">Supplier Products</label>
                <button type="button" class="btn btn-sm btn-light-primary" data-product-add>Add Product</button>
            </div>
            <div class="border rounded-3 p-3">
                <div id="supplier-products-wrapper" data-next-index="{{ count($productRows) }}">
                    @foreach ($productRows as $index => $productRow)
                        <div class="border rounded-3 p-3 mb-3 supplier-product-row" data-product-row>
                            <div class="row g-3 align-items-end">
                                <div class="col-12 col-lg-5">
                                    <label class="form-label">Product Name</label>
                                    <input
                                        type="text"
                                        name="products[{{ $index }}][product_name]"
                                        class="form-control @error("products.$index.product_name") is-invalid @enderror"
                                        value="{{ data_get($productRow, 'product_name') }}"
                                        placeholder="Example: Clove"
                                        required
                                    >
                                    @error("products.$index.product_name")<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-12 col-lg-3">
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
                                <div class="col-12 col-lg-3">
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
                                <div class="col-12 col-lg-1">
                                    <button type="button" class="btn btn-light-danger w-100" data-product-remove>Remove</button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                @error('products')<div class="text-danger small">{{ $message }}</div>@enderror
                <div class="form-text mt-2">
                    Satu supplier bisa punya beberapa product, dan setiap product punya capacity serta minimum order sendiri.
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
</div>

<div class="d-flex justify-content-end gap-2">
    <a href="{{ route('suppliers.index') }}" class="btn btn-light">Cancel</a>
    <button type="submit" class="btn btn-primary">{{ $submitLabel }}</button>
</div>

<template id="supplier-product-template">
    <div class="border rounded-3 p-3 mb-3 supplier-product-row" data-product-row>
        <div class="row g-3 align-items-end">
            <div class="col-12 col-lg-5">
                <label class="form-label">Product Name</label>
                <input type="text" name="products[__INDEX__][product_name]" class="form-control" placeholder="Example: Clove" required>
            </div>
            <div class="col-12 col-lg-3">
                <label class="form-label">Monthly Capacity (kg)</label>
                <input type="number" step="0.01" min="0" name="products[__INDEX__][monthly_capacity_kg]" class="form-control">
            </div>
            <div class="col-12 col-lg-3">
                <label class="form-label">Minimum Order (kg)</label>
                <input type="number" step="0.01" min="0" name="products[__INDEX__][minimum_order_kg]" class="form-control">
            </div>
            <div class="col-12 col-lg-1">
                <button type="button" class="btn btn-light-danger w-100" data-product-remove>Remove</button>
            </div>
        </div>
    </div>
</template>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const wrapper = document.getElementById('supplier-products-wrapper');
            const template = document.getElementById('supplier-product-template');
            const addButton = document.querySelector('[data-product-add]');

            if (!wrapper || !template || !addButton) {
                return;
            }

            const createRow = function (index) {
                return template.innerHTML.replaceAll('__INDEX__', String(index));
            };

            const ensureOneRow = function () {
                if (wrapper.querySelectorAll('[data-product-row]').length === 0) {
                    const index = Number(wrapper.dataset.nextIndex || 0);
                    wrapper.insertAdjacentHTML('beforeend', createRow(index));
                    wrapper.dataset.nextIndex = String(index + 1);
                }
            };

            addButton.addEventListener('click', function () {
                const index = Number(wrapper.dataset.nextIndex || 0);
                wrapper.insertAdjacentHTML('beforeend', createRow(index));
                wrapper.dataset.nextIndex = String(index + 1);
            });

            wrapper.addEventListener('click', function (event) {
                const removeButton = event.target.closest('[data-product-remove]');

                if (!removeButton) {
                    return;
                }

                removeButton.closest('[data-product-row]')?.remove();
                ensureOneRow();
            });
        });
    </script>
@endpush
