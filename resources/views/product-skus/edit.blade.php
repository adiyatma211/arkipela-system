@extends('layouts.app')

@section('content')
    <div class="page-content">
        <section class="row">
            <div class="col-12 col-xl-9">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-1">Edit Product SKU</h4>
                        <p class="text-muted mb-0">Perbarui SKU {{ $productSku->sku_code }} tanpa perlu isi ulang banyak field barcode yang tumpang tindih.</p>
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
