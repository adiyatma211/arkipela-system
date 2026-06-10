@csrf
@if ($formMethod !== 'POST')
    @method($formMethod)
@endif

<div class="row">
    <div class="col-12 col-lg-6">
        <div class="mb-3">
            <label class="form-label">Parent Product</label>
            <div class="border rounded-3 px-3 py-2 bg-light-subtle">
                <div class="fw-semibold">{{ $product->product_name }}</div>
                <div class="text-muted small">{{ $product->product_code }}</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-6">
        <div class="mb-3">
            <label for="variant_name" class="form-label">Variant Name</label>
            <input type="text" id="variant_name" name="variant_name" class="form-control @error('variant_name') is-invalid @enderror" value="{{ old('variant_name', $productSku->variant_name) }}" placeholder="Example: Whole 100g Jar" required>
            @error('variant_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="col-12 col-lg-4">
        <div class="mb-3">
            <label for="brand_name" class="form-label">Brand Name</label>
            <input type="text" id="brand_name" name="brand_name" class="form-control @error('brand_name') is-invalid @enderror" value="{{ old('brand_name', $productSku->brand_name) }}" placeholder="Arkipela Spice">
            @error('brand_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="col-12 col-lg-4">
        <div class="mb-3">
            <label for="net_weight" class="form-label">Net Weight</label>
            <input type="number" step="0.01" min="0" id="net_weight" name="net_weight" class="form-control @error('net_weight') is-invalid @enderror" value="{{ old('net_weight', $productSku->net_weight) }}" placeholder="100">
            @error('net_weight')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="col-12 col-lg-4">
        <div class="mb-3">
            <label for="weight_unit" class="form-label">Weight Unit</label>
            <input type="text" id="weight_unit" name="weight_unit" class="form-control @error('weight_unit') is-invalid @enderror" value="{{ old('weight_unit', $productSku->weight_unit ?: 'G') }}" placeholder="G">
            @error('weight_unit')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="col-12 col-lg-4">
        <div class="mb-3">
            <label for="sellable_unit" class="form-label">Sellable Unit</label>
            <input type="text" id="sellable_unit" name="sellable_unit" class="form-control @error('sellable_unit') is-invalid @enderror" value="{{ old('sellable_unit', $productSku->sellable_unit ?: 'EACH') }}" placeholder="EACH">
            @error('sellable_unit')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="col-12 col-lg-3">
        <div class="mb-3">
            <label class="form-label d-block">Retail Sellable</label>
            <div class="form-check form-switch mt-2">
                <input type="hidden" name="is_retail_sellable" value="0">
                <input class="form-check-input" type="checkbox" role="switch" id="is_retail_sellable" name="is_retail_sellable" value="1" @checked(old('is_retail_sellable', $productSku->is_retail_sellable))>
                <label class="form-check-label" for="is_retail_sellable">Yes</label>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-3">
        <div class="mb-3">
            <label class="form-label d-block">Active</label>
            <div class="form-check form-switch mt-2">
                <input type="hidden" name="is_active" value="0">
                <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1" @checked(old('is_active', $productSku->is_active ?? true))>
                <label class="form-check-label" for="is_active">Yes</label>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="mb-3">
            <label for="notes" class="form-label">Notes</label>
            <textarea id="notes" name="notes" rows="4" class="form-control @error('notes') is-invalid @enderror">{{ old('notes', $productSku->notes) }}</textarea>
            @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
    @include('products.partials.barcode-fields', [
        'fieldPrefix' => 'sku',
        'barcodeTypeOptions' => $barcodeTypeOptions ?? [],
        'barcodeTypeValue' => old('barcode_type', $productSku->barcode_type),
        'barcodeNumberValue' => old('barcode_number', $productSku->barcode_number),
        'gtinValue' => old('gtin', $productSku->gtin),
        'upcValue' => old('upc', $productSku->upc),
        'eanValue' => old('ean', $productSku->ean),
        'barcodeImageUrl' => $productSku->barcodeImageUrl(),
        'barcodeLabel' => 'SKU Barcode Preview',
        'barcodeDownloadPngUrl' => $productSku->exists ? route('product-skus.barcode.download', [$productSku, 'png']) : null,
        'barcodeDownloadJpegUrl' => $productSku->exists ? route('product-skus.barcode.download', [$productSku, 'jpeg']) : null,
    ])
</div>

<div class="d-flex justify-content-end gap-2">
    <a href="{{ route('products.skus.index', $product) }}" class="btn btn-light">Cancel</a>
    <button type="submit" class="btn btn-primary">{{ $submitLabel }}</button>
</div>
