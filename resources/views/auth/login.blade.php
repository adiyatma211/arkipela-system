@extends('layouts.auth', ['title' => 'Login'])

@push('styles')
    <style>
        .archipela-auth-logo {
            display: inline-flex;
            align-items: center;
            gap: 0.8rem;
            text-decoration: none;
        }

        .archipela-auth-logo-mark {
            width: 2.6rem;
            height: 2.6rem;
            border-radius: 0.8rem;
            background: linear-gradient(135deg, #41bbdd 0%, #435ebe 100%);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 1.2rem;
            box-shadow: 0 12px 28px rgba(67, 94, 190, 0.22);
        }

        .archipela-auth-logo-text {
            font-weight: 800;
            font-size: 1.35rem;
            color: #25396f;
            letter-spacing: 0.01em;
        }

        .archipela-auth-panel {
            position: relative;
            height: 100%;
            display: flex;
            align-items: flex-end;
            padding: 3rem;
            overflow: hidden;
        }

        .archipela-auth-panel::before {
            content: "";
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at top right, rgba(255, 255, 255, 0.18), transparent 30%),
                radial-gradient(circle at bottom left, rgba(65, 187, 221, 0.22), transparent 30%);
        }

        .archipela-auth-panel-content {
            position: relative;
            z-index: 1;
            max-width: 34rem;
            color: #fff;
        }

        .archipela-auth-pill {
            display: inline-flex;
            align-items: center;
            padding: 0.45rem 0.8rem;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.14);
            color: rgba(255, 255, 255, 0.92);
            font-weight: 700;
            font-size: 0.82rem;
            margin-bottom: 1rem;
        }

        .archipela-auth-panel-content h2 {
            color: #fff;
            font-size: 2.2rem;
            font-weight: 800;
            line-height: 1.15;
            margin-bottom: 1rem;
        }

        .archipela-auth-panel-content p {
            color: rgba(255, 255, 255, 0.82);
            font-size: 1rem;
            line-height: 1.7;
            margin-bottom: 1.5rem;
        }

        .archipela-auth-highlights {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 1rem;
        }

        .archipela-auth-highlight {
            padding: 1rem 1.1rem;
            border-radius: 1rem;
            background: rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(6px);
        }

        .archipela-auth-highlight strong {
            display: block;
            color: #fff;
            font-size: 0.98rem;
            margin-bottom: 0.3rem;
        }

        .archipela-auth-highlight span {
            color: rgba(255, 255, 255, 0.75);
            font-size: 0.9rem;
            line-height: 1.5;
        }

        .archipela-login-help {
            border-top: 1px solid rgba(0, 0, 0, 0.06);
            margin-top: 2rem;
            padding-top: 1.5rem;
        }

        .archipela-login-help code {
            font-size: 0.98rem;
            font-weight: 700;
        }

        @media (max-width: 991.98px) {
            .archipela-auth-highlights {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush

@section('content')
    <div class="row h-100">
        <div class="col-lg-5 col-12">
            <div id="auth-left">
                <div class="auth-logo mb-4">
                    <a href="{{ route('login') }}" class="archipela-auth-logo">
                        <span class="archipela-auth-logo-mark">
                            <i class="bi bi-grid-1x2-fill"></i>
                        </span>
                        <span class="archipela-auth-logo-text">Archipela Web</span>
                    </a>
                </div>

                <h1 class="auth-title">Welcome back</h1>
                <p class="auth-subtitle mb-5">Masuk untuk melanjutkan operasional internal Archipela.</p>

                @if (session('status'))
                    <div class="alert alert-light-success color-success">{{ session('status') }}</div>
                @endif

                <form action="{{ route('login.store') }}" method="POST">
                    @csrf
                    <div class="form-group position-relative has-icon-left mb-4">
                        <input
                            type="email"
                            name="email"
                            value="{{ old('email', 'owner@archipela.test') }}"
                            class="form-control form-control-xl @error('email') is-invalid @enderror"
                            placeholder="Email"
                            required
                        >
                        <div class="form-control-icon">
                            <i class="bi bi-person"></i>
                        </div>
                        @error('email')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group position-relative has-icon-left mb-4">
                        <input
                            type="password"
                            name="password"
                            class="form-control form-control-xl @error('password') is-invalid @enderror"
                            placeholder="Password"
                            required
                        >
                        <div class="form-control-icon">
                            <i class="bi bi-shield-lock"></i>
                        </div>
                        @error('password')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-check form-check-lg d-flex align-items-end mb-4">
                        <input class="form-check-input me-2" type="checkbox" id="remember" name="remember" value="1">
                        <label class="form-check-label text-gray-600" for="remember">
                            Remember me
                        </label>
                    </div>

                    <button class="btn btn-primary btn-block btn-lg shadow-lg mt-5">Log in</button>
                </form>

                <div class="archipela-login-help text-center text-lg fs-4">
                    <p class="text-gray-600 mb-2">Default seed user</p>
                    <p class="font-bold mb-0">
                        <code>owner@archipela.test / password</code>
                    </p>
                </div>
            </div>
        </div>

        <div class="col-lg-7 d-none d-lg-block">
            <div id="auth-right">
                <div class="archipela-auth-panel">
                    <div class="archipela-auth-panel-content">
                        <span class="archipela-auth-pill">Export Operating System</span>
                        <h2>Kelola supplier, client, order, dan dashboard owner dari satu workspace.</h2>
                        <p>
                            Fondasi MVP Archipela dirancang untuk membantu tim procurement, sales,
                            dan owner melihat progres operasional export dengan lebih cepat dan rapi.
                        </p>

                        <div class="archipela-auth-highlights">
                            <div class="archipela-auth-highlight">
                                <strong>Supplier Management</strong>
                                <span>Kontrol sourcing, status supplier, dan kapasitas supply.</span>
                            </div>
                            <div class="archipela-auth-highlight">
                                <strong>Client CRM</strong>
                                <span>Pantau pipeline buyer dari lead sampai active buyer.</span>
                            </div>
                            <div class="archipela-auth-highlight">
                                <strong>Order Control</strong>
                                <span>Siapkan alur order, margin, dan shipment progression.</span>
                            </div>
                            <div class="archipela-auth-highlight">
                                <strong>Owner Dashboard</strong>
                                <span>Ringkasan KPI dan risk alert untuk pengambilan keputusan cepat.</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
