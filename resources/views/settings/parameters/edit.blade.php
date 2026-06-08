@extends('layouts.app')

@section('content')
    <div class="page-content">
        <section class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <h4 class="mb-1">Parameter Form</h4>
                            <small class="text-muted">{{ $parameter->group_key }} / {{ $parameter->code }}</small>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{ $formAction }}" method="POST">
                            @include('settings.parameters._form')
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
