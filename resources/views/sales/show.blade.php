@extends('layouts.app')

@section('title', __('Sale #:id', ['id' => $sale->id]))
@section('page-title', __('Sale Details'))
@section('page-subtitle', __('Invoice view with stock movements, collection history, and balance status'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/sales/sales-show.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/sales/sales-payments.css') }}">
@endpush

@section('content')
    <nav aria-label="breadcrumb" class="breadcrumb-shell mb-4">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('sales.index') }}">Sales</a></li>
            <li class="breadcrumb-item active" aria-current="page">Sale #{{ $sale->id }}</li>
        </ol>
    </nav>

    <div class="invoice-shell mb-4">
        <div>
            <span class="invoice-kicker">Sales Invoice</span>
            <h2 class="mb-1">Sale #{{ $sale->id }}</h2>
            <p class="text-muted mb-0">{{ $sale->customer->full_name }} • {{ $sale->sale_date->format('M d, Y') }}</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            @include('layouts.partials.back-button', ['fallback' => route('sales.index')])
            @include('sales.partials.payment-status-badge', ['sale' => $sale])
            @if($canManageSales)
                <a href="{{ route('sales.edit', $sale) }}" class="btn btn-outline-primary">Edit Sale</a>
            @endif
            @if($canCollectPayments && (float) $sale->remaining_amount > 0)
                <a href="{{ route('sale-payments.create', $sale) }}" class="btn btn-success">Collect Payment</a>
            @endif
            @if($canDeleteSales)
                <form method="POST" action="{{ route('sales.destroy', $sale) }}" data-confirm="Delete Sale #{{ $sale->id }}? This is only allowed when no payment has been collected.">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger" @disabled(! $canDeleteSale)>Delete</button>
                </form>
            @endif
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="summary-card">
                <div class="summary-card-label">Customer</div>
                <div class="summary-card-value fs-4">{{ $sale->customer->full_name }}</div>
                <div class="summary-card-meta">{{ $sale->customer->phone }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="summary-card">
                <div class="summary-card-label">Invoice Total</div>
                <div class="summary-card-value">{{ number_format((float) $sale->total_amount, 2) }}</div>
                <div class="summary-card-meta">Gross sale amount</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="summary-card">
                <div class="summary-card-label">Collected</div>
                <div class="summary-card-value">{{ number_format((float) $sale->paid_amount, 2) }}</div>
                <div class="summary-card-meta">Opening + later payments</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="summary-card">
                <div class="summary-card-label">Remaining</div>
                <div class="summary-card-value">{{ number_format((float) $sale->remaining_amount, 2) }}</div>
                <div class="summary-card-meta">Outstanding balance</div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-8">
            <div class="content-card mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h2 class="section-title mb-1">Invoice Items</h2>
                        <p class="section-text mb-0">Multi-line sale items deducted from inventory.</p>
                    </div>
                    <div class="text-muted">Sold by {{ $sale->seller->name }}</div>
                </div>

                <div class="table-responsive">
                    <table class="table admin-table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Quantity</th>
                                <th>Unit Price</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sale->items as $item)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $item->inventoryItem->product_name }}</div>
                                        <div class="text-muted small">{{ strtoupper($item->unit) }}</div>
                                    </td>
                                    <td>{{ number_format((float) $item->quantity, 3) }}</td>
                                    <td>{{ number_format((float) $item->unit_price, 2) }}</td>
                                    <td class="fw-semibold">{{ number_format((float) $item->total_price, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="content-card">
                <h2 class="section-title mb-3">Inventory Movements</h2>
                <div class="table-responsive">
                    <table class="table admin-table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Date</th>
                                <th>Quantity Out</th>
                                <th>Posted By</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($inventoryMovements as $movement)
                                <tr>
                                    <td>{{ $movement->inventoryItem->product_name }}</td>
                                    <td>{{ $movement->transaction_date->format('M d, Y') }}</td>
                                    <td>{{ number_format((float) $movement->quantity_out, 3) }}</td>
                                    <td>{{ $movement->creator->name }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">No inventory movement rows were found for this sale.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="content-card mb-4">
                <h2 class="section-title mb-3">Payment History</h2>
                <div class="payment-history-stack">
                    @php
                        $openingPayment = max(0, round((float) $sale->paid_amount - (float) $sale->payments->sum('amount'), 2));
                    @endphp

                    <div class="payment-history-card payment-history-initial">
                        <div class="d-flex justify-content-between align-items-start gap-3">
                            <div>
                                <div class="fw-semibold">{{ number_format($openingPayment, 2) }}</div>
                                <div class="text-muted small">Opening payment recorded with sale</div>
                            </div>
                            <span class="text-muted small">{{ $sale->sale_date->format('M d, Y') }}</span>
                        </div>
                    </div>

                    @forelse($sale->payments as $payment)
                        <div class="payment-history-card">
                            <div class="d-flex justify-content-between align-items-start gap-3">
                                <div>
                                    <div class="fw-semibold">{{ number_format((float) $payment->amount, 2) }}</div>
                                    <div class="text-muted small">{{ \App\Models\SalePayment::paymentMethodOptions()[$payment->payment_method] ?? ucfirst($payment->payment_method) }}</div>
                                </div>
                                <span class="text-muted small">{{ $payment->payment_date->format('M d, Y') }}</span>
                            </div>
                            <div class="text-muted small mt-2">Received by {{ $payment->receiver->name }}</div>
                            @if($payment->notes)
                                <div class="small mt-2">{{ $payment->notes }}</div>
                            @endif
                        </div>
                    @empty
                        <p class="text-muted mb-0">No follow-up payments have been collected for this sale yet.</p>
                    @endforelse
                </div>
            </div>

            <div class="content-card">
                <h2 class="section-title mb-3">Notes</h2>
                <p class="mb-0 text-muted">{{ $sale->notes ?: 'No invoice notes were added.' }}</p>
            </div>
        </div>
    </div>
@endsection
