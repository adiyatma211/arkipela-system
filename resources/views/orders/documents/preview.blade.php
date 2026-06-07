@extends('layouts.app')

@section('content')
    <style>
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
            color: #243b6b;
        }

        .document-preview-paper h1,
        .document-preview-paper h2,
        .document-preview-paper h3,
        .document-preview-paper h4 {
            color: #1d3f8f;
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

        @media print {
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
            }

            .document-preview-paper {
                max-width: none;
                padding: 0;
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
                                <button type="button" class="btn btn-light-primary" onclick="window.print()">Print Preview</button>
                                <a href="{{ route('orders.show', $order) }}" class="btn btn-light">Back to Order</a>
                            </div>
                        </div>
                    </div>

                    <div class="document-preview-card">
                        <div class="document-preview-paper">
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
