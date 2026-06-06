@extends('layouts.app')

@section('content')
    <div class="page-content">
        <section class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                            <div>
                                <h4 class="mb-1">User Accounts</h4>
                                <p class="text-muted mb-0">Kelola akun login internal, role, status aktif, dan jejak login terakhir.</p>
                            </div>
                            <a href="{{ route('settings.users.create') }}" class="btn btn-primary">Add User</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-light-primary">
                            Owner tidak bisa dibatasi oleh permission biasa. Sistem juga akan menolak delete atau deactivate owner terakhir dan akun yang sedang dipakai login.
                        </div>

                        <form method="GET" action="{{ route('settings.users.index') }}" class="mb-4">
                            <div class="row g-3">
                                <div class="col-12 col-lg-4">
                                    <label class="form-label">Search</label>
                                    <input type="text" name="search" value="{{ $filters['search'] }}" class="form-control" placeholder="Name or email">
                                </div>
                                <div class="col-12 col-lg-3">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-select">
                                        <option value="">All status</option>
                                        <option value="active" @selected($filters['status'] === 'active')>Active</option>
                                        <option value="inactive" @selected($filters['status'] === 'inactive')>Inactive</option>
                                    </select>
                                </div>
                                <div class="col-12 col-lg-3">
                                    <label class="form-label">Role</label>
                                    <select name="role_id" class="form-select">
                                        <option value="">All roles</option>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->id }}" @selected($filters['role_id'] === (string) $role->id)>{{ $role->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12 col-lg-2 d-flex align-items-end gap-2">
                                    <button type="submit" class="btn btn-primary w-100">Apply</button>
                                    <a href="{{ route('settings.users.index') }}" class="btn btn-light w-100">Reset</a>
                                </div>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-hover table-lg">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Status</th>
                                        <th>Last Login</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($users as $user)
                                        <tr>
                                            <td class="font-semibold">{{ $user->name }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>{{ $user->role?->name ?: '-' }}</td>
                                            <td>
                                                <span class="badge {{ $user->status === 'active' ? 'bg-light-success' : 'bg-light-danger' }}">
                                                    {{ ucfirst($user->status) }}
                                                </span>
                                            </td>
                                            <td>{{ $user->last_login_at?->format('d M Y H:i') ?: 'Never' }}</td>
                                            <td class="text-center">
                                                <div class="d-inline-flex flex-wrap justify-content-center gap-2">
                                                    <a href="{{ route('settings.users.edit', $user) }}" class="btn btn-sm btn-light-warning">Edit</a>

                                                    <form method="POST" action="{{ route('settings.users.status', $user) }}" class="d-inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="hidden" name="status" value="{{ $user->status === 'active' ? 'inactive' : 'active' }}">
                                                        <button
                                                            type="submit"
                                                            class="btn btn-sm {{ $user->status === 'active' ? 'btn-light-secondary' : 'btn-light-success' }}"
                                                            onclick="return confirm('Change status for {{ $user->email }}?')"
                                                        >
                                                            {{ $user->status === 'active' ? 'Deactivate' : 'Activate' }}
                                                        </button>
                                                    </form>

                                                    <form method="POST" action="{{ route('settings.users.destroy', $user) }}" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button
                                                            type="submit"
                                                            class="btn btn-sm btn-light-danger"
                                                            onclick="return confirm('Delete user {{ $user->email }}? This action cannot be undone.')"
                                                        >
                                                            Delete
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-5">Belum ada user.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $users->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
