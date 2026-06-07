@once
    @push('styles')
        <style>
            .image-preview-trigger {
                cursor: zoom-in;
            }

            .supplier-image-modal[hidden] {
                display: none !important;
            }

            .supplier-image-modal {
                position: fixed;
                inset: 0;
                z-index: 1080;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 1.5rem;
            }

            .supplier-image-modal__backdrop {
                position: absolute;
                inset: 0;
                background: rgba(15, 23, 42, 0.78);
                backdrop-filter: blur(4px);
            }

            .supplier-image-modal__dialog {
                position: relative;
                z-index: 1;
                width: min(100%, 1100px);
                max-height: calc(100vh - 3rem);
                display: flex;
                flex-direction: column;
                overflow: hidden;
                border-radius: 1rem;
                background: #fff;
                box-shadow: 0 30px 60px rgba(15, 23, 42, 0.25);
            }

            .supplier-image-modal__toolbar {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 0.75rem;
                padding: 1rem 1.25rem;
                border-bottom: 1px solid #e5e7eb;
                background: #f8fafc;
            }

            .supplier-image-modal__actions {
                display: flex;
                align-items: center;
                gap: 0.5rem;
                flex-wrap: wrap;
            }

            .supplier-image-modal__viewport {
                position: relative;
                flex: 1;
                min-height: 320px;
                overflow: auto;
                background: #0f172a;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 1.5rem;
            }

            .supplier-image-modal__image {
                max-width: 100%;
                max-height: 70vh;
                object-fit: contain;
                transform-origin: center center;
                transition: transform 0.15s ease;
            }

            .supplier-image-modal__footer {
                padding: 1rem 1.25rem;
                border-top: 1px solid #e5e7eb;
                background: #fff;
            }

            @media (max-width: 576px) {
                .supplier-image-modal {
                    padding: 0.75rem;
                }

                .supplier-image-modal__toolbar {
                    align-items: flex-start;
                    flex-direction: column;
                }
            }
        </style>
    @endpush

    <div id="supplier-image-preview-modal" class="supplier-image-modal" hidden aria-hidden="true">
        <div class="supplier-image-modal__backdrop" data-image-preview-close></div>
        <div class="supplier-image-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="supplier-image-preview-title">
            <div class="supplier-image-modal__toolbar">
                <div>
                    <div id="supplier-image-preview-title" class="fw-semibold">Photo Preview</div>
                    <div id="supplier-image-preview-meta" class="text-muted small"></div>
                </div>
                <div class="supplier-image-modal__actions">
                    <button type="button" class="btn btn-sm btn-light-secondary" data-image-preview-zoom-out>Zoom Out</button>
                    <button type="button" class="btn btn-sm btn-light-primary" data-image-preview-reset>Reset</button>
                    <button type="button" class="btn btn-sm btn-light-secondary" data-image-preview-zoom-in>Zoom In</button>
                    <button type="button" class="btn btn-sm btn-danger" data-image-preview-close>Close</button>
                </div>
            </div>
            <div class="supplier-image-modal__viewport">
                <img id="supplier-image-preview-image" src="" alt="" class="supplier-image-modal__image">
            </div>
            <div class="supplier-image-modal__footer">
                <div id="supplier-image-preview-caption" class="text-muted small mb-0"></div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const modal = document.getElementById('supplier-image-preview-modal');
                const image = document.getElementById('supplier-image-preview-image');
                const title = document.getElementById('supplier-image-preview-title');
                const meta = document.getElementById('supplier-image-preview-meta');
                const caption = document.getElementById('supplier-image-preview-caption');
                const zoomInButton = modal?.querySelector('[data-image-preview-zoom-in]');
                const zoomOutButton = modal?.querySelector('[data-image-preview-zoom-out]');
                const resetButton = modal?.querySelector('[data-image-preview-reset]');
                const closeButtons = modal?.querySelectorAll('[data-image-preview-close]');

                if (!modal || !image || !title || !meta || !caption || !zoomInButton || !zoomOutButton || !resetButton || !closeButtons?.length) {
                    return;
                }

                let scale = 1;
                let previousOverflow = '';

                const clampScale = function (nextScale) {
                    return Math.min(4, Math.max(0.5, nextScale));
                };

                const updateScale = function () {
                    image.style.transform = 'scale(' + scale + ')';
                    meta.textContent = 'Zoom ' + Math.round(scale * 100) + '%';
                };

                const openModal = function (trigger) {
                    const src = trigger.getAttribute('data-preview-src');

                    if (!src) {
                        return;
                    }

                    scale = 1;
                    image.src = src;
                    image.alt = trigger.getAttribute('data-preview-title') || 'Photo Preview';
                    title.textContent = trigger.getAttribute('data-preview-title') || 'Photo Preview';
                    caption.textContent = trigger.getAttribute('data-preview-caption') || 'Tanpa caption';
                    updateScale();
                    previousOverflow = document.body.style.overflow;
                    document.body.style.overflow = 'hidden';
                    modal.hidden = false;
                    modal.setAttribute('aria-hidden', 'false');
                };

                const closeModal = function () {
                    modal.hidden = true;
                    modal.setAttribute('aria-hidden', 'true');
                    image.src = '';
                    document.body.style.overflow = previousOverflow;
                };

                document.addEventListener('click', function (event) {
                    const trigger = event.target.closest('[data-image-preview-trigger]');

                    if (trigger) {
                        openModal(trigger);
                    }
                });

                closeButtons.forEach(function (button) {
                    button.addEventListener('click', closeModal);
                });

                zoomInButton.addEventListener('click', function () {
                    scale = clampScale(scale + 0.25);
                    updateScale();
                });

                zoomOutButton.addEventListener('click', function () {
                    scale = clampScale(scale - 0.25);
                    updateScale();
                });

                resetButton.addEventListener('click', function () {
                    scale = 1;
                    updateScale();
                });

                document.addEventListener('keydown', function (event) {
                    if (modal.hidden) {
                        return;
                    }

                    if (event.key === 'Escape') {
                        closeModal();
                    }

                    if (event.key === '+' || event.key === '=') {
                        scale = clampScale(scale + 0.25);
                        updateScale();
                    }

                    if (event.key === '-') {
                        scale = clampScale(scale - 0.25);
                        updateScale();
                    }

                    if (event.key === '0') {
                        scale = 1;
                        updateScale();
                    }
                });
            });
        </script>
    @endpush
@endonce
