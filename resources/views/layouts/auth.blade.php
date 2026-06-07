<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Login' }} - Archipela Web</title>
    <link rel="shortcut icon" href="{{ asset('assetes/logo/logo.png') }}" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('template/dist/assets/compiled/css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('template/dist/assets/compiled/css/app-dark.css') }}">
    <link rel="stylesheet" href="{{ asset('template/dist/assets/compiled/css/auth.css') }}">
    @stack('styles')
</head>
<body>
    <script src="{{ asset('template/dist/assets/static/js/initTheme.js') }}"></script>
    <div id="auth">
        @yield('content')
    </div>
    @stack('scripts')
</body>
</html>
