@php
    $fieldPrefix = $fieldPrefix ?? 'barcode';
    $barcodeTypeValue = $barcodeTypeValue ?? null;
    $barcodeNumberValue = $barcodeNumberValue ?? null;
    $gtinValue = $gtinValue ?? null;
    $upcValue = $upcValue ?? null;
    $eanValue = $eanValue ?? null;
    $barcodeImageUrl = $barcodeImageUrl ?? null;
    $barcodeLabel = $barcodeLabel ?? 'Barcode Preview';
    $barcodeDownloadPngUrl = $barcodeDownloadPngUrl ?? null;
    $barcodeDownloadJpegUrl = $barcodeDownloadJpegUrl ?? null;
    $showAdvanced = filled($gtinValue)
        || filled($upcValue)
        || filled($eanValue)
        || $errors->has('gtin')
        || $errors->has('upc')
        || $errors->has('ean');
@endphp

<div class="col-12">
    <div class="border rounded-3 p-3 mb-3">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
            <div>
                <h6 class="mb-1">Barcode Setup</h6>
                <p class="text-muted small mb-0">
                    Pilih tipe barcode lalu isi satu nomor utama. Sistem akan menormalkan digit retail dan membuat preview sesudah data disimpan.
                </p>
            </div>
        </div>

        <div class="row">
            <div class="col-12 col-lg-4">
                <div class="mb-3">
                    <label for="{{ $fieldPrefix }}_barcode_type" class="form-label">Barcode Type</label>
                    <select
                        id="{{ $fieldPrefix }}_barcode_type"
                        name="barcode_type"
                        class="form-select @error('barcode_type') is-invalid @enderror"
                    >
                        <option value="">Select barcode type</option>
                        @foreach (($barcodeTypeOptions ?? []) as $option)
                            <option value="{{ $option['value'] }}" @selected($barcodeTypeValue === $option['value'])>{{ $option['label'] }}</option>
                        @endforeach
                    </select>
                    @error('barcode_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="col-12 col-lg-8">
                <div class="mb-3">
                    <label for="{{ $fieldPrefix }}_barcode_number" class="form-label">Barcode Digits</label>
                    <input
                        type="text"
                        id="{{ $fieldPrefix }}_barcode_number"
                        name="barcode_number"
                        class="form-control @error('barcode_number') is-invalid @enderror"
                        value="{{ $barcodeNumberValue }}"
                        placeholder="Enter barcode digits once"
                        inputmode="numeric"
                    >
                    @error('barcode_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        <div class="alert alert-light-secondary mb-0">
            <div class="small text-muted mb-1" id="{{ $fieldPrefix }}_barcode_hint">
                Pilih tipe barcode untuk melihat aturan digitnya.
            </div>
            <div class="small">
                Canonical barcode:
                <span class="fw-semibold" id="{{ $fieldPrefix }}_barcode_canonical">{{ $barcodeNumberValue ?: '-' }}</span>
            </div>
        </div>

        <details class="mt-3" @if ($showAdvanced) open @endif>
            <summary class="fw-semibold small">Advanced barcode fields</summary>
            <p class="text-muted small mt-2 mb-3">
                Opsional. Gunakan hanya jika Anda memang perlu menyimpan GTIN, UPC, atau EAN mentah secara eksplisit.
            </p>

            <div class="row">
                <div class="col-12 col-lg-4">
                    <div class="mb-3">
                        <label for="{{ $fieldPrefix }}_gtin" class="form-label">GTIN</label>
                        <input
                            type="text"
                            id="{{ $fieldPrefix }}_gtin"
                            name="gtin"
                            class="form-control @error('gtin') is-invalid @enderror"
                            value="{{ $gtinValue }}"
                            placeholder="Optional GTIN"
                            inputmode="numeric"
                        >
                        @error('gtin')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="col-12 col-lg-4">
                    <div class="mb-3">
                        <label for="{{ $fieldPrefix }}_upc" class="form-label">UPC</label>
                        <input
                            type="text"
                            id="{{ $fieldPrefix }}_upc"
                            name="upc"
                            class="form-control @error('upc') is-invalid @enderror"
                            value="{{ $upcValue }}"
                            placeholder="Optional UPC"
                            inputmode="numeric"
                        >
                        @error('upc')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="col-12 col-lg-4">
                    <div class="mb-3">
                        <label for="{{ $fieldPrefix }}_ean" class="form-label">EAN</label>
                        <input
                            type="text"
                            id="{{ $fieldPrefix }}_ean"
                            name="ean"
                            class="form-control @error('ean') is-invalid @enderror"
                            value="{{ $eanValue }}"
                            placeholder="Optional EAN"
                            inputmode="numeric"
                        >
                        @error('ean')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </details>
    </div>
</div>

<div class="col-12">
    @include('products.partials.barcode-preview', [
        'barcodeImageUrl' => $barcodeImageUrl,
        'barcodeType' => $barcodeTypeValue,
        'barcodeValue' => $barcodeNumberValue,
        'barcodeLabel' => $barcodeLabel,
        'barcodeDownloadPngUrl' => $barcodeDownloadPngUrl,
        'barcodeDownloadJpegUrl' => $barcodeDownloadJpegUrl,
    ])
</div>

@push('scripts')
    <script>
        (() => {
            const typeInput = document.getElementById(@json($fieldPrefix . '_barcode_type'));
            const digitsInput = document.getElementById(@json($fieldPrefix . '_barcode_number'));
            const hintNode = document.getElementById(@json($fieldPrefix . '_barcode_hint'));
            const canonicalNode = document.getElementById(@json($fieldPrefix . '_barcode_canonical'));

            if (!typeInput || !digitsInput || !hintNode || !canonicalNode) {
                return;
            }

            const normalizeDigits = (value) => (value || '').replace(/\D+/g, '');

            const computeUpcCheckDigit = (baseDigits) => {
                let sumOdd = 0;
                let sumEven = 0;

                [...baseDigits].forEach((digit, index) => {
                    if (index % 2 === 0) {
                        sumOdd += Number(digit);
                    } else {
                        sumEven += Number(digit);
                    }
                });

                return String((10 - (((sumOdd * 3) + sumEven) % 10)) % 10);
            };

            const computeEanCheckDigit = (baseDigits) => {
                let sum = 0;

                [...baseDigits].forEach((digit, index) => {
                    sum += Number(digit) * (index % 2 === 0 ? 1 : 3);
                });

                return String((10 - (sum % 10)) % 10);
            };

            const describeBarcode = () => {
                const type = typeInput.value;
                const digits = normalizeDigits(digitsInput.value);
                let hint = 'Pilih tipe barcode untuk melihat aturan digitnya.';
                let canonical = digits || '-';
                let placeholder = 'Enter barcode digits once';

                if (!type) {
                    hint = 'Pilih tipe barcode lalu isi nomor utama pada field Barcode Digits.';
                } else if (type === 'UPC-A') {
                    placeholder = '11 or 12 digits';

                    if (digits.length === 11) {
                        canonical = digits + computeUpcCheckDigit(digits);
                        hint = 'UPC-A menerima 11 atau 12 digit. Jika 11 digit, sistem akan menambahkan check digit terakhir.';
                    } else if (digits.length === 12) {
                        canonical = digits;
                        hint = 'UPC-A siap dipakai. Sistem akan memvalidasi check digit saat disimpan.';
                    } else if (digits.length > 0) {
                        hint = 'UPC-A membutuhkan 11 atau 12 digit.';
                    } else {
                        hint = 'UPC-A membutuhkan 11 atau 12 digit.';
                    }
                } else if (type === 'EAN-13') {
                    placeholder = '12 or 13 digits';

                    if (digits.length === 12) {
                        canonical = digits + computeEanCheckDigit(digits);
                        hint = 'EAN-13 menerima 12 atau 13 digit. Jika 12 digit, sistem akan menambahkan check digit terakhir.';
                    } else if (digits.length === 13) {
                        canonical = digits;
                        hint = 'EAN-13 siap dipakai. Sistem akan memvalidasi check digit saat disimpan.';
                    } else if (digits.length > 0) {
                        hint = 'EAN-13 membutuhkan 12 atau 13 digit.';
                    } else {
                        hint = 'EAN-13 membutuhkan 12 atau 13 digit.';
                    }
                } else if (type === 'ITF-14') {
                    placeholder = '14 digits';
                    hint = 'ITF-14 biasanya dipakai untuk distribution packaging. Preview SVG saat ini fokus ke UPC-A dan EAN-13.';
                } else {
                    placeholder = 'Barcode digits';
                    hint = 'Untuk tipe ini, sistem menyimpan digit barcode tanpa preview retail otomatis.';
                }

                digitsInput.placeholder = placeholder;
                canonicalNode.textContent = canonical;
                hintNode.textContent = hint;
            };

            digitsInput.addEventListener('input', () => {
                const normalized = normalizeDigits(digitsInput.value);

                if (digitsInput.value !== normalized) {
                    digitsInput.value = normalized;
                }

                describeBarcode();
            });

            typeInput.addEventListener('change', describeBarcode);
            describeBarcode();
        })();
    </script>
@endpush
