@extends('layouts.app')

@section('content')
    <div class="page-content">
        <section class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <h4 class="mb-1">{{ $role->name }}</h4>
                            <small class="text-muted">{{ $role->slug }}</small>
                        </div>
                        <a href="{{ route('settings.roles.index') }}" class="btn btn-light">Back</a>
                    </div>
                    <div class="card-body">
                        @if ($role->slug === 'owner')
                            <div class="alert alert-warning">
                                Role owner tetap full access lewat override sistem. Checklist di bawah tetap bisa disimpan untuk dokumentasi, tetapi owner tidak akan dibatasi oleh daftar ini.
                            </div>
                        @endif

                        <form action="{{ route('settings.roles.update', $role) }}" method="POST">
                            @csrf
                            @method('PUT')

                            @error('permissions')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror

                            <div class="row">
                                @foreach ($permissionGroups as $module => $permissions)
                                    <div class="col-12 col-xl-6">
                                        <div class="card border mb-4">
                                            <div class="card-header">
                                                <h5 class="mb-0 text-capitalize">{{ str_replace('.', ' ', $module) }}</h5>
                                            </div>
                                            <div class="card-body">
                                                @foreach ($permissions as $permission)
                                                    <div class="form-check mb-3">
                                                        <input
                                                            class="form-check-input"
                                                            type="checkbox"
                                                            value="{{ $permission->id }}"
                                                            id="permission_{{ $permission->id }}"
                                                            name="permissions[]"
                                                            @checked(in_array($permission->id, old('permissions', $role->permissions->pluck('id')->all())))
                                                        >
                                                        <label class="form-check-label" for="permission_{{ $permission->id }}">
                                                            <strong>{{ $permission->name }}</strong>
                                                            <span class="d-block text-muted small">{{ $permission->description ?: $permission->slug }}</span>
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('settings.roles.index') }}" class="btn btn-light">Cancel</a>
                                <button type="submit" class="btn btn-primary">Save Permissions</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
