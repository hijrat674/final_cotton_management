<div
    class="modal fade action-overlay-modal"
    id="actionOverlayModal"
    tabindex="-1"
    aria-labelledby="actionOverlayModalLabel"
    aria-hidden="true"
>
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable action-overlay-dialog">
        <div class="modal-content action-overlay-content">
            <div class="modal-header action-overlay-header">
                <div class="action-overlay-heading">
                    <span class="action-overlay-kicker">{{ __('app.quick_action') }}</span>
                    <h2 class="modal-title action-overlay-title" id="actionOverlayModalLabel">{{ __('app.loading') }}</h2>
                </div>
                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="{{ __('app.close') }}"
                ></button>
            </div>

            <div class="modal-body action-overlay-body">
                <div class="action-overlay-loading" data-modal-loading>
                    <div class="spinner-border text-success" role="status" aria-hidden="true"></div>
                    <p class="mb-0">{{ __('app.loading') }}</p>
                </div>

                <iframe
                    class="action-overlay-frame"
                    title="{{ __('app.quick_action') }}"
                    loading="lazy"
                    data-modal-frame
                ></iframe>
            </div>

            <div class="modal-footer action-overlay-footer">
                <a href="#" class="btn btn-outline-secondary" data-modal-open-full>{{ __('app.open_full_page') }}</a>
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">{{ __('app.close') }}</button>
            </div>
        </div>
    </div>
</div>
