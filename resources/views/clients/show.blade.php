@extends('layouts.app')

@section('content')
    <div class="page-content">
        <section class="row">
            <div class="col-12 col-lg-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <h4 class="mb-1">{{ $client->company_name }}</h4>
                            <small class="text-muted">{{ $client->client_code }}</small>
                        </div>
                        <div class="d-flex gap-2">
                            <span class="badge {{ $statusBadgeMap[$client->status] ?? 'bg-secondary' }}">
                                {{ $statusLabelMap[$client->status] ?? ucfirst(str_replace('_', ' ', $client->status)) }}
                            </span>
                            <a href="{{ route('clients.edit', $client) }}" class="btn btn-sm btn-primary">Edit Client</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-lg-6">
                                <div class="mb-4">
                                    <small class="text-muted d-block mb-1">Interested Products</small>
                                    <div class="font-semibold">{{ $client->interested_products ?: '-' }}</div>
                                </div>
                            </div>
                            <div class="col-12 col-lg-6">
                                <div class="mb-4">
                                    <small class="text-muted d-block mb-1">Lead Source</small>
                                    <div class="font-semibold">{{ $client->source ?: '-' }}</div>
                                </div>
                            </div>
                            <div class="col-12 col-lg-6">
                                <div class="mb-4">
                                    <small class="text-muted d-block mb-1">PIC Name</small>
                                    <div class="font-semibold">{{ $client->pic_name ?: '-' }}</div>
                                </div>
                            </div>
                            <div class="col-12 col-lg-6">
                                <div class="mb-4">
                                    <small class="text-muted d-block mb-1">PIC Position</small>
                                    <div class="font-semibold">{{ $client->pic_position ?: '-' }}</div>
                                </div>
                            </div>
                            <div class="col-12 col-lg-6">
                                <div class="mb-4">
                                    <small class="text-muted d-block mb-1">PIC Email</small>
                                    <div class="font-semibold">{{ $client->pic_email ?: '-' }}</div>
                                </div>
                            </div>
                            <div class="col-12 col-lg-6">
                                <div class="mb-4">
                                    <small class="text-muted d-block mb-1">PIC WhatsApp</small>
                                    <div class="font-semibold">{{ $client->pic_whatsapp ?: '-' }}</div>
                                </div>
                            </div>
                            <div class="col-12 col-lg-6">
                                <div class="mb-4">
                                    <small class="text-muted d-block mb-1">Website</small>
                                    <div class="font-semibold">
                                        @if ($client->website)
                                            <a href="{{ $client->website }}" target="_blank" rel="noopener noreferrer">{{ $client->website }}</a>
                                        @else
                                            -
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-lg-6">
                                <div class="mb-4">
                                    <small class="text-muted d-block mb-1">Created By</small>
                                    <div class="font-semibold">{{ $client->creator?->name ?: '-' }}</div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-4">
                                    <small class="text-muted d-block mb-1">Address</small>
                                    <div class="font-semibold">{{ $client->address ?: '-' }}</div>
                                </div>
                            </div>
                            <div class="col-12 col-lg-4">
                                <div class="mb-4">
                                    <small class="text-muted d-block mb-1">Country</small>
                                    <div class="font-semibold">{{ $client->country ?: '-' }}</div>
                                </div>
                            </div>
                            <div class="col-12 col-lg-4">
                                <div class="mb-4">
                                    <small class="text-muted d-block mb-1">City</small>
                                    <div class="font-semibold">{{ $client->city ?: '-' }}</div>
                                </div>
                            </div>
                            <div class="col-12 col-lg-4">
                                <div class="mb-4">
                                    <small class="text-muted d-block mb-1">Pipeline Status</small>
                                    <div class="font-semibold">{{ $statusLabelMap[$client->status] ?? '-' }}</div>
                                </div>
                            </div>
                            <div class="col-12 col-lg-4">
                                <div class="mb-4">
                                    <small class="text-muted d-block mb-1">Target Quantity</small>
                                    <div class="font-semibold">{{ $client->target_quantity_kg ? number_format((float) $client->target_quantity_kg, 0) . ' kg' : '-' }}</div>
                                </div>
                            </div>
                            <div class="col-12 col-lg-4">
                                <div class="mb-4">
                                    <small class="text-muted d-block mb-1">Target Price</small>
                                    <div class="font-semibold">{{ $client->target_price ? $client->currency . ' ' . number_format((float) $client->target_price, 2) : '-' }}</div>
                                </div>
                            </div>
                            <div class="col-12 col-lg-4">
                                <div class="mb-4">
                                    <small class="text-muted d-block mb-1">Currency</small>
                                    <div class="font-semibold">{{ $client->currency ?: '-' }}</div>
                                </div>
                            </div>
                            <div class="col-12 col-lg-6">
                                <div class="mb-4">
                                    <small class="text-muted d-block mb-1">Preferred Incoterm</small>
                                    <div class="font-semibold">{{ $client->preferred_incoterm ?: '-' }}</div>
                                </div>
                            </div>
                            <div class="col-12 col-lg-6">
                                <div class="mb-4">
                                    <small class="text-muted d-block mb-1">Preferred Payment Term</small>
                                    <div class="font-semibold">{{ $client->preferred_payment_term ?: '-' }}</div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div>
                                    <small class="text-muted d-block mb-1">Notes</small>
                                    <div class="font-semibold">{{ $client->notes ?: '-' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h4>CRM Snapshot</h4>
                    </div>
                    <div class="card-body">
                        <div class="list-group">
                            <div class="list-group-item">
                                <small class="text-muted d-block">Pipeline Stage</small>
                                <span class="font-semibold">{{ $statusLabelMap[$client->status] ?? '-' }}</span>
                            </div>
                            <div class="list-group-item">
                                <small class="text-muted d-block">Contact Completeness</small>
                                <span class="font-semibold">{{ $client->pic_name && ($client->pic_email || $client->pic_whatsapp) ? 'Good' : 'Needs enrichment' }}</span>
                            </div>
                            <div class="list-group-item">
                                <small class="text-muted d-block">Commercial Readiness</small>
                                <span class="font-semibold">{{ $client->interested_products && $client->preferred_incoterm && $client->preferred_payment_term ? 'Ready for quotation' : 'Need more info' }}</span>
                            </div>
                        </div>

                        <div class="d-grid gap-2 mt-4">
                            <a href="{{ route('clients.edit', $client) }}" class="btn btn-primary">Edit Client</a>
                            <a href="{{ route('clients.index') }}" class="btn btn-light">Back to List</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
