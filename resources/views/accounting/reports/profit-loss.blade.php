@extends('layouts.app')

@section('title', __('accounting.profit_loss'))
@section('page-title', __('accounting.profit_loss'))
@section('page-subtitle', __('accounting.profit_loss_subtitle'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/accounting/reports.css') }}">
@endpush

@section('content')
    @include('accounting.reports.partials.nav')

    <div class="row g-4 mb-4">
        <div class="col-md-4"><div class="summary-card"><div class="summary-card-label">{{ __('accounting.total_revenue') }}</div><div class="summary-card-value">{{ number_format($report['total_revenue'], 2) }}</div></div></div>
        <div class="col-md-4"><div class="summary-card"><div class="summary-card-label">{{ __('accounting.total_expenses') }}</div><div class="summary-card-value">{{ number_format($report['total_expenses'], 2) }}</div></div></div>
        <div class="col-md-4"><div class="summary-card"><div class="summary-card-label">{{ __('accounting.net_profit') }}</div><div class="summary-card-value">{{ number_format($report['net_profit'], 2) }}</div></div></div>
    </div>

    <div class="row g-4">
        <div class="col-xl-6">
            <div class="content-card">
                <h2 class="section-title mb-3">{{ __('accounting.revenue_accounts') }}</h2>
                <div class="table-responsive">
                    <table class="table admin-table mb-0">
                        <thead><tr><th>{{ __('accounting.account') }}</th><th class="text-end">{{ __('accounting.amount') }}</th></tr></thead>
                        <tbody>
                            @foreach($report['revenue_accounts'] as $row)
                                <tr><td>{{ $row['account']->account_name }}</td><td class="text-end">{{ number_format($row['amount'], 2) }}</td></tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="content-card">
                <h2 class="section-title mb-3">{{ __('accounting.expense_accounts') }}</h2>
                <div class="table-responsive">
                    <table class="table admin-table mb-0">
                        <thead><tr><th>{{ __('accounting.account') }}</th><th class="text-end">{{ __('accounting.amount') }}</th></tr></thead>
                        <tbody>
                            @foreach($report['expense_accounts'] as $row)
                                <tr><td>{{ $row['account']->account_name }}</td><td class="text-end">{{ number_format($row['amount'], 2) }}</td></tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
