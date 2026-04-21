@extends('layouts.app')

@section('title', __('customers.index.title'))
@section('page-title', __('customers.index.page_title'))
@section('page-subtitle', __('customers.index.page_subtitle'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/customers/customers-index.css') }}">
@endpush

@section('content')
    <nav aria-label="breadcrumb" class="breadcrumb-shell mb-4">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ __('customers.index.title') }}</li>
        </ol>
    </nav>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="summary-card">
                <div class="summary-card-label">{{ __('customers.summary.total_customers') }}</div>
                <div class="summary-card-value">{{ $summary['total_customers'] }}</div>
                <div class="summary-card-meta">{{ __('customers.summary.total_customers_text') }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="summary-card">
                <div class="summary-card-label">{{ __('customers.summary.customers_with_balance') }}</div>
                <div class="summary-card-value">{{ $summary['customers_with_balance'] }}</div>
                <div class="summary-card-meta">{{ __('customers.summary.customers_with_balance_text') }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="summary-card">
                <div class="summary-card-label">{{ __('customers.summary.outstanding_balance') }}</div>
                <div class="summary-card-value">{{ number_format($summary['total_outstanding'], 2) }}</div>
                <div class="summary-card-meta">{{ __('customers.summary.outstanding_balance_text') }}</div>
            </div>
        </div>
    </div>

    <div class="content-card mb-4">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-3">
            <div>
                <h2 class="section-title mb-1">{{ __('customers.index.directory_title') }}</h2>
                <p class="section-text mb-0">{{ __('customers.index.directory_text') }}</p>
            </div>
            @if($canManageCustomers)
                <a
                    href="{{ route('customers.create') }}"
                    class="btn btn-primary"
                    data-modal-open
                    data-modal-size="xl"
                    data-modal-title="{{ __('customers.actions.new') }}"
                >{{ __('customers.actions.new') }}</a>
            @endif
        </div>

        <form method="GET" action="{{ route('customers.index') }}" class="filters-form" id="customersFilterForm">
            <div class="row g-3">
                <div class="col-md-5">
                    <label for="name" class="form-label">{{ __('customers.filters.customer_name') }}</label>
                    <input type="text" id="name" name="name" value="{{ $filters['name'] }}" class="form-control" placeholder="{{ __('customers.filters.search_name') }}">
                </div>
                <div class="col-md-5">
                    <label for="phone" class="form-label">{{ __('customers.filters.phone_number') }}</label>
                    <input type="text" id="phone" name="phone" value="{{ $filters['phone'] }}" class="form-control" placeholder="{{ __('customers.filters.search_phone') }}">
                </div>
                <div class="col-md-2">
                    <label for="sort" class="form-label">{{ __('customers.filters.sort') }}</label>
                    <select id="sort" name="sort" class="form-select">
                        <option value="latest" @selected($filters['sort'] === 'latest')>{{ __('customers.filters.latest') }}</option>
                        <option value="oldest" @selected($filters['sort'] === 'oldest')>{{ __('customers.filters.oldest') }}</option>
                    </select>
                </div>
            </div>

            <div class="d-flex flex-wrap gap-2 mt-3">
                <button type="submit" class="btn btn-primary">{{ __('customers.actions.apply_filters') }}</button>
                <button type="button" class="btn btn-outline-secondary" data-reset-filters="#customersFilterForm">{{ __('customers.actions.reset') }}</button>
            </div>
        </form>
    </div>

    <div class="content-card">
        <div class="table-responsive">
            <table class="table admin-table align-middle mb-0">
                <thead>
                    <tr>
                        <th>{{ __('customers.table.customer') }}</th>
                        <th>{{ __('customers.table.phone') }}</th>
                        <th>{{ __('customers.table.address') }}</th>
                        <th>{{ __('customers.table.sales') }}</th>
                        <th>{{ __('customers.table.outstanding') }}</th>
                        <th class="text-end">{{ __('customers.table.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $customer)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $customer->full_name }}</div>
                                <div class="text-muted small">{{ __('customers.messages.number', ['id' => $customer->id]) }}</div>
                            </td>
                            <td>{{ $customer->phone }}</td>
                            <td>{{ $customer->address ?: __('customers.messages.no_address') }}</td>
                            <td>{{ $customer->sales_count }}</td>
                            <td class="fw-semibold {{ $customer->outstanding_balance > 0 ? 'text-warning-emphasis' : 'text-success' }}">
                                {{ number_format($customer->outstanding_balance, 2) }}
                            </td>
                            <td class="text-end">
                                <div class="table-actions">
                                    <a
                                        href="{{ route('customers.show', $customer) }}"
                                        class="btn btn-sm btn-outline-primary"
                                        data-modal-open
                                        data-modal-size="lg"
                                        data-modal-title="{{ __('customers.actions.view') }}"
                                    >{{ __('customers.actions.view') }}</a>
                                    @if($canManageCustomers)
                                        <a
                                            href="{{ route('customers.edit', $customer) }}"
                                            class="btn btn-sm btn-outline-secondary"
                                            data-modal-open
                                            data-modal-size="xl"
                                            data-modal-title="{{ __('customers.actions.edit') }}"
                                        >{{ __('customers.actions.edit') }}</a>
                                    @endif
                                    @if($canDeleteCustomers)
                                        <form method="POST" action="{{ route('customers.destroy', $customer) }}" data-confirm="{{ __('customers.messages.delete_confirm', ['name' => $customer->full_name]) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" @disabled($customer->sales_count > 0)>{{ __('customers.actions.delete') }}</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">{{ __('customers.messages.empty') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $customers->links() }}
        </div>
    </div>
@endsection
