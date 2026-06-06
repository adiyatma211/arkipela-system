@csrf
@if ($formMethod !== 'POST')
    @method($formMethod)
@endif

<div class="row">
    <div class="col-12 col-lg-6">
        <div class="mb-3">
            <label for="company_name" class="form-label">Company Name</label>
            <input type="text" id="company_name" name="company_name" class="form-control @error('company_name') is-invalid @enderror" value="{{ old('company_name', $client->company_name) }}" required>
            @error('company_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="col-12 col-lg-3">
        <div class="mb-3">
            <label for="country" class="form-label">Country</label>
            <input type="text" id="country" name="country" class="form-control @error('country') is-invalid @enderror" value="{{ old('country', $client->country ?: 'Indonesia') }}">
            @error('country')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="col-12 col-lg-3">
        <div class="mb-3">
            <label for="city" class="form-label">City</label>
            <input type="text" id="city" name="city" class="form-control @error('city') is-invalid @enderror" value="{{ old('city', $client->city) }}">
            @error('city')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="col-12">
        <div class="mb-3">
            <label for="address" class="form-label">Address</label>
            <textarea id="address" name="address" rows="3" class="form-control @error('address') is-invalid @enderror">{{ old('address', $client->address) }}</textarea>
            @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="col-12 col-lg-6">
        <div class="mb-3">
            <label for="website" class="form-label">Website</label>
            <input type="url" id="website" name="website" class="form-control @error('website') is-invalid @enderror" value="{{ old('website', $client->website) }}" placeholder="https://company.com">
            @error('website')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="col-12 col-lg-6">
        <div class="mb-3">
            <label for="source" class="form-label">Lead Source</label>
            <input type="text" id="source" name="source" class="form-control @error('source') is-invalid @enderror" value="{{ old('source', $client->source) }}" placeholder="Trade show, WhatsApp, referral">
            @error('source')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="col-12 col-lg-4">
        <div class="mb-3">
            <label for="pic_name" class="form-label">PIC Name</label>
            <input type="text" id="pic_name" name="pic_name" class="form-control @error('pic_name') is-invalid @enderror" value="{{ old('pic_name', $client->pic_name) }}">
            @error('pic_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="col-12 col-lg-4">
        <div class="mb-3">
            <label for="pic_position" class="form-label">PIC Position</label>
            <input type="text" id="pic_position" name="pic_position" class="form-control @error('pic_position') is-invalid @enderror" value="{{ old('pic_position', $client->pic_position) }}">
            @error('pic_position')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="col-12 col-lg-4">
        <div class="mb-3">
            <label for="pic_whatsapp" class="form-label">PIC WhatsApp</label>
            <input type="text" id="pic_whatsapp" name="pic_whatsapp" class="form-control @error('pic_whatsapp') is-invalid @enderror" value="{{ old('pic_whatsapp', $client->pic_whatsapp) }}">
            @error('pic_whatsapp')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="col-12 col-lg-6">
        <div class="mb-3">
            <label for="pic_email" class="form-label">PIC Email</label>
            <input type="email" id="pic_email" name="pic_email" class="form-control @error('pic_email') is-invalid @enderror" value="{{ old('pic_email', $client->pic_email) }}">
            @error('pic_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="col-12 col-lg-6">
        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select id="status" name="status" class="form-select @error('status') is-invalid @enderror" required>
                @foreach ($statusOptions as $option)
                    <option value="{{ $option['value'] }}" @selected(old('status', $client->status) === $option['value'])>{{ $option['label'] }}</option>
                @endforeach
            </select>
            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="col-12">
        <div class="mb-3">
            <label for="interested_products" class="form-label">Interested Products</label>
            <input type="text" id="interested_products" name="interested_products" class="form-control @error('interested_products') is-invalid @enderror" value="{{ old('interested_products', $client->interested_products) }}" placeholder="Example: Clove, Cinnamon, Nutmeg">
            @error('interested_products')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="col-12 col-lg-4">
        <div class="mb-3">
            <label for="target_quantity_kg" class="form-label">Target Quantity (kg)</label>
            <input type="number" step="0.01" min="0" id="target_quantity_kg" name="target_quantity_kg" class="form-control @error('target_quantity_kg') is-invalid @enderror" value="{{ old('target_quantity_kg', $client->target_quantity_kg) }}">
            @error('target_quantity_kg')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="col-12 col-lg-4">
        <div class="mb-3">
            <label for="target_price" class="form-label">Target Price</label>
            <input type="number" step="0.01" min="0" id="target_price" name="target_price" class="form-control @error('target_price') is-invalid @enderror" value="{{ old('target_price', $client->target_price) }}">
            @error('target_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="col-12 col-lg-4">
        <div class="mb-3">
            <label for="currency" class="form-label">Currency</label>
            <input type="text" id="currency" name="currency" class="form-control @error('currency') is-invalid @enderror" value="{{ old('currency', $client->currency ?: 'USD') }}" placeholder="USD">
            @error('currency')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="col-12 col-lg-6">
        <div class="mb-3">
            <label for="preferred_incoterm" class="form-label">Preferred Incoterm</label>
            <input type="text" id="preferred_incoterm" name="preferred_incoterm" class="form-control @error('preferred_incoterm') is-invalid @enderror" value="{{ old('preferred_incoterm', $client->preferred_incoterm) }}" placeholder="FOB, CIF, EXW">
            @error('preferred_incoterm')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="col-12 col-lg-6">
        <div class="mb-3">
            <label for="preferred_payment_term" class="form-label">Preferred Payment Term</label>
            <input type="text" id="preferred_payment_term" name="preferred_payment_term" class="form-control @error('preferred_payment_term') is-invalid @enderror" value="{{ old('preferred_payment_term', $client->preferred_payment_term) }}" placeholder="T/T 30% DP, LC at sight">
            @error('preferred_payment_term')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="col-12">
        <div class="mb-3">
            <label for="notes" class="form-label">Notes</label>
            <textarea id="notes" name="notes" rows="4" class="form-control @error('notes') is-invalid @enderror">{{ old('notes', $client->notes) }}</textarea>
            @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
</div>

<div class="d-flex justify-content-end gap-2">
    <a href="{{ route('clients.index') }}" class="btn btn-light">Cancel</a>
    <button type="submit" class="btn btn-primary">{{ $submitLabel }}</button>
</div>
