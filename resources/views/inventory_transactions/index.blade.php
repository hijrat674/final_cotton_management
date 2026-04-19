@extends('layouts.app')

@section('title', __('inventory.transactions.title'))
@section('page-title', __('inventory.transactions.page_title'))
@section('page-subtitle', __('inventory.transactions.page_subtitle'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/inventory/inventory-transactions-index.css') }}">
@endpush

@section('content')
    <nav aria-label="breadcrumb" class="breadcrumb-shell mb-4">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ __('inventory.transactions.title') }}</li>
        </ol>
    </nav>

    <div class="content-card mb-4">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-3">
            <div>
                <h2 class="section-title mb-1">{{ __('inventory.transactions.ledger_title') }}</h2>
                <p class="section-text mb-0">{{ __('inventory.transactions.ledger_text') }}</p>
            </div>
            @if($canCreateTransactions)
                <a href="{{ route('inventory-transactions.create') }}" class="btn btn-primary">{{ __('inventory.transactions.actions.record') }}</a>
            @endif
        </div>

        <form method="GET" action="{{ route('inventory-transactions.index') }}" class="filters-form" id="inventoryTransactionsFilterForm">
            <div class="row g-3">
                <div class="col-md-6 col-xl-3">
                    <label for="transaction_type" class="form-label">{{ __('inventory.transactions.filters.transaction_type') }}</label>
                    <select id="transaction_type" name="transaction_type" class="form-select">
                        <option value="">{{ __('inventory.transactions.filters.all_types') }}</option>
                        @foreach($transactionTypes as $value => $label)
                            <option value="{{ $value }}" @selected($filters['transaction_type'] === $value)>{{ __($label) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 col-xl-3">
                    <label for="inventory_item_id" class="form-label">{{ __('inventory.transactions.filters.inventory_item') }}</label>
                    <select id="inventory_item_id" name="inventory_item_id" class="form-select">
                        <option value="">{{ __('inventory.transactions.filters.all_items') }}</option>
                        @foreach($items as $item)
                            <option value="{{ $item->id }}" @selected($filters['inventory_item_id'] === (string) $item->id)>{{ $item->product_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 col-xl-2">
                    <label for="date_from" class="form-label">{{ __('inventory.transactions.filters.from') }}</label>
                    <input type="date" id="date_from" name="date_from" value="{{ $filters['date_from'] }}" class="form-control">
                </div>
                <div class="col-md-6 col-xl-2">
                    <label for="date_to" class="form-label">{{ __('inventory.transactions.filters.to') }}</label>
                    <input type="date" id="date_to" name="date_to" value="{{ $filters['date_to'] }}" class="form-control">
                </div>
                <div class="col-md-6 col-xl-2">
                    <label for="sort" class="form-label">{{ __('inventory.transactions.filters.sort') }}</label>
                    <select id="sort" name="sort" class="form-select">
                        <option value="latest" @selected($filters['sort'] === 'latest')>{{ __('inventory.transactions.filters.latest') }}</option>
                        <option value="oldest" @selected($filters['sort'] === 'oldest')>{{ __('inventory.transactions.filters.oldest') }}</option>
                    </select>
                </div>
            </div>

            <div class="d-flex flex-wrap gap-2 mt-3">
                <button type="submit" class="btn btn-primary">{{ __('inventory.transactions.actions.apply_filters') }}</button>
                <button type="button" class="btn btn-outline-secondary" data-reset-filters="#inventoryTransactionsFilterForm">{{ __('inventory.transactions.actions.reset') }}</button>
            </div>
        </form>
    </div>

    <div class="content-card">
        <div class="table-responsive">
            <table class="table admin-table align-middle mb-0">
                <thead>
                    <tr>
                        <th>{{ __('inventory.transactions.table.date') }}</th>
                        <th>{{ __('inventory.transactions.table.item') }}</th>
                        <th>{{ __('inventory.transactions.table.type') }}</th>
                        <th>{{ __('inventory.transactions.table.in') }}</th>
                        <th>{{ __('inventory.transactions.table.out') }}</th>
                        <th>{{ __('inventory.transactions.table.reference') }}</th>
                        <th>{{ __('inventory.transactions.table.created_by') }}</th>
                        <th class="text-end">{{ __('inventory.transactions.table.action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $transaction)
                        <tr>
                            <td>{{ $transaction->transaction_date->format('M d, Y') }}</td>
                            <td class="fw-semibold">{{ $transaction->inventoryItem->product_name }}</td>
                            <td>{{ __($transactionTypes[$transaction->transaction_type] ?? ucfirst(str_replace('_', ' ', $transaction->transaction_type))) }}</td>
                            <td>{{ number_format((float) $transaction->quantity_in, 3) }}</td>
                            <td>{{ number_format((float) $transaction->quantity_out, 3) }}</td>
                            <td>{{ $transaction->reference_type ? __(ucfirst(str_replace('_', ' ', $transaction->reference_type)).' #:id', ['id' => $transaction->reference_id]) : __('inventory.transactions.messages.no_reference') }}</td>
                            <td>{{ $transaction->creator->name }}</td>
                            <td class="text-end">
                                <a href="{{ route('inventory-transactions.show', $transaction) }}" class="btn btn-sm btn-outline-primary">{{ __('inventory.transactions.actions.view') }}</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">{{ __('inventory.transactions.messages.empty') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $transactions->links() }}
        </div>
    </div>
@endsection
