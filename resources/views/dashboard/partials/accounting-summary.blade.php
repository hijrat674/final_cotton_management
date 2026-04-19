@php
    $netProfit = (float) ($accountingSummary['net_profit'] ?? 0);
    $isNetPositive = $netProfit >= 0;
@endphp

<section class="summary-showcase accounting-summary-section content-card mt-4">
    <div class="users-summary-header">
        <div>
            <h3 class="users-summary-title">{{ __('dashboard.accounting.title') }}</h3>
            <p class="users-summary-text mb-0">{{ __('dashboard.accounting.text') }}</p>
        </div>
        <a href="{{ route('accounting.dashboard') }}" class="btn btn-outline-primary users-summary-action">
            {{ __('dashboard.accounting.open') }}
        </a>
    </div>

    <div class="row g-4 users-summary-grid">
        <div class="col-sm-6 col-xl-3">
            <article class="users-summary-card accounting-summary-card-income">
                <span class="users-summary-icon" aria-hidden="true">
                    <i class="bi bi-arrow-up-right-circle-fill"></i>
                </span>
                <div class="users-summary-copy">
                    <span class="users-summary-label">{{ __('dashboard.accounting.income') }}</span>
                    <div class="users-summary-value-wrap">
                        <div class="accounting-summary-value-block">
                            <strong class="accounting-summary-amount">{{ number_format($accountingSummary['total_revenue'], 2) }}</strong>
                            <span class="accounting-summary-currency">AFG</span>
                        </div>
                    </div>
                </div>
            </article>
        </div>

        <div class="col-sm-6 col-xl-3">
            <article class="users-summary-card accounting-summary-card-expenses">
                <span class="users-summary-icon" aria-hidden="true">
                    <i class="bi bi-receipt-cutoff"></i>
                </span>
                <div class="users-summary-copy">
                    <span class="users-summary-label">{{ __('dashboard.accounting.expenses') }}</span>
                    <div class="users-summary-value-wrap">
                        <div class="accounting-summary-value-block">
                            <strong class="accounting-summary-amount">{{ number_format($accountingSummary['total_expenses'], 2) }}</strong>
                            <span class="accounting-summary-currency">AFG</span>
                        </div>
                    </div>
                </div>
            </article>
        </div>

        <div class="col-sm-6 col-xl-3">
            <article class="users-summary-card {{ $isNetPositive ? 'accounting-summary-card-profit' : 'accounting-summary-card-loss' }}">
                <span class="users-summary-icon" aria-hidden="true">
                    <i class="bi {{ $isNetPositive ? 'bi-graph-up-arrow' : 'bi-graph-down-arrow' }}"></i>
                </span>
                <div class="users-summary-copy">
                    <span class="users-summary-label">{{ $isNetPositive ? __('dashboard.accounting.net_profit') : __('dashboard.accounting.net_loss') }}</span>
                    <div class="users-summary-value-wrap">
                        <div class="accounting-summary-value-block">
                            <strong class="accounting-summary-amount">{{ number_format($accountingSummary['net_profit'], 2) }}</strong>
                            <span class="accounting-summary-currency">AFG</span>
                        </div>
                    </div>
                </div>
            </article>
        </div>

        <div class="col-sm-6 col-xl-3">
            <article class="users-summary-card accounting-summary-card-cash">
                <span class="users-summary-icon" aria-hidden="true">
                    <i class="bi bi-bank2"></i>
                </span>
                <div class="users-summary-copy">
                    <span class="users-summary-label">{{ __('dashboard.accounting.cash_balance') }}</span>
                    <div class="users-summary-value-wrap">
                        <div class="accounting-summary-value-block">
                            <strong class="accounting-summary-amount">{{ number_format($accountingSummary['cash_balance'], 2) }}</strong>
                            <span class="accounting-summary-currency">AFG</span>
                        </div>
                    </div>
                </div>
            </article>
        </div>
    </div>
</section>
