<section class="summary-showcase expenses-summary-section content-card mt-4">
    <div class="users-summary-header">
        <div>
            <h3 class="users-summary-title">{{ __('dashboard.expenses.title') }}</h3>
            <p class="users-summary-text mb-0">{{ __('dashboard.expenses.text') }}</p>
        </div>
        <a href="{{ route('expenses.index') }}" class="btn btn-outline-primary users-summary-action">
            {{ __('dashboard.expenses.open') }}
        </a>
    </div>

    <div class="row g-4 users-summary-grid">
        <div class="col-sm-6 col-xl-3">
            <article class="users-summary-card expenses-summary-card-count">
                <span class="users-summary-icon" aria-hidden="true">
                    <i class="bi bi-folder2-open"></i>
                </span>
                <div class="users-summary-copy">
                    <span class="users-summary-label">{{ __('dashboard.expenses.total_count') }}</span>
                    <div class="users-summary-value-wrap">
                        <strong class="users-summary-value">{{ $expenseSummary['total_expenses_count'] }}</strong>
                    </div>
                </div>
            </article>
        </div>

        <div class="col-sm-6 col-xl-3">
            <article class="users-summary-card expenses-summary-card-total">
                <span class="users-summary-icon" aria-hidden="true">
                    <i class="bi bi-wallet2"></i>
                </span>
                <div class="users-summary-copy">
                    <span class="users-summary-label">{{ __('dashboard.expenses.total_amount') }}</span>
                    <div class="users-summary-value-wrap">
                        <strong class="users-summary-value expense-summary-line">
                            <span class="expense-summary-value">{{ number_format($expenseSummary['total_expenses_amount'], 2) }}</span>
                        </strong>
                    </div>
                </div>
            </article>
        </div>

        <div class="col-sm-6 col-xl-3">
            <article class="users-summary-card expenses-summary-card-today">
                <span class="users-summary-icon" aria-hidden="true">
                    <i class="bi bi-calendar-date"></i>
                </span>
                <div class="users-summary-copy">
                    <span class="users-summary-label">{{ __('dashboard.expenses.today') }}</span>
                    <div class="users-summary-value-wrap">
                        <strong class="users-summary-value expense-summary-line">
                            <span class="expense-summary-value">{{ number_format($expenseSummary['today_expenses_amount'], 2) }}</span>
                        </strong>
                    </div>
                </div>
            </article>
        </div>

        <div class="col-sm-6 col-xl-3">
            <article class="users-summary-card expenses-summary-card-month">
                <span class="users-summary-icon" aria-hidden="true">
                    <i class="bi bi-calendar3"></i>
                </span>
                <div class="users-summary-copy">
                    <span class="users-summary-label">{{ __('dashboard.expenses.month') }}</span>
                    <div class="users-summary-value-wrap">
                        <strong class="users-summary-value expense-summary-line">
                            <span class="expense-summary-value">{{ number_format($expenseSummary['month_expenses_amount'], 2) }}</span>
                        </strong>
                    </div>
                </div>
            </article>
        </div>
    </div>

    @if(!empty($expenseSummary['distribution']))
        <div class="inventory-item-summary-head">
            <div>
                <h4 class="users-summary-title inventory-item-summary-title">{{ __('dashboard.expenses.categories_title') }}</h4>
                <p class="users-summary-text mb-0">{{ __('dashboard.expenses.categories_text') }}</p>
            </div>
        </div>

        <div class="row g-4 users-summary-grid mt-1">
            @foreach($expenseSummary['distribution'] as $distribution)
                <div class="col-sm-6 col-xl-4">
                    <article class="users-summary-card expense-distribution-card">
                        <span class="users-summary-icon expense-distribution-icon" aria-hidden="true">
                            <i class="bi bi-receipt-cutoff"></i>
                        </span>
                        <div class="users-summary-copy">
                            <span class="users-summary-label expense-distribution-label">{{ $distribution['label'] }}</span>
                            <div class="users-summary-value-wrap">
                                <strong class="users-summary-value">{{ $distribution['count'] }}</strong>
                            </div>
                        </div>
                    </article>
                </div>
            @endforeach
        </div>
    @endif
</section>
