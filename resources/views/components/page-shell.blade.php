@props([
    'title',
    'subtitle',
    'phase',
    'checkpoints' => [],
])

<div class="page-content">
    <section class="row">
        <div class="col-12 col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h4>{{ $title }}</h4>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-4">{{ $subtitle }}</p>

                    <div class="alert alert-light-info color-info">
                        {{ $phase }}
                    </div>

                    <div class="row g-3">
                        @foreach ($checkpoints as $checkpoint)
                            <div class="col-md-6">
                                <div class="border rounded p-3 h-100">
                                    <h6 class="mb-2">{{ $checkpoint['title'] }}</h6>
                                    <p class="text-muted mb-0">{{ $checkpoint['description'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h4>Next Sprint Focus</h4>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-0">
                        Shell ini sengaja dibuat lebih dulu agar sprint berikutnya tinggal mengisi CRUD,
                        data table, dan business logic tanpa bongkar layout lagi.
                    </p>
                </div>
            </div>
        </div>
    </section>
</div>
