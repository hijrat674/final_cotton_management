@php
    $totalInput = (float) ($productionSummary['total_input_quantity'] ?? 0);
    $totalOutput = (float) ($productionSummary['total_output_quantity'] ?? 0);
    $totalWaste = max($totalInput - $totalOutput, 0);
    $efficiency = $totalInput > 0 ? round(($totalOutput / $totalInput) * 100, 1) : 0;
@endphp

<section class="summary-showcase production-summary-section content-card mt-4">
    <div class="users-summary-header">
        <div>
            <h3 class="users-summary-title">{{ __('dashboard.production.title') }}</h3>
            <p class="users-summary-text mb-0">{{ __('dashboard.production.text') }}</p>
        </div>
        <a href="{{ route('production-stages.index') }}" class="btn btn-outline-primary users-summary-action">
            {{ __('dashboard.production.open') }}
        </a>
    </div>

    <div class="row g-4 users-summary-grid">
        <div class="col-sm-6 col-xl-4">
            <article class="users-summary-card production-summary-card-total">
                <span class="users-summary-icon" aria-hidden="true">
                    <i class="bi bi-gear-wide-connected"></i>
                </span>
                <div class="users-summary-copy">
                    <span class="users-summary-label">{{ __('dashboard.production.total') }}</span>
                    <div class="users-summary-value-wrap">
                        <strong class="users-summary-value">{{ $productionSummary['total_stages'] }}</strong>
                    </div>
                </div>
            </article>
        </div>

        <div class="col-sm-6 col-xl-4">
            <article class="users-summary-card production-summary-card-today">
                <span class="users-summary-icon" aria-hidden="true">
                    <i class="bi bi-calendar2-check-fill"></i>
                </span>
                <div class="users-summary-copy">
                    <span class="users-summary-label">{{ __('dashboard.production.today') }}</span>
                    <div class="users-summary-value-wrap">
                        <strong class="users-summary-value">{{ $productionSummary['today_production'] }}</strong>
                    </div>
                </div>
            </article>
        </div>

        <div class="col-sm-6 col-xl-4">
            <article class="users-summary-card production-summary-card-input">
                <span class="users-summary-icon" aria-hidden="true">
                    <i class="bi bi-box-arrow-in-down"></i>
                </span>
                <div class="users-summary-copy">
                    <span class="users-summary-label">{{ __('dashboard.production.input') }}</span>
                    <div class="users-summary-value-wrap">
                        <strong class="users-summary-value production-summary-line">
                            <span class="production-summary-value">{{ number_format($totalInput, 3) }}</span>
                            <span class="production-summary-unit">&nbsp;kg</span>
                        </strong>
                    </div>
                </div>
            </article>
        </div>

        <div class="col-sm-6 col-xl-4">
            <article class="users-summary-card production-summary-card-output">
                <span class="users-summary-icon" aria-hidden="true">
                    <i class="bi bi-box-arrow-up-right"></i>
                </span>
                <div class="users-summary-copy">
                    <span class="users-summary-label">{{ __('dashboard.production.output') }}</span>
                    <div class="users-summary-value-wrap">
                        <strong class="users-summary-value production-summary-line">
                            <span class="production-summary-value">{{ number_format($totalOutput, 3) }}</span>
                            <span class="production-summary-unit">&nbsp;kg</span>
                        </strong>
                    </div>
                </div>
            </article>
        </div>

        <div class="col-sm-6 col-xl-4">
            <article class="users-summary-card production-summary-card-waste">
                <span class="users-summary-icon" aria-hidden="true">
                    <i class="bi bi-trash3-fill"></i>
                </span>
                <div class="users-summary-copy">
                    <span class="users-summary-label">{{ __('dashboard.production.waste') }}</span>
                    <div class="users-summary-value-wrap">
                        <strong class="users-summary-value production-summary-line">
                            <span class="production-summary-value">{{ number_format($totalWaste, 3) }}</span>
                            <span class="production-summary-unit">&nbsp;kg</span>
                        </strong>
                    </div>
                </div>
            </article>
        </div>

        <div class="col-sm-6 col-xl-4">
            <article class="users-summary-card production-summary-card-efficiency">
                <span class="users-summary-icon" aria-hidden="true">
                    <i class="bi bi-percent"></i>
                </span>
                <div class="users-summary-copy">
                    <span class="users-summary-label">{{ __('dashboard.production.efficiency') }}</span>
                    <div class="users-summary-value-wrap">
                        <strong class="users-summary-value production-summary-line">
                            <span class="production-summary-value">{{ number_format($efficiency, 1) }}</span>
                            <span class="production-summary-unit">&nbsp;%</span>
                        </strong>
                    </div>
                </div>
            </article>
        </div>
    </div>
</section>
