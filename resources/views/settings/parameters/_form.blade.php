@csrf
@if ($formMethod !== 'POST')
    @method($formMethod)
@endif

<div class="row">
    <div class="col-12 col-lg-6">
        <div class="mb-3">
            <label for="group_key" class="form-label">Group Key</label>
            <input list="parameter-group-options" id="group_key" name="group_key" class="form-control @error('group_key') is-invalid @enderror" value="{{ old('group_key', $parameter->group_key) }}" placeholder="quantity_unit" required>
            <datalist id="parameter-group-options">
                @foreach ($groupOptions as $groupOption)
                    <option value="{{ $groupOption }}"></option>
                @endforeach
            </datalist>
            <div class="form-text">Gunakan group yang konsisten, misalnya `quantity_unit`, `dimension_unit`, `packaging_type`.</div>
            @error('group_key')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="col-12 col-lg-6">
        <div class="mb-3">
            <label for="code" class="form-label">Code</label>
            <input type="text" id="code" name="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code', $parameter->code) }}" placeholder="PCS" required>
            <div class="form-text">Kode akan dinormalisasi ke uppercase dan dipakai sebagai nilai internal.</div>
            @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="col-12 col-lg-8">
        <div class="mb-3">
            <label for="name" class="form-label">Display Name</label>
            <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $parameter->name) }}" placeholder="Pieces" required>
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="col-12 col-lg-4">
        <div class="mb-3">
            <label for="sort_order" class="form-label">Sort Order</label>
            <input type="number" min="0" id="sort_order" name="sort_order" class="form-control @error('sort_order') is-invalid @enderror" value="{{ old('sort_order', $parameter->sort_order ?? 0) }}">
            @error('sort_order')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="col-12">
        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea id="description" name="description" rows="3" class="form-control @error('description') is-invalid @enderror" placeholder="Short explanation for operators">{{ old('description', $parameter->description) }}</textarea>
            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="col-12">
        <div class="mb-3">
            <label for="attributes_json" class="form-label">Attributes JSON</label>
            <textarea id="attributes_json" name="attributes_json" rows="6" class="form-control font-monospace @error('attributes_json') is-invalid @enderror" placeholder='{"to_cm_factor": 2.54}'>{{ old('attributes_json', $attributesJson) }}</textarea>
            <div class="form-text">Optional. Cocok untuk metadata seperti konversi unit, flag khusus, atau default behavior.</div>
            @error('attributes_json')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="col-12">
        <div class="form-check mb-4">
            <input type="checkbox" id="is_active" name="is_active" value="1" class="form-check-input" @checked(old('is_active', $parameter->is_active ?? true))>
            <label for="is_active" class="form-check-label">Active parameter</label>
        </div>
    </div>
</div>

<div class="d-flex justify-content-end gap-2">
    <a href="{{ route('settings.parameters.index') }}" class="btn btn-light">Cancel</a>
    <button type="submit" class="btn btn-primary">{{ $submitLabel }}</button>
</div>
