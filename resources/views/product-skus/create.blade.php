@extends('layouts.app')

@section('content')
    <div class="page-content">
        <section class="row">
            <div class="col-12 col-xl-9">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-1">Add Product SKU</h4>
                        <p class="text-muted mb-0">Barcode retail dibuat di level SKU. Cukup pilih tipe barcode lalu isi satu nomor utama.</p>
                    </div>
                    <div class="card-body">
                        <form action="{{ $formAction }}" method="POST">
                            @include('product-skus._form')
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
