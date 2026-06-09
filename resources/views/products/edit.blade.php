@extends('layouts.app')

@section('content')
    <div class="page-content">
        <section class="row">
            <div class="col-12 col-xl-9">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-1">Edit Product</h4>
                        <p class="text-muted mb-0">Update master data untuk {{ $product->product_code }}.</p>
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
