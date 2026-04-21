@extends('layouts.app')

@section('title', __('Collect Payment'))
@section('page-title', __('Collect Payment'))
@section('page-subtitle', __('Add a follow-up payment against an existing sales invoice'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/sales/sales-payments.css') }}">
@endpush

@section('content')
    <nav aria-label="breadcrumb" class="breadcrumb-shell mb-4">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('sales.index') }}">Sales</a></li>
            <li class="breadcrumb-item"><a href="{{ route('sales.show', $sale) }}">Sale #{{ $sale->id }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">Collect Payment</li>
        </ol>
    </nav>

    <form method="POST" action="{{ route('sale-payments.store') }}" data-sale-payment-form>
        @csrf
        <input type="hidden" name="sale_id" value="{{ $sale->id }}">
        <input type="hidden" name="customer_id" value="{{ $sale->customer_id }}">

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="sale-form-card">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Sale Reference</label>
                            <div class="form-control bg-light">Sale #{{ $sale->id }}</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Customer</label>
                            <div class="form-control bg-light">{{ $sale->customer->full_name }}</div>
                        </div>

                        <div class="col-md-4">
                            <label for="payment_date" class="form-label">Payment Date</label>
                            <input type="date" id="payment_date" name="payment_date" value="{{ old('payment_date', now()->toDateString()) }}" class="form-control @error('payment_date') is-invalid @enderror" required>
                            @error('payment_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="amount" class="form-label">Amount</label>
                            <input type="number" step="0.01" min="0.01" max="{{ number_format((float) $sale->remaining_amount, 2, '.', '') }}" id="amount" name="amount" value="{{ old('amount') }}" class="form-control @error('amount') is-invalid @enderror" data-payment-amount required>
                            @error('amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="payment_method" class="form-label">Payment Method</label>
                            <select id="payment_method" name="payment_method" class="form-select @error('payment_method') is-invalid @enderror" required>
                                <option value="">Select method</option>
                                @foreach($paymentMethods as $value => $label)
                                    <option value="{{ $value }}" @selected(old('payment_method') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('payment_method')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea id="notes" name="notes" rows="4" class="form-control @error('notes') is-invalid @enderror">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="sale-form-card payment-summary-card">
                    <h3 class="section-title mb-3">Balance Snapshot</h3>
                    <div class="summary-breakdown">
                        <div>
                            <span>Invoice Total</span>
                            <strong>{{ number_format((float) $sale->total_amount, 2) }}</strong>
                        </div>
                        <div>
                            <span>Already Paid</span>
                            <strong>{{ number_format((float) $sale->paid_amount, 2) }}</strong>
                        </div>
                        <div>
                            <span>Remaining Before</span>
                            <strong data-payment-remaining>{{ number_format((float) $sale->remaining_amount, 2) }}</strong>
                        </div>
                        <div>
                            <span>Remaining After</span>
                            <strong data-payment-after>{{ number_format((float) $sale->remaining_amount, 2) }}</strong>
                        </div>
                    </div>
                    <input type="hidden" value="{{ number_format((float) $sale->remaining_amount, 2, '.', '') }}" data-payment-current-remaining>
                </div>
            </div>
        </div>

        <div class="d-flex flex-wrap gap-2 mt-4">
            <button type="submit" class="btn btn-primary">Save Payment</button>
            @include('layouts.partials.back-button', ['fallback' => route('sales.show', $sale)])
        </div>
    </form>
@endsection
