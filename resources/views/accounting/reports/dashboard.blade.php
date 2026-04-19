@extends('layouts.app')

@section('title', __('accounting.dashboard_title'))
@section('page-title', __('accounting.dashboard_title'))
@section('page-subtitle', __('accounting.dashboard_subtitle'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/accounting/accounting-dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/accounting/reports.css') }}">
@endpush

@section('content')
    <nav aria-label="breadcrumb" class="breadcrumb-shell mb-4">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ __('accounting.module') }}</li>
        </ol>
    </nav>

    @include('accounting.reports.partials.nav')

    <div class="content-card mb-4">
        <form method="GET" action="{{ route('accounting.dashboard') }}" class="filters-form" id="accountingDashboardFilters">
            <div class="row g-3">
                <div class="col-md-5">
                    <label for="date_from" class="form-label">{{ __('accounting.from') }}</label>
                    <input type="date" id="date_from" name="date_from" value="{{ $filters['date_from'] ?? '' }}" class="form-control">
                </div>
                <div class="col-md-5">
                    <label for="date_to" class="form-label">{{ __('accounting.to') }}</label>
                    <input type="date" id="date_to" name="date_to" value="{{ $filters['date_to'] ?? '' }}" class="form-control">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">{{ __('accounting.apply') }}</button>
                </div>
            </div>
        </form>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-6 col-xl-3">
            <div class="summary-card">
                <div class="summary-card-label">{{ __('accounting.revenue') }}</div>
                <div class="summary-card-value">{{ number_format($summary['total_revenue'], 2) }}</div>
                <div class="summary-card-meta">{{ __('accounting.auto_posted_income') }}</div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="summary-card">
                <div class="summary-card-label">{{ __('accounting.expenses') }}</div>
                <div class="summary-card-value">{{ number_format($summary['total_expenses'], 2) }}</div>
                <div class="summary-card-meta">{{ __('accounting.auto_posted_expenses') }}</div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="summary-card">
                <div class="summary-card-label">{{ __('accounting.net_profit') }}</div>
                <div class="summary-card-value">{{ number_format($summary['net_profit'], 2) }}</div>
                <div class="summary-card-meta">{{ __('accounting.financial_result') }}</div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="summary-card">
                <div class="summary-card-label">{{ __('accounting.cash_balance') }}</div>
                <div class="summary-card-value">{{ number_format($summary['cash_balance'], 2) }}</div>
                <div class="summary-card-meta">{{ __('accounting.ledger_cash_position') }}</div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-6">
            <div class="content-card chart-card">
                <h2 class="section-title mb-3">{{ __('accounting.revenue_vs_expense') }}</h2>
                <canvas id="revenueExpenseChart"></canvas>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="content-card chart-card">
                <h2 class="section-title mb-3">{{ __('accounting.monthly_profit_trend') }}</h2>
                <canvas id="profitTrendChart"></canvas>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="content-card chart-card">
                <h2 class="section-title mb-3">{{ __('accounting.expense_distribution') }}</h2>
                <canvas id="expenseDistributionChart"></canvas>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="content-card chart-card">
                <h2 class="section-title mb-3">{{ __('accounting.cash_flow') }}</h2>
                <canvas id="cashFlowChart"></canvas>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
    <script>
        const revenueVsExpense = @json($summary['revenue_vs_expense']);
        const monthlyProfit = @json($summary['monthly_profit']);
        const expenseDistribution = @json($summary['expense_distribution']);
        const cashFlow = @json($summary['cash_flow']);

        new Chart(document.getElementById('revenueExpenseChart'), {
            type: 'bar',
            data: {
                labels: revenueVsExpense.map(item => item.label),
                datasets: [{
                    label: @json(__('accounting.amount')),
                    data: revenueVsExpense.map(item => item.amount),
                    backgroundColor: ['#2563eb', '#f97316']
                }]
            }
        });

        new Chart(document.getElementById('profitTrendChart'), {
            type: 'line',
            data: {
                labels: monthlyProfit.map(item => item.label),
                datasets: [{
                    label: @json(__('accounting.profit')),
                    data: monthlyProfit.map(item => item.profit),
                    borderColor: '#16a34a',
                    backgroundColor: 'rgba(22, 163, 74, 0.15)',
                    tension: 0.3,
                    fill: true
                }]
            }
        });

        new Chart(document.getElementById('expenseDistributionChart'), {
            type: 'pie',
            data: {
                labels: expenseDistribution.map(item => item.label),
                datasets: [{
                    data: expenseDistribution.map(item => item.amount),
                    backgroundColor: ['#f97316', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4']
                }]
            }
        });

        new Chart(document.getElementById('cashFlowChart'), {
            type: 'bar',
            data: {
                labels: cashFlow.map(item => item.label),
                datasets: [
                    {
                        label: @json(__('accounting.cash_in')),
                        data: cashFlow.map(item => item.cash_in),
                        backgroundColor: '#0f9d58'
                    },
                    {
                        label: @json(__('accounting.cash_out')),
                        data: cashFlow.map(item => item.cash_out),
                        backgroundColor: '#dc2626'
                    }
                ]
            }
        });
    </script>
@endpush
