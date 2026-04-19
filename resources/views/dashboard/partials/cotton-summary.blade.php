@php
    $latestEntry = collect($cottonSummary['latest_entries'] ?? [])->first();
@endphp

<section class="summary-showcase cotton-summary-section content-card mt-4">
    <div class="users-summary-header">
        <div>
            <h3 class="users-summary-title">{{ __('dashboard.cotton.title') }}</h3>
            <p class="users-summary-text mb-0">{{ __('dashboard.cotton.text') }}</p>
        </div>
        <a href="{{ route('cotton-entries.index') }}" class="btn btn-outline-primary users-summary-action">
            {{ __('dashboard.cotton.open') }}
        </a>
    </div>

    <div class="row g-4 users-summary-grid">
        <div class="col-sm-6 col-xl-4">
            <article class="users-summary-card cotton-summary-card-total">
                <span class="users-summary-icon" aria-hidden="true">
                    <i class="bi bi-truck-front-fill"></i>
                </span>
                <div class="users-summary-copy">
                    <span class="users-summary-label">{{ __('dashboard.cotton.total_entries') }}</span>
                    <div class="users-summary-value-wrap">
                        <strong class="users-summary-value">{{ $cottonSummary['total_entries'] }}</strong>
                    </div>
                </div>
            </article>
        </div>

        <div class="col-sm-6 col-xl-4">
            <article class="users-summary-card cotton-summary-card-today">
                <span class="users-summary-icon" aria-hidden="true">
                    <i class="bi bi-calendar2-check-fill"></i>
                </span>
                <div class="users-summary-copy">
                    <span class="users-summary-label">{{ __('dashboard.cotton.today_entries') }}</span>
                    <div class="users-summary-value-wrap">
                        <strong class="users-summary-value">{{ $cottonSummary['today_entries'] }}</strong>
                    </div>
                </div>
            </article>
        </div>

        <div class="col-sm-6 col-xl-4">
            <article class="users-summary-card cotton-summary-card-intake">
                <span class="users-summary-icon" aria-hidden="true">
                    <i class="bi bi-box2-heart-fill"></i>
                </span>
                <div class="users-summary-copy">
                    <span class="users-summary-label">{{ __('dashboard.cotton.total_intake') }}</span>
                    <div class="users-summary-value-wrap">
                        <strong class="users-summary-value inventory-stock-line">
                            <span class="inventory-stock-value">{{ number_format($cottonSummary['total_intake_quantity'], 3) }}</span>
                            <span class="inventory-stock-unit">&nbsp;kg</span>
                        </strong>
                    </div>
                </div>
            </article>
        </div>
    </div>

    <div class="cotton-latest-block">
        <div class="inventory-item-summary-head">
            <div>
                <h4 class="users-summary-title inventory-item-summary-title">{{ __('dashboard.cotton.latest_title') }}</h4>
                <p class="users-summary-text mb-0">{{ __('dashboard.cotton.latest_text') }}</p>
            </div>
        </div>

        @if($latestEntry)
            <article class="cotton-latest-card">
                <div class="cotton-latest-grid">
                    <div class="cotton-latest-item">
                        <span class="cotton-latest-icon" aria-hidden="true">
                            <i class="bi bi-truck"></i>
                        </span>
                        <div class="cotton-latest-copy">
                            <span class="cotton-latest-label">{{ __('dashboard.cotton.truck_number') }}</span>
                            <strong class="cotton-latest-value">{{ $latestEntry->truck_number }}</strong>
                        </div>
                    </div>

                    <div class="cotton-latest-item">
                        <span class="cotton-latest-icon" aria-hidden="true">
                            <i class="bi bi-person-fill"></i>
                        </span>
                        <div class="cotton-latest-copy">
                            <span class="cotton-latest-label">{{ __('dashboard.cotton.driver') }}</span>
                            <strong class="cotton-latest-value">{{ $latestEntry->driver_name }}</strong>
                        </div>
                    </div>

                    <div class="cotton-latest-item">
                        <span class="cotton-latest-icon" aria-hidden="true">
                            <i class="bi bi-box-seam-fill"></i>
                        </span>
                        <div class="cotton-latest-copy">
                            <span class="cotton-latest-label">{{ __('dashboard.cotton.material_category') }}</span>
                            <strong class="cotton-latest-value">{{ $latestEntry->inventoryItem->product_name }}</strong>
                        </div>
                    </div>

                    <div class="cotton-latest-item">
                        <span class="cotton-latest-icon" aria-hidden="true">
                            <i class="bi bi-speedometer2"></i>
                        </span>
                        <div class="cotton-latest-copy">
                            <span class="cotton-latest-label">{{ __('dashboard.cotton.net_weight') }}</span>
                            <strong class="cotton-latest-value cotton-latest-value-inline">
                                <span>{{ number_format((float) $latestEntry->net_weight, 3) }}</span>
                                <span class="cotton-latest-unit">kg</span>
                            </strong>
                        </div>
                    </div>

                    <div class="cotton-latest-item">
                        <span class="cotton-latest-icon" aria-hidden="true">
                            <i class="bi bi-calendar-event-fill"></i>
                        </span>
                        <div class="cotton-latest-copy">
                            <span class="cotton-latest-label">{{ __('dashboard.cotton.entry_date') }}</span>
                            <strong class="cotton-latest-value">{{ $latestEntry->entry_date->format('M d, Y') }}</strong>
                        </div>
                    </div>
                </div>
            </article>
        @else
            <article class="cotton-latest-card cotton-latest-empty">
                <span class="cotton-latest-empty-icon" aria-hidden="true">
                    <i class="bi bi-inbox-fill"></i>
                </span>
                <div>
                    <h4 class="cotton-latest-empty-title">{{ __('dashboard.cotton.empty_title') }}</h4>
                    <p class="users-summary-text mb-0">{{ __('dashboard.cotton.empty_text') }}</p>
                </div>
            </article>
        @endif
    </div>
</section>
