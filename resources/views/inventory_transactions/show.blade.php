@extends('layouts.app')

@section('title', __('Inventory Transaction Details'))
@section('page-title', __('Inventory Transaction Details'))
@section('page-subtitle', __('Review stock movement details and ledger context'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/inventory/inventory-transactions-show.css') }}">
@endpush

@section('content')
    <nav aria-label="breadcrumb" class="breadcrumb-shell mb-4">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('inventory-transactions.index') }}">Inventory Transactions</a></li>
            <li class="breadcrumb-item active" aria-current="page">Transaction #{{ $transaction->id }}</li>
        </ol>
    </nav>

    <div class="row g-4">
        <div class="col-xl-8">
            <div class="content-card detail-card">
                <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 mb-4">
                    <div>
                        <h2 class="section-title mb-1">Transaction #{{ $transaction->id }}</h2>
                        <p class="section-text mb-0">{{ $transaction->transaction_date->format('M d, Y') }}</p>
                    </div>
                    <div class="transaction-net {{ $transaction->isStockIn() ? 'is-in' : 'is-out' }}">
                        {{ $transaction->isStockIn() ? '+' : '-' }}{{ number_format(abs($transaction->net_quantity), 3) }}
                    </div>
                </div>

                <div class="detail-grid">
                    <div>
                        <span class="detail-label">Inventory Item</span>
                        <span class="detail-value">{{ $transaction->inventoryItem->product_name }}</span>
                    </div>
                    <div>
                        <span class="detail-label">Transaction Type</span>
                        <span class="detail-value">{{ \App\Models\InventoryTransaction::transactionTypeOptions()[$transaction->transaction_type] ?? ucfirst(str_replace('_', ' ', $transaction->transaction_type)) }}</span>
                    </div>
                    <div>
                        <span class="detail-label">Quantity In</span>
                        <span class="detail-value">{{ number_format((float) $transaction->quantity_in, 3) }}</span>
                    </div>
                    <div>
                        <span class="detail-label">Quantity Out</span>
                        <span class="detail-value">{{ number_format((float) $transaction->quantity_out, 3) }}</span>
                    </div>
                    <div>
                        <span class="detail-label">Reference</span>
                        <span class="detail-value">{{ $transaction->reference_type ? ucfirst(str_replace('_', ' ', $transaction->reference_type)).' #'.$transaction->reference_id : 'No reference' }}</span>
                    </div>
                    <div>
                        <span class="detail-label">Created By</span>
                        <span class="detail-value">{{ $transaction->creator->name }}</span>
                    </div>
                </div>

                <div class="notes-panel mt-4">
                    <span class="detail-label">Notes</span>
                    <p class="mb-0">{{ $transaction->notes ?: 'No notes attached to this transaction.' }}</p>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="content-card detail-card">
                <h3 class="section-title mb-3">Inventory Impact</h3>
                <div class="snapshot-list">
                    <div class="snapshot-row">
                        <span>Current Item Stock</span>
                        <strong>{{ number_format($transaction->inventoryItem->current_stock, 3) }} {{ strtoupper($transaction->inventoryItem->unit) }}</strong>
                    </div>
                    <div class="snapshot-row">
                        <span>Minimum Stock</span>
                        <strong>{{ number_format((float) $transaction->inventoryItem->minimum_stock, 3) }}</strong>
                    </div>
                    <div class="snapshot-row">
                        <span>Stock Status</span>
                        <strong>@include('inventory_items.partials.stock-status-badge', ['item' => $transaction->inventoryItem])</strong>
                    </div>
                </div>

                <div class="alert alert-info alert-modern mt-4 mb-0">
                    Ledger entries are kept read-only in the interface to protect stock integrity and audit history.
                </div>
            </div>
        </div>
    </div>
@endsection
