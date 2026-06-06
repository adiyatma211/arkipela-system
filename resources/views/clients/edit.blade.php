@extends('layouts.app')

@section('content')
    <div class="page-content">
        <section class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Edit {{ $client->client_code }}</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ $formAction }}" method="POST">
                            @include('clients._form')
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
