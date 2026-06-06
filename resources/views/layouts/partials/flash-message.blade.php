@if (session('status'))
    <div class="alert alert-light-primary color-primary mb-4">
        {{ session('status') }}
    </div>
@endif
