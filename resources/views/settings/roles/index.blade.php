@extends('layouts.app')

@section('content')
    <div class="page-content">
        <section class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                            <div>
                                <h4 class="mb-1">Role Access Matrix</h4>
                                <p class="text-muted mb-0">Hak akses sekarang dibaca dari tabel permissions dan relasi role-permission.</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-light-primary">
                            <strong>Owner</strong> tetap full access sebagai override global. Perubahan checklist terutama berlaku untuk role selain owner.
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover table-lg">
                                <thead>
                                    <tr>
                                        <th>Role</th>
                                        <th>Slug</th>
                                        <th>Description</th>
                                        <th>Assigned Permissions</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($roles as $role)
                                        <tr>
                                            <td class="font-semibold">{{ $role->name }}</td>
                                            <td><code>{{ $role->slug }}</code></td>
                                            <td>{{ $role->description ?: '-' }}</td>
                                            <td>
                                                @if ($role->permissions->isEmpty())
                                                    <span class="text-muted">No permissions assigned</span>
                                                @else
                                                    <div class="d-flex flex-wrap gap-2">
                                                        @foreach ($role->permissions as $permission)
                                                            <span class="badge bg-light-secondary text-dark">{{ $permission->name }}</span>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('settings.roles.edit', $role) }}" class="btn btn-sm btn-primary">Edit Access</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
