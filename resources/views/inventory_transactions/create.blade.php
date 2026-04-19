@extends('layouts.app')

@section('title', __('Record Inventory Transaction'))
@section('page-title', __('Record Inventory Transaction'))
@section('page-subtitle', __('Add a stock movement to the ledger with integrity checks'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/inventory/inventory-transactions-form.css') }}">
@endpush

@section('content')
    <nav aria-label="breadcrumb" class="breadcrumb-shell mb-4">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('inventory-transactions.index') }}">Inventory Transactions</a></li>
            <li class="breadcrumb-item active" aria-current="page">Create</li>
        </ol>
    </nav>

    <div class="content-card form-card">
        <div class="mb-4">
            <h2 class="section-title mb-1">New Stock Movement</h2>
            <p class="section-text mb-0">Every stock change is recorded here so current stock remains fully traceable and derived from the ledger.</p>
        </div>

        <form method="POST" action="{{ route('inventory-transactions.store') }}" data-transaction-form>
            @csrf
            @include('inventory_transactions.partials.form')

            <div class="d-flex flex-wrap gap-2 mt-4">
                <button type="submit" class="btn btn-primary">Record Transaction</button>
                <a href="{{ route('inventory-transactions.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
@endsection
