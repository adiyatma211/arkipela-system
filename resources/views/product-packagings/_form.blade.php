@csrf
@if ($formMethod !== 'POST')
    @method($formMethod)
@endif

<div class="row">
    <div class="col-12 col-lg-6">
        <div class="mb-3">
            <label class="form-label">Parent SKU</label>
            <div class="border rounded-3 px-3 py-2 bg-light-subtle">
                <div class="fw-semibold">{{ $productSku->variant_name }}</div>
                <div class="text-muted small">{{ $productSku->sku_code }} | {{ $productSku->product?->product_name }}</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-6">
        <div class="mb-3">
            <label for="level" class="form-label">Packaging Level</label>
            <select id="level" name="level" class="form-select @error('level') is-invalid @enderror" required>
                @foreach ($levelOptions as $option)
                    <option value="{{ $option['value'] }}" @selected(old('level', $productPackaging->level) === $option['value'])>{{ $option['label'] }}</option>
                @endforeach
            </select>
            @error('level')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="col-12 col-lg-4">
        <div class="mb-3">
            <label for="units_per_pack" class="form-label">Units per Pack</label>
            <input type="number" min="1" id="units_per_pack" name="units_per_pack" class="form-control @error('units_per_pack') is-invalid @enderror" value="{{ old('units_per_pack', $productPackaging->units_per_pack) }}">
            @error('units_per_pack')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="col-12 col-lg-4">
        <div class="mb-3">
            <label for="barcode_type" class="form-label">Barcode Type</label>
            <select id="barcode_type" name="barcode_type" class="form-select @error('barcode_type') is-invalid @enderror">
                <option value="">Select barcode type</option>
                @foreach (($barcodeTypeOptions ?? []) as $option)
                    <option value="{{ $option['value'] }}" @selected(old('barcode_type', $productPackaging->barcode_type) === $option['value'])>{{ $option['label'] }}</option>
                @endforeach
            </select>
            @error('barcode_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="col-12 col-lg-4">
        <div class="mb-3">
            <label for="barcode_number" class="form-label">Barcode Number</label>
            <input type="text" id="barcode_number" name="barcode_number" class="form-control @error('barcode_number') is-invalid @enderror" value="{{ old('barcode_number', $productPackaging->barcode_number) }}">
            @error('barcode_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="col-12 col-lg-4">
        <div class="mb-3">
            <label for="gtin" class="form-label">GTIN</label>
            <input type="text" id="gtin" name="gtin" class="form-control @error('gtin') is-invalid @enderror" value="{{ old('gtin', $productPackaging->gtin) }}">
            @error('gtin')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="col-12 col-lg-4">
        <div class="mb-3">
            <label for="upc" class="form-label">UPC</label>
            <input type="text" id="upc" name="upc" class="form-control @error('upc') is-invalid @enderror" value="{{ old('upc', $productPackaging->upc) }}">
            @error('upc')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="col-12 col-lg-4">
        <div class="mb-3">
            <label for="ean" class="form-label">EAN</label>
            <input type="text" id="ean" name="ean" class="form-control @error('ean') is-invalid @enderror" value="{{ old('ean', $productPackaging->ean) }}">
            @error('ean')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="col-12 col-lg-6">
        <div class="mb-3">
            <label class="form-label d-block">Default for Level</label>
            <div class="form-check form-switch mt-2">
                <input type="hidden" name="is_default_for_level" value="0">
                <input class="form-check-input" type="checkbox" role="switch" id="is_default_for_level" name="is_default_for_level" value="1" @checked(old('is_default_for_level', $productPackaging->is_default_for_level))>
                <label class="form-check-label" for="is_default_for_level">Yes</label>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-3">
        <div class="mb-3">
            <label for="length" class="form-label">Length</label>
            <input type="number" step="0.01" min="0" id="length" name="length" class="form-control @error('length') is-invalid @enderror" value="{{ old('length', $productPackaging->length) }}">
            @error('length')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="col-12 col-lg-3">
        <div class="mb-3">
            <label for="width" class="form-label">Width</label>
            <input type="number" step="0.01" min="0" id="width" name="width" class="form-control @error('width') is-invalid @enderror" value="{{ old('width', $productPackaging->width) }}">
            @error('width')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="col-12 col-lg-3">
        <div class="mb-3">
            <label for="height" class="form-label">Height</label>
            <input type="number" step="0.01" min="0" id="height" name="height" class="form-control @error('height') is-invalid @enderror" value="{{ old('height', $productPackaging->height) }}">
            @error('height')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="col-12 col-lg-3">
        <div class="mb-3">
            <label for="dimension_unit" class="form-label">Dimension Unit</label>
            <input type="text" id="dimension_unit" name="dimension_unit" class="form-control @error('dimension_unit') is-invalid @enderror" value="{{ old('dimension_unit', $productPackaging->dimension_unit ?: 'CM') }}">
            @error('dimension_unit')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="col-12 col-lg-6">
        <div class="mb-3">
            <label for="net_weight" class="form-label">Net Weight</label>
            <input type="number" step="0.01" min="0" id="net_weight" name="net_weight" class="form-control @error('net_weight') is-invalid @enderror" value="{{ old('net_weight', $productPackaging->net_weight) }}">
            @error('net_weight')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="col-12 col-lg-6">
        <div class="mb-3">
            <label for="gross_weight" class="form-label">Gross Weight</label>
            <input type="number" step="0.01" min="0" id="gross_weight" name="gross_weight" class="form-control @error('gross_weight') is-invalid @enderror" value="{{ old('gross_weight', $productPackaging->gross_weight) }}">
            @error('gross_weight')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="col-12">
        <div class="mb-3">
            <label for="notes" class="form-label">Notes</label>
            <textarea id="notes" name="notes" rows="4" class="form-control @error('notes') is-invalid @enderror">{{ old('notes', $productPackaging->notes) }}</textarea>
            @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="col-12">
        @include('products.partials.barcode-preview', [
            'barcodeImageUrl' => $productPackaging->barcodeImageUrl(),
            'barcodeType' => old('barcode_type', $productPackaging->barcode_type),
            'barcodeValue' => old('barcode_number', $productPackaging->barcode_number),
            'barcodeLabel' => 'Packaging Barcode Preview',
        ])
    </div>
</div>

<div class="d-flex justify-content-end gap-2">
    <a href="{{ route('product-skus.packagings.index', $productSku) }}" class="btn btn-light">Cancel</a>
    <button type="submit" class="btn btn-primary">{{ $submitLabel }}</button>
</div>
