<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 Forbidden - Archipela Web</title>
    <link rel="shortcut icon" href="{{ asset('assetes/logo/logo.png') }}" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('template/dist/assets/compiled/css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('template/dist/assets/compiled/css/app-dark.css') }}">
</head>
<body>
    <div id="error">
        <div class="error-page container">
            <div class="col-md-8 col-12 offset-md-2">
                <div class="text-center">
                    <h1 class="error-title">403</h1>
                    <p class="fs-5 text-gray-600">Anda tidak punya akses ke halaman ini.</p>
                    <p class="text-muted">
                        Hubungi owner atau administrator sistem jika hak akses Anda memang seharusnya tersedia.
                    </p>
                    <a href="{{ auth()->check() ? route(auth()->user()->homeRoute()) : route('login') }}" class="btn btn-lg btn-outline-primary mt-3">
                        Back to Safe Page
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
