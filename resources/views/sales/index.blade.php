@extends('layouts.app')

@section('title', __('sales.index.title'))
@section('page-title', __('sales.index.page_title'))
@section('page-subtitle', __('sales.index.page_subtitle'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/sales/sales-index.css') }}">
@endpush

@section('content')
    <nav aria-label="breadcrumb" class="breadcrumb-shell mb-4">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ __('sales.index.title') }}</li>
        </ol>
    </nav>

    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="summary-card">
                <div class="summary-card-label">{{ __('sales.summary.invoices') }}</div>
                <div class="summary-card-value">{{ $summary['total_sales'] }}</div>
                <div class="summary-card-meta">{{ __('sales.summary.invoices_text') }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="summary-card">
                <div class="summary-card-label">{{ __('sales.summary.revenue') }}</div>
                <div class="summary-card-value">{{ number_format($summary['total_revenue'], 2) }}</div>
                <div class="summary-card-meta">{{ __('sales.summary.revenue_text') }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="summary-card">
                <div class="summary-card-label">{{ __('sales.summary.paid') }}</div>
                <div class="summary-card-value">{{ number_format($summary['paid_amount'], 2) }}</div>
                <div class="summary-card-meta">{{ __('sales.summary.paid_text') }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="summary-card">
                <div class="summary-card-label">{{ __('sales.summary.pending') }}</div>
                <div class="summary-card-value">{{ number_format($summary['pending_amount'], 2) }}</div>
                <div class="summary-card-meta">{{ __('sales.summary.pending_text') }}</div>
            </div>
        </div>
    </div>

    <div class="content-card mb-4">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-3">
            <div>
                <h2 class="section-title mb-1">{{ __('sales.index.register_title') }}</h2>
                <p class="section-text mb-0">{{ __('sales.index.register_text') }}</p>
            </div>
            @if($canCreateSales)
                <a href="{{ route('sales.create') }}" class="btn btn-primary">{{ __('sales.actions.new') }}</a>
            @endif
        </div>

        <form method="GET" action="{{ route('sales.index') }}" class="filters-form" id="salesFilterForm">
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="customer_id" class="form-label">{{ __('sales.filters.customer') }}</label>
                    <select id="customer_id" name="customer_id" class="form-select">
                        <option value="">{{ __('sales.filters.all_customers') }}</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" @selected($filters['customer_id'] === (string) $customer->id)>{{ $customer->full_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="date_from" class="form-label">{{ __('sales.filters.from') }}</label>
                    <input type="date" id="date_from" name="date_from" value="{{ $filters['date_from'] }}" class="form-control">
                </div>
                <div class="col-md-3">
                    <label for="date_to" class="form-label">{{ __('sales.filters.to') }}</label>
                    <input type="date" id="date_to" name="date_to" value="{{ $filters['date_to'] }}" class="form-control">
                </div>
                <div class="col-md-2">
                    <label for="sort" class="form-label">{{ __('sales.filters.sort') }}</label>
                    <select id="sort" name="sort" class="form-select">
                        <option value="latest" @selected($filters['sort'] === 'latest')>{{ __('sales.filters.latest') }}</option>
                        <option value="oldest" @selected($filters['sort'] === 'oldest')>{{ __('sales.filters.oldest') }}</option>
                    </select>
                </div>
            </div>

            <div class="d-flex flex-wrap gap-2 mt-3">
                <button type="submit" class="btn btn-primary">{{ __('sales.actions.apply_filters') }}</button>
                <button type="button" class="btn btn-outline-secondary" data-reset-filters="#salesFilterForm">{{ __('sales.actions.reset') }}</button>
            </div>
        </form>
    </div>

    <div class="content-card">
        <div class="table-responsive">
            <table class="table admin-table align-middle mb-0">
                <thead>
                    <tr>
                        <th>{{ __('sales.table.invoice') }}</th>
                        <th>{{ __('sales.table.customer') }}</th>
                        <th>{{ __('sales.table.date') }}</th>
                        <th>{{ __('sales.table.total') }}</th>
                        <th>{{ __('sales.table.paid') }}</th>
                        <th>{{ __('sales.table.remaining') }}</th>
                        <th>{{ __('sales.table.status') }}</th>
                        <th class="text-end">{{ __('sales.table.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sales as $sale)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ __('sales.messages.number', ['id' => $sale->id]) }}</div>
                                <div class="text-muted small">{{ __('sales.messages.handled_by', ['name' => $sale->seller->name]) }}</div>
                            </td>
                            <td>{{ $sale->customer->full_name }}</td>
                            <td>{{ $sale->sale_date->format('M d, Y') }}</td>
                            <td>{{ number_format((float) $sale->total_amount, 2) }}</td>
                            <td>{{ number_format((float) $sale->paid_amount, 2) }}</td>
                            <td>{{ number_format((float) $sale->remaining_amount, 2) }}</td>
                            <td>@include('sales.partials.payment-status-badge', ['sale' => $sale])</td>
                            <td class="text-end">
                                <div class="table-actions">
                                    <a href="{{ route('sales.show', $sale) }}" class="btn btn-sm btn-outline-primary">{{ __('sales.actions.view') }}</a>
                                    @if($canManageSales)
                                        <a href="{{ route('sales.edit', $sale) }}" class="btn btn-sm btn-outline-secondary">{{ __('sales.actions.edit') }}</a>
                                        @if((float) $sale->remaining_amount > 0)
                                            <a href="{{ route('sale-payments.create', $sale) }}" class="btn btn-sm btn-outline-success">{{ __('sales.actions.collect') }}</a>
                                        @endif
                                    @endif
                                    @if($canDeleteSales)
                                        <form method="POST" action="{{ route('sales.destroy', $sale) }}" data-confirm="{{ __('sales.messages.delete_confirm', ['id' => $sale->id]) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" @disabled(! $sale->canBeDeletedSafely())>{{ __('sales.actions.delete') }}</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">{{ __('sales.messages.empty') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $sales->links() }}
        </div>
    </div>
@endsection
