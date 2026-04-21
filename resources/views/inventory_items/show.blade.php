@extends('layouts.app')

@section('title', __('Inventory Item Details'))
@section('page-title', __('Inventory Item Details'))
@section('page-subtitle', __('Review master data, live stock, and recent stock movements'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/inventory/inventory-items-show.css') }}">
@endpush

@section('content')
    <nav aria-label="breadcrumb" class="breadcrumb-shell mb-4">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('inventory-items.index') }}">Inventory Items</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $item->product_name }}</li>
        </ol>
    </nav>

    <div class="row g-4">
        <div class="col-xl-8">
            <div class="content-card detail-card">
                <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 mb-4">
                    <div>
                        <h2 class="section-title mb-1">{{ $item->product_name }}</h2>
                        <p class="section-text mb-0">{{ $item->product_code ?: 'No product code assigned' }}</p>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <span class="badge text-bg-light">{{ \App\Models\InventoryItem::productTypeOptions()[$item->product_type] ?? ucfirst(str_replace('_', ' ', $item->product_type)) }}</span>
                        @include('inventory_items.partials.stock-status-badge', ['item' => $item])
                    </div>
                </div>

                <div class="detail-grid">
                    <div>
                        <span class="detail-label">Unit</span>
                        <span class="detail-value">{{ strtoupper($item->unit) }}</span>
                    </div>
                    <div>
                        <span class="detail-label">Minimum Stock</span>
                        <span class="detail-value">{{ number_format((float) $item->minimum_stock, 3) }}</span>
                    </div>
                    <div>
                        <span class="detail-label">Current Stock</span>
                        <span class="detail-value">{{ number_format($item->current_stock, 3) }}</span>
                    </div>
                    <div>
                        <span class="detail-label">Stock Status</span>
                        <span class="detail-value">@include('inventory_items.partials.stock-status-badge', ['item' => $item])</span>
                    </div>
                </div>

                <div class="notes-panel mt-4">
                    <span class="detail-label">Notes</span>
                    <p class="mb-0">{{ $item->notes ?: 'No notes added for this item.' }}</p>
                </div>

                <div class="d-flex flex-wrap gap-2 mt-4">
                    @include('layouts.partials.back-button', ['fallback' => route('inventory-items.index')])
                    @if($canManageItems)
                        <a href="{{ route('inventory-items.edit', $item) }}" class="btn btn-primary">Edit Item</a>
                    @endif

                    @if(auth()->user()?->hasRole(\App\Models\User::ROLE_ADMIN, \App\Models\User::ROLE_PRODUCTION))
                        <a href="{{ route('inventory-transactions.create', ['inventory_item_id' => $item->id]) }}" class="btn btn-outline-primary">Add Transaction</a>
                    @endif

                    @if($canManageItems)
                        <form method="POST" action="{{ route('inventory-items.destroy', $item) }}" data-confirm="Delete {{ $item->product_name }}? This is only allowed when no transactions exist.">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger" @disabled($item->transactions->isNotEmpty())>Delete Item</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="content-card detail-card">
                <h3 class="section-title mb-3">Ledger Snapshot</h3>
                <div class="snapshot-list">
                    <div class="snapshot-row">
                        <span>Total Stock In</span>
                        <strong>{{ number_format((float) ($item->total_quantity_in ?? 0), 3) }}</strong>
                    </div>
                    <div class="snapshot-row">
                        <span>Total Stock Out</span>
                        <strong>{{ number_format((float) ($item->total_quantity_out ?? 0), 3) }}</strong>
                    </div>
                    <div class="snapshot-row">
                        <span>Current Balance</span>
                        <strong>{{ number_format($item->current_stock, 3) }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="content-card mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h3 class="section-title mb-1">Recent Transactions</h3>
                <p class="section-text mb-0">Latest stock movements recorded against this item.</p>
            </div>
            <a href="{{ route('inventory-transactions.index', ['inventory_item_id' => $item->id]) }}" class="btn btn-outline-secondary">View Full Ledger</a>
        </div>

        <div class="table-responsive">
            <table class="table admin-table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Type</th>
                        <th>In</th>
                        <th>Out</th>
                        <th>By</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($item->transactions as $transaction)
                        <tr>
                            <td>{{ $transaction->transaction_date->format('M d, Y') }}</td>
                            <td>{{ \App\Models\InventoryTransaction::transactionTypeOptions()[$transaction->transaction_type] ?? ucfirst(str_replace('_', ' ', $transaction->transaction_type)) }}</td>
                            <td>{{ number_format((float) $transaction->quantity_in, 3) }}</td>
                            <td>{{ number_format((float) $transaction->quantity_out, 3) }}</td>
                            <td>{{ $transaction->creator->name }}</td>
                            <td class="text-end">
                                <a href="{{ route('inventory-transactions.show', $transaction) }}" class="btn btn-sm btn-outline-primary">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No transactions have been recorded for this item yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
