@extends('layouts.app')

@section('title', $customer->full_name)
@section('page-title', __('Customer Profile'))
@section('page-subtitle', __('Sales exposure, collections, and account history for :name', ['name' => $customer->full_name]))

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/customers/customers-show.css') }}">
@endpush

@section('content')
    <nav aria-label="breadcrumb" class="breadcrumb-shell mb-4">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('customers.index') }}">Customers</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $customer->full_name }}</li>
        </ol>
    </nav>

    <div class="customer-hero-card mb-4">
        <div>
            <span class="customer-hero-kicker">Customer Account</span>
            <h2 class="mb-1">{{ $customer->full_name }}</h2>
            <p class="text-muted mb-0">{{ $customer->address ?: 'No address recorded yet.' }}</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            @if($canManageCustomers)
                <a href="{{ route('customers.edit', $customer) }}" class="btn btn-outline-primary">Edit Customer</a>
            @endif
            @if($canDeleteCustomer)
                <form method="POST" action="{{ route('customers.destroy', $customer) }}" data-confirm="Delete {{ $customer->full_name }}? This is only allowed when no sales history exists.">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger">Delete Customer</button>
                </form>
            @endif
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="summary-card">
                <div class="summary-card-label">Phone</div>
                <div class="summary-card-value fs-4">{{ $customer->phone }}</div>
                <div class="summary-card-meta">Primary contact</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="summary-card">
                <div class="summary-card-label">Total Sales</div>
                <div class="summary-card-value">{{ $summary['total_sales'] }}</div>
                <div class="summary-card-meta">Invoices issued</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="summary-card">
                <div class="summary-card-label">Collected</div>
                <div class="summary-card-value">{{ number_format($summary['paid_amount'], 2) }}</div>
                <div class="summary-card-meta">Paid against sales</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="summary-card">
                <div class="summary-card-label">Outstanding</div>
                <div class="summary-card-value">{{ number_format($summary['outstanding'], 2) }}</div>
                <div class="summary-card-meta">Current receivable</div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-7">
            <div class="content-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h2 class="section-title mb-1">Sales History</h2>
                        <p class="section-text mb-0">Invoice register for this customer.</p>
                    </div>
                    @if($canManageCustomers)
                        <a href="{{ route('sales.create', ['customer_id' => $customer->id]) }}" class="btn btn-outline-primary">New Sale</a>
                    @endif
                </div>

                <div class="table-responsive">
                    <table class="table admin-table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Invoice</th>
                                <th>Date</th>
                                <th>Total</th>
                                <th>Paid</th>
                                <th>Remaining</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($customer->sales as $sale)
                                <tr>
                                    <td><a href="{{ route('sales.show', $sale) }}" class="fw-semibold text-decoration-none">Sale #{{ $sale->id }}</a></td>
                                    <td>{{ $sale->sale_date->format('M d, Y') }}</td>
                                    <td>{{ number_format((float) $sale->total_amount, 2) }}</td>
                                    <td>{{ number_format((float) $sale->paid_amount, 2) }}</td>
                                    <td>{{ number_format((float) $sale->remaining_amount, 2) }}</td>
                                    <td>@include('sales.partials.payment-status-badge', ['sale' => $sale])</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">No sales recorded for this customer yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-xl-5">
            <div class="content-card mb-4">
                <h2 class="section-title mb-3">Payment History</h2>
                <div class="d-grid gap-3">
                    @forelse($customer->salePayments as $payment)
                        <div class="payment-history-card">
                            <div class="d-flex justify-content-between align-items-start gap-3">
                                <div>
                                    <div class="fw-semibold">{{ number_format((float) $payment->amount, 2) }}</div>
                                    <div class="text-muted small">
                                        Sale #{{ $payment->sale_id }} • {{ \App\Models\SalePayment::paymentMethodOptions()[$payment->payment_method] ?? ucfirst($payment->payment_method) }}
                                    </div>
                                </div>
                                <span class="text-muted small">{{ $payment->payment_date->format('M d, Y') }}</span>
                            </div>
                            <div class="text-muted small mt-2">Received by {{ $payment->receiver->name }}</div>
                            @if($payment->notes)
                                <div class="small mt-2">{{ $payment->notes }}</div>
                            @endif
                        </div>
                    @empty
                        <p class="text-muted mb-0">No follow-up payments have been recorded for this customer yet.</p>
                    @endforelse
                </div>
            </div>

            <div class="content-card">
                <h2 class="section-title mb-3">Account Notes</h2>
                <p class="mb-0 text-muted">{{ $customer->notes ?: 'No notes added for this customer yet.' }}</p>
            </div>
        </div>
    </div>
@endsection
