@if (session('status'))
    <div class="alert alert-light-primary color-primary mb-4">
        {{ session('status') }}
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-light-danger color-danger mb-4">
        <ul class="mb-0 ps-3">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
