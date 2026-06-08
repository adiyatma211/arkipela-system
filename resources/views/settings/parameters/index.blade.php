@extends('layouts.app')

@section('content')
    <div class="page-content">
        <section class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                            <div>
                                <h4 class="mb-1">Parameter Registry</h4>
                                <p class="text-muted mb-0">Satu tempat untuk master qty unit, packaging, size unit, dan lookup reusable lain.</p>
                            </div>
                            <a href="{{ route('settings.parameters.create') }}" class="btn btn-primary">Add Parameter</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ route('settings.parameters.index') }}" class="mb-4">
                            <div class="row g-3">
                                <div class="col-12 col-lg-4">
                                    <label for="group_key" class="form-label">Group</label>
                                    <select id="group_key" name="group_key" class="form-select">
                                        <option value="">All groups</option>
                                        @foreach ($groupOptions as $groupOption)
                                            <option value="{{ $groupOption }}" @selected($filters['group_key'] === $groupOption)>{{ $groupOption }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12 col-lg-4">
                                    <label for="status" class="form-label">Status</label>
                                    <select id="status" name="status" class="form-select">
                                        <option value="">All status</option>
                                        <option value="active" @selected($filters['status'] === 'active')>Active</option>
                                        <option value="inactive" @selected($filters['status'] === 'inactive')>Inactive</option>
                                    </select>
                                </div>
                                <div class="col-12 col-lg-4">
                                    <label for="search" class="form-label">Search</label>
                                    <input type="text" id="search" name="search" class="form-control" value="{{ $filters['search'] }}" placeholder="Code, name, description">
                                </div>
                            </div>
                            <div class="d-flex justify-content-end gap-2 mt-3">
                                <a href="{{ route('settings.parameters.index') }}" class="btn btn-light">Reset</a>
                                <button type="submit" class="btn btn-primary">Apply Filter</button>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-hover table-lg">
                                <thead>
                                    <tr>
                                        <th>Group</th>
                                        <th>Code</th>
                                        <th>Name</th>
                                        <th>Description</th>
                                        <th>Attributes</th>
                                        <th>Sort</th>
                                        <th>Status</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($parameters as $parameter)
                                        <tr>
                                            <td><code>{{ $parameter->group_key }}</code></td>
                                            <td><code>{{ $parameter->code }}</code></td>
                                            <td class="font-semibold">{{ $parameter->name }}</td>
                                            <td>{{ $parameter->description ?: '-' }}</td>
                                            <td>
                                                @if ($parameter->attributes)
                                                    <code>{{ json_encode($parameter->attributes, JSON_UNESCAPED_SLASHES) }}</code>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>{{ $parameter->sort_order }}</td>
                                            <td>
                                                <span class="badge {{ $parameter->is_active ? 'bg-light-success' : 'bg-light-secondary' }}">
                                                    {{ $parameter->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('settings.parameters.edit', $parameter) }}" class="btn btn-sm btn-primary">Edit</a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center text-muted">No parameter found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                            {{ $parameters->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
