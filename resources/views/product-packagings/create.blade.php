@extends('layouts.app')

@section('content')
    <div class="page-content">
        <section class="row">
            <div class="col-12 col-xl-9">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-1">Add Packaging</h4>
                        <p class="text-muted mb-0">Tambah level packaging untuk SKU {{ $productSku->sku_code }}.</p>
                    </div>
                    <div class="card-body">
                        <form action="{{ $formAction }}" method="POST">
                            @include('product-packagings._form')
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
