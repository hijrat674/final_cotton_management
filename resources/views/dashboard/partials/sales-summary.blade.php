<section class="summary-showcase sales-summary-section content-card mt-4">
    <div class="users-summary-header">
        <div>
            <h3 class="users-summary-title">{{ __('dashboard.sales.title') }}</h3>
            <p class="users-summary-text mb-0">{{ __('dashboard.sales.text') }}</p>
        </div>
        <a href="{{ route('sales.index') }}" class="btn btn-outline-primary users-summary-action">
            {{ __('dashboard.sales.open') }}
        </a>
    </div>

    <div class="row g-4 users-summary-grid mb-4">
        <div class="col-sm-6 col-xl-4">
            <article class="users-summary-card sales-summary-card-total">
                <span class="users-summary-icon" aria-hidden="true">
                    <i class="bi bi-cart-check-fill"></i>
                </span>
                <div class="users-summary-copy">
                    <span class="users-summary-label">{{ __('dashboard.sales.total_sales') }}</span>
                    <div class="users-summary-value-wrap">
                        <strong class="users-summary-value">{{ $salesSummary['total_sales_count'] }}</strong>
                    </div>
                </div>
            </article>
        </div>

        <div class="col-sm-6 col-xl-4">
            <article class="users-summary-card sales-summary-card-today">
                <span class="users-summary-icon" aria-hidden="true">
                    <i class="bi bi-calendar2-check-fill"></i>
                </span>
                <div class="users-summary-copy">
                    <span class="users-summary-label">{{ __('dashboard.sales.today_sales') }}</span>
                    <div class="users-summary-value-wrap">
                        <strong class="users-summary-value">{{ $salesSummary['today_sales'] }}</strong>
                    </div>
                </div>
            </article>
        </div>

        <div class="col-sm-6 col-xl-4">
            <article class="users-summary-card sales-summary-card-revenue">
                <span class="users-summary-icon" aria-hidden="true">
                    <i class="bi bi-wallet2"></i>
                </span>
                <div class="users-summary-copy">
                    <span class="users-summary-label">{{ __('dashboard.sales.total_revenue') }}</span>
                    <div class="users-summary-value-wrap">
                        <strong class="users-summary-value sales-summary-line">
                            <span class="sales-summary-value">{{ number_format($salesSummary['total_revenue'], 2) }}</span>
                            <span class="sales-summary-unit">AFN</span>
                        </strong>
                    </div>
                </div>
            </article>
        </div>

        <div class="col-sm-6 col-xl-4">
            <article class="users-summary-card sales-summary-card-paid">
                <span class="users-summary-icon" aria-hidden="true">
                    <i class="bi bi-check-circle-fill"></i>
                </span>
                <div class="users-summary-copy">
                    <span class="users-summary-label">{{ __('dashboard.sales.paid_amount') }}</span>
                    <div class="users-summary-value-wrap">
                        <strong class="users-summary-value sales-summary-line">
                            <span class="sales-summary-value">{{ number_format($salesSummary['paid_amount'], 2) }}</span>
                            <span class="sales-summary-unit">AFN</span>
                        </strong>
                    </div>
                </div>
            </article>
        </div>

        <div class="col-sm-6 col-xl-4">
            <article class="users-summary-card sales-summary-card-pending">
                <span class="users-summary-icon" aria-hidden="true">
                    <i class="bi bi-clock-history"></i>
                </span>
                <div class="users-summary-copy">
                    <span class="users-summary-label">{{ __('dashboard.sales.pending_amount') }}</span>
                    <div class="users-summary-value-wrap">
                        <strong class="users-summary-value sales-summary-line">
                            <span class="sales-summary-value">{{ number_format($salesSummary['pending_amount'], 2) }}</span>
                            <span class="sales-summary-unit">AFN</span>
                        </strong>
                    </div>
                </div>
            </article>
        </div>

        <div class="col-sm-6 col-xl-4">
            <article class="users-summary-card sales-summary-card-open">
                <span class="users-summary-icon" aria-hidden="true">
                    <i class="bi bi-receipt-cutoff"></i>
                </span>
                <div class="users-summary-copy">
                    <span class="users-summary-label">{{ __('dashboard.sales.open_invoices') }}</span>
                    <div class="users-summary-value-wrap">
                        <strong class="users-summary-value">{{ $salesSummary['open_invoices_count'] }}</strong>
                    </div>
                </div>
            </article>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="content-card h-100">
                <h3 class="section-title mb-3">{{ __('dashboard.sales.daily_trend') }}</h3>
                <div class="d-grid gap-3">
                    @forelse($salesSummary['daily_trend'] as $point)
                        <div class="inventory-distribution-card">
                            <span>{{ $point['date'] }}</span>
                            <strong>{{ number_format($point['amount'], 2) }}</strong>
                        </div>
                    @empty
                        <p class="text-muted mb-0">{{ __('dashboard.sales.daily_trend_empty') }}</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="content-card h-100">
                <h3 class="section-title mb-3">{{ __('dashboard.sales.top_products') }}</h3>
                <div class="d-grid gap-3">
                    @forelse($salesSummary['top_products'] as $product)
                        <div class="inventory-distribution-card">
                            <span>{{ $product['name'] }}</span>
                            <strong>{{ number_format($product['quantity'], 3) }}</strong>
                        </div>
                    @empty
                        <p class="text-muted mb-0">{{ __('dashboard.sales.top_products_empty') }}</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</section>
