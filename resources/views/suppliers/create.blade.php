@extends('layouts.app')

@section('content')
    <div class="page-content">
        <section class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Supplier Form</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ $formAction }}" method="POST" enctype="multipart/form-data">
                            @include('suppliers._form')
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
