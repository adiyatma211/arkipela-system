@once
    <style>
        .archipela-flash-stack {
            display: grid;
            gap: 0.85rem;
            margin-bottom: 1.5rem;
        }

        .archipela-flash {
            position: relative;
            overflow: hidden;
            border: 1px solid transparent;
            border-radius: 1rem;
            box-shadow: 0 12px 32px rgba(15, 23, 42, 0.08);
        }

        .archipela-flash::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            width: 0.35rem;
        }

        .archipela-flash--success {
            background: #f1fcf6;
            border-color: #ccefd9;
            color: #155d3b;
        }

        .archipela-flash--success::before {
            background: #198754;
        }

        .archipela-flash--danger {
            background: #fff5f5;
            border-color: #f4cccc;
            color: #9f1d1d;
        }

        .archipela-flash--danger::before {
            background: #dc3545;
        }

        .archipela-flash__body {
            display: flex;
            align-items: flex-start;
            gap: 0.9rem;
            padding: 1rem 1.1rem 1rem 1.25rem;
        }

        .archipela-flash__icon {
            width: 2.4rem;
            height: 2.4rem;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 1.05rem;
        }

        .archipela-flash--success .archipela-flash__icon {
            background: rgba(25, 135, 84, 0.12);
        }

        .archipela-flash--danger .archipela-flash__icon {
            background: rgba(220, 53, 69, 0.1);
        }

        .archipela-flash__content {
            flex: 1;
            min-width: 0;
        }

        .archipela-flash__title {
            font-weight: 800;
            margin-bottom: 0.15rem;
        }

        .archipela-flash__message {
            margin: 0;
            color: inherit;
            opacity: 0.92;
        }

        .archipela-flash__close {
            margin-left: auto;
            border: 0;
            background: transparent;
            color: inherit;
            opacity: 0.7;
            line-height: 1;
            padding: 0.15rem;
            flex-shrink: 0;
        }

        .archipela-flash__close:hover {
            opacity: 1;
        }

        .archipela-flash__details {
            margin-top: 0.65rem;
        }

        .archipela-flash__summary {
            cursor: pointer;
            font-weight: 700;
            list-style: none;
        }

        .archipela-flash__summary::-webkit-details-marker {
            display: none;
        }

        .archipela-flash__list {
            margin: 0.7rem 0 0;
            padding-left: 1.1rem;
        }

        @media (max-width: 576px) {
            .archipela-flash__body {
                padding-right: 0.9rem;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('[data-flash-close]').forEach(function (button) {
                button.addEventListener('click', function () {
                    button.closest('.archipela-flash')?.remove();
                });
            });

            window.setTimeout(function () {
                document.querySelectorAll('[data-flash-autoclose="true"]').forEach(function (flash) {
                    flash.remove();
                });
            }, 5000);
        });
    </script>
@endonce

@if (session('status') || session('error') || $errors->any())
    <div class="archipela-flash-stack">
        @if (session('status'))
            <div class="archipela-flash archipela-flash--success" data-flash-autoclose="true">
                <div class="archipela-flash__body">
                    <div class="archipela-flash__icon">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <div class="archipela-flash__content">
                        <div class="archipela-flash__title">Berhasil</div>
                        <p class="archipela-flash__message">{{ session('status') }}</p>
                    </div>
                    <button type="button" class="archipela-flash__close" data-flash-close aria-label="Close">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="archipela-flash archipela-flash--danger">
                <div class="archipela-flash__body">
                    <div class="archipela-flash__icon">
                        <i class="bi bi-x-octagon-fill"></i>
                    </div>
                    <div class="archipela-flash__content">
                        <div class="archipela-flash__title">Gagal</div>
                        <p class="archipela-flash__message">{{ session('error') }}</p>
                    </div>
                    <button type="button" class="archipela-flash__close" data-flash-close aria-label="Close">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="archipela-flash archipela-flash--danger">
                <div class="archipela-flash__body">
                    <div class="archipela-flash__icon">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                    </div>
                    <div class="w-100">
                        <div class="archipela-flash__title">Data belum bisa diproses</div>
                        <p class="archipela-flash__message">
                            Ada {{ $errors->count() }} masalah yang perlu diperbaiki sebelum melanjutkan.
                        </p>
                        <details class="archipela-flash__details" @if($errors->count() <= 2) open @endif>
                            <summary class="archipela-flash__summary">Lihat detail error</summary>
                            <ul class="archipela-flash__list">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </details>
                    </div>
                    <button type="button" class="archipela-flash__close" data-flash-close aria-label="Close">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            </div>
        @endif
    </div>
@endif
