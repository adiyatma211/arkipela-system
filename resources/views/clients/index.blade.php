@extends('layouts.app')

@section('content')
    <div class="page-content">
        <section class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                            <div>
                                <h4 class="mb-1">Client CRM Pipeline</h4>
                                <p class="text-muted mb-0">Kelola buyer prospect, kebutuhan produk, dan progress komersial client export.</p>
                            </div>
                            <a href="{{ route('clients.create') }}" class="btn btn-primary">Add Client</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ route('clients.index') }}" class="mb-4">
                            <div class="row g-3">
                                <div class="col-12 col-lg-4">
                                    <label class="form-label">Search</label>
                                    <input
                                        type="text"
                                        name="search"
                                        value="{{ $filters['search'] }}"
                                        class="form-control"
                                        placeholder="Code, company, PIC, email"
                                    >
                                </div>
                                <div class="col-12 col-lg-3">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-select">
                                        <option value="">All status</option>
                                        @foreach ($statusOptions as $option)
                                            <option value="{{ $option['value'] }}" @selected($filters['status'] === $option['value'])>
                                                {{ $option['label'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12 col-lg-3">
                                    <label class="form-label">Country</label>
                                    <input
                                        type="text"
                                        name="country"
                                        value="{{ $filters['country'] }}"
                                        class="form-control"
                                        placeholder="Indonesia, UAE, India"
                                    >
                                </div>
                                <div class="col-12 col-lg-2">
                                    <label class="form-label">Product</label>
                                    <input
                                        type="text"
                                        name="product"
                                        value="{{ $filters['product'] }}"
                                        class="form-control"
                                        placeholder="Clove"
                                    >
                                </div>
                                <div class="col-12 d-flex justify-content-end gap-2">
                                    <a href="{{ route('clients.index') }}" class="btn btn-light">Reset</a>
                                    <button type="submit" class="btn btn-primary">Apply Filter</button>
                                </div>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-hover table-lg">
                                <thead>
                                    <tr>
                                        <th>Client Code</th>
                                        <th>Company</th>
                                        <th>Country</th>
                                        <th>Interested Products</th>
                                        <th>Target Qty</th>
                                        <th>Status</th>
                                        <th>Commercial</th>
                                        <th>PIC</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($clients as $client)
                                        <tr>
                                            <td class="font-semibold">{{ $client->client_code }}</td>
                                            <td>
                                                <div class="font-semibold">{{ $client->company_name }}</div>
                                                <small class="text-muted">{{ $client->website ?: 'No website' }}</small>
                                            </td>
                                            <td>
                                                <div>{{ $client->country ?: '-' }}</div>
                                                <small class="text-muted">{{ $client->city ?: 'No city' }}</small>
                                            </td>
                                            <td>{{ $client->interested_products ?: '-' }}</td>
                                            <td>{{ $client->target_quantity_kg ? number_format((float) $client->target_quantity_kg, 0) . ' kg' : '-' }}</td>
                                            <td>
                                                <span class="badge {{ $statusBadgeMap[$client->status] ?? 'bg-secondary' }}">
                                                    {{ $statusLabelMap[$client->status] ?? ucfirst(str_replace('_', ' ', $client->status)) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div>{{ $client->preferred_incoterm ?: '-' }}</div>
                                                <small class="text-muted">{{ $client->preferred_payment_term ?: 'No payment term' }}</small>
                                            </td>
                                            <td>
                                                <div>{{ $client->pic_name ?: '-' }}</div>
                                                <small class="text-muted">{{ $client->pic_email ?: ($client->pic_whatsapp ?: 'No contact') }}</small>
                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center gap-2 flex-wrap">
                                                    <a href="{{ route('clients.show', $client) }}" class="btn btn-sm btn-light-primary">View</a>
                                                    <a href="{{ route('clients.edit', $client) }}" class="btn btn-sm btn-light-warning">Edit</a>
                                                    <form action="{{ route('clients.destroy', $client) }}" method="POST" onsubmit="return confirm('Delete this client?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-light-danger">Delete</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center text-muted py-5">
                                                Belum ada client. Tambahkan buyer pertama untuk mulai mengisi pipeline CRM ArkipelaSpice.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $clients->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
