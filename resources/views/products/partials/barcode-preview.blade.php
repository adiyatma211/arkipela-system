@php
    $barcodeImageUrl = $barcodeImageUrl ?? null;
    $barcodeType = $barcodeType ?? null;
    $barcodeValue = $barcodeValue ?? null;
    $barcodeLabel = $barcodeLabel ?? 'Barcode Preview';
    $barcodeDownloadPngUrl = $barcodeDownloadPngUrl ?? null;
    $barcodeDownloadJpegUrl = $barcodeDownloadJpegUrl ?? null;
    $previewSupported = filled($barcodeImageUrl) && filled($barcodeValue);
@endphp

<div class="border rounded-3 p-3 bg-light-subtle h-100">
    <small class="text-muted d-block mb-2">{{ $barcodeLabel }}</small>
    @if ($previewSupported)
        <img src="{{ $barcodeImageUrl }}" alt="{{ $barcodeValue }}" class="img-fluid mb-2" style="max-height: 140px;">
        <div class="fw-semibold">{{ $barcodeValue }}</div>
        <div class="text-muted small">{{ $barcodeType ?: 'Barcode' }}</div>
        @if ($barcodeDownloadPngUrl || $barcodeDownloadJpegUrl)
            <div class="d-flex gap-2 flex-wrap mt-3">
                @if ($barcodeDownloadPngUrl)
                    <a href="{{ $barcodeDownloadPngUrl }}" class="btn btn-sm btn-light-primary">Download PNG</a>
                @endif
                @if ($barcodeDownloadJpegUrl)
                    <a href="{{ $barcodeDownloadJpegUrl }}" class="btn btn-sm btn-light-secondary">Download JPEG</a>
                @endif
            </div>
        @endif
    @else
        <div class="text-muted small">
            Preview will be generated after save for supported retail types (`UPC-A` and `EAN-13`).
        </div>
    @endif
</div>
