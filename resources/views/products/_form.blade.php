@csrf
@if ($formMethod !== 'POST')
    @method($formMethod)
@endif

<div class="row">
    <div class="col-12 col-lg-6">
        <div class="mb-3">
            <label for="product_name" class="form-label">Product Name</label>
            <input type="text" id="product_name" name="product_name" class="form-control @error('product_name') is-invalid @enderror" value="{{ old('product_name', $product->product_name) }}" required>
            @error('product_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="col-12 col-lg-3">
        <div class="mb-3">
            <label for="category" class="form-label">Category</label>
            <input type="text" id="category" name="category" class="form-control @error('category') is-invalid @enderror" value="{{ old('category', $product->category) }}" placeholder="Spices">
            @error('category')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="col-12 col-lg-3">
        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select id="status" name="status" class="form-select @error('status') is-invalid @enderror" required>
                @foreach ($statusOptions as $option)
                    <option value="{{ $option['value'] }}" @selected(old('status', $product->status) === $option['value'])>{{ $option['label'] }}</option>
                @endforeach
            </select>
            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="col-12 col-lg-6">
        <div class="mb-3">
            <label for="scientific_name" class="form-label">Scientific Name</label>
            <input type="text" id="scientific_name" name="scientific_name" class="form-control @error('scientific_name') is-invalid @enderror" value="{{ old('scientific_name', $product->scientific_name) }}" placeholder="Example: Syzygium aromaticum">
            @error('scientific_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="col-12 col-lg-3">
        <div class="mb-3">
            <label for="origin_area" class="form-label">Origin Area</label>
            <input type="text" id="origin_area" name="origin_area" class="form-control @error('origin_area') is-invalid @enderror" value="{{ old('origin_area', $product->origin_area) }}" placeholder="Maluku, Sulawesi">
            @error('origin_area')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="col-12 col-lg-3">
        <div class="mb-3">
            <label for="form" class="form-label">Default Form</label>
            <input type="text" id="form" name="form" class="form-control @error('form') is-invalid @enderror" value="{{ old('form', $product->form) }}" placeholder="Whole, Powder, Stick">
            @error('form')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="col-12 col-lg-3">
        <div class="mb-3">
            <label for="default_unit" class="form-label">Default Unit</label>
            <input type="text" id="default_unit" name="default_unit" class="form-control @error('default_unit') is-invalid @enderror" value="{{ old('default_unit', $product->default_unit ?: 'KG') }}" placeholder="KG">
            @error('default_unit')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="col-12">
        <div class="mb-3">
            <label for="notes" class="form-label">Notes</label>
            <textarea id="notes" name="notes" rows="4" class="form-control @error('notes') is-invalid @enderror">{{ old('notes', $product->notes) }}</textarea>
            @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
</div>

<div class="d-flex justify-content-end gap-2">
    <a href="{{ route('products.index') }}" class="btn btn-light">Cancel</a>
    <button type="submit" class="btn btn-primary">{{ $submitLabel }}</button>
</div>
