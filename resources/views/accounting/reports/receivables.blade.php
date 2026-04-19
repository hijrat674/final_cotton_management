@extends('layouts.app')

@section('title', __('accounting.customer_receivables'))
@section('page-title', __('accounting.customer_receivables'))
@section('page-subtitle', __('accounting.customer_receivables_subtitle'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/accounting/reports.css') }}">
@endpush

@section('content')
    @include('accounting.reports.partials.nav')

    <div class="row g-4 mb-4">
        <div class="col-md-6 col-xl-4">
            <div class="summary-card">
                <div class="summary-card-label">{{ __('accounting.total_receivables') }}</div>
                <div class="summary-card-value">{{ number_format($report['total_receivables'], 2) }}</div>
                <div class="summary-card-meta">{{ __('accounting.open_customer_balances') }}</div>
            </div>
        </div>
    </div>

    <div class="content-card">
        <div class="table-responsive">
            <table class="table admin-table mb-0">
                <thead>
                    <tr>
                        <th>{{ __('accounting.customer') }}</th>
                        <th>{{ __('accounting.phone') }}</th>
                        <th>{{ __('accounting.sales_count') }}</th>
                        <th class="text-end">{{ __('accounting.outstanding') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($report['customers'] as $customer)
                        <tr>
                            <td><a href="{{ route('customers.show', $customer) }}" class="text-decoration-none fw-semibold">{{ $customer->full_name }}</a></td>
                            <td>{{ $customer->phone }}</td>
                            <td>{{ $customer->sales_count }}</td>
                            <td class="text-end">{{ number_format($customer->outstanding_balance, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center py-4 text-muted">{{ __('accounting.no_outstanding_customer_balances') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
