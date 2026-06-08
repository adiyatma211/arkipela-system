@extends('layouts.app')

@section('content')
    <style>
        @page {
            size: A4 portrait;
            margin: 9mm;
        }

        body {
            background: #f4f5f7;
        }

        .document-preview-shell {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .document-preview-card {
            border: 1px solid rgba(67, 94, 190, 0.16);
            border-radius: 1rem;
            background: #fff;
            box-shadow: 0 12px 32px rgba(15, 23, 42, 0.06);
        }

        .document-preview-paper {
            width: 100%;
            max-width: 960px;
            margin: 0 auto;
            padding: 2.5rem;
            color: #1f2937;
        }

        .document-preview-paper h1,
        .document-preview-paper h2,
        .document-preview-paper h3,
        .document-preview-paper h4 {
            color: #111827;
        }

        .document-preview-meta-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1rem;
        }

        .document-preview-meta-card {
            border: 1px solid rgba(67, 94, 190, 0.12);
            border-radius: 0.85rem;
            padding: 1rem 1.1rem;
            background: #fbfcff;
        }

        .document-preview-table th {
            background: #f3f6ff;
            white-space: nowrap;
        }

        .document-preview-table th,
        .document-preview-table td {
            vertical-align: top;
        }

        .invoice-sheet {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            padding: 8mm;
            background: #fff;
            color: #111;
            box-shadow: 0 18px 44px rgba(15, 23, 42, 0.08);
            font-size: 11px;
            line-height: 1.25;
        }

        .invoice-title {
            margin: 0;
            text-align: center;
            font-size: 19px;
            font-weight: 700;
            letter-spacing: 0.08em;
        }

        .invoice-logo {
            max-width: 110px;
            max-height: 58px;
            object-fit: contain;
        }

        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .invoice-table th,
        .invoice-table td {
            border: 1px solid #222;
            padding: 4px 5px;
            vertical-align: top;
        }

        .invoice-table th {
            font-weight: 700;
            text-align: left;
        }

        .invoice-table .text-center {
            text-align: center;
        }

        .invoice-table .text-right {
            text-align: right;
        }

        .invoice-table .text-top {
            vertical-align: top;
        }

        .invoice-cell-label {
            display: block;
            margin-bottom: 2px;
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }

        .invoice-company {
            font-size: 16px;
            font-weight: 700;
        }

        .invoice-muted {
            color: #374151;
        }

        .invoice-small {
            font-size: 10px;
        }

        .invoice-xs {
            font-size: 9px;
        }

        .invoice-section-gap {
            height: 8px;
        }

        .invoice-product-title {
            font-weight: 700;
        }

        .invoice-product-meta {
            margin-top: 2px;
            white-space: pre-line;
        }

        .invoice-buyer-cell {
            font-size: 10px;
        }

        .invoice-buyer-cell .invoice-product-title {
            font-size: 13px;
        }

        .invoice-buyer-cell .invoice-product-meta {
            overflow-wrap: anywhere;
            word-break: break-word;
        }

        .invoice-signature-box {
            min-height: 82px;
            display: flex;
            align-items: end;
            justify-content: center;
            padding-bottom: 10px;
            font-size: 24px;
            font-family: "Brush Script MT", "Segoe Script", cursive;
        }

        .invoice-screen-note {
            max-width: 960px;
            margin: 0 auto 0.75rem;
            color: #4b5563;
            font-size: 0.9rem;
        }

        @media (max-width: 992px) {
            .document-preview-paper {
                padding: 1rem;
            }

            .invoice-sheet {
                width: 100%;
                min-height: auto;
                padding: 10px;
                overflow-x: auto;
            }
        }

        @media print {
            body {
                background: #fff;
            }

            .document-preview-actions,
            .navbar,
            .main-sidebar,
            .footer,
            .flash-message-container {
                display: none !important;
            }

            .document-preview-card {
                box-shadow: none;
                border: none;
                background: transparent;
            }

            .document-preview-paper {
                max-width: none;
                padding: 0;
            }

            .invoice-sheet {
                width: auto;
                min-height: auto;
                padding: 0;
                box-shadow: none;
            }

            .invoice-screen-note {
                display: none;
            }
        }
    </style>

    <div class="page-content">
        <section class="row">
            <div class="col-12">
                <div class="document-preview-shell">
                    <div class="card">
                        <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-2 document-preview-actions">
                            <div>
                                <div class="font-semibold">{{ $documentType->label() }}</div>
                                <small class="text-muted">{{ $document->document_number ?: 'Draft preview' }}</small>
                            </div>
                            <div class="d-flex flex-wrap gap-2">
                                <button type="button" class="btn btn-light-primary" onclick="window.print()">Print / Save PDF</button>
                                <a href="{{ route('orders.show', $order) }}" class="btn btn-light">Back to Order</a>
                            </div>
                        </div>
                    </div>

                    <div class="document-preview-card">
                        <div class="document-preview-paper">
                            <div class="invoice-screen-note">
                                Preview ini sudah diformat untuk kertas A4. Gunakan tombol <strong>Print / Save PDF</strong> untuk melihat hasil final seperti PDF.
                            </div>
                            @if ($documentType === \App\Enums\OrderDocumentType::COMMERCIAL_INVOICE)
                                @include('orders.documents.partials.commercial-invoice')
                            @elseif ($documentType === \App\Enums\OrderDocumentType::PACKING_LIST)
                                @include('orders.documents.partials.packing-list')
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
