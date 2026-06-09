@extends('layouts.app')

@section('content')
    <div class="page-content">
        <section class="row">
            <div class="col-12 col-xl-9">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-1">Add Product</h4>
                        <p class="text-muted mb-0">Buat commodity master baru sebagai source of truth lintas modul.</p>
                    </div>
                    <div class="card-body">
                        <form action="{{ $formAction }}" method="POST">
                            @include('products._form')
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
