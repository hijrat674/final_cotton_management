@extends('layouts.app')

@section('title', __('Create Expense'))
@section('page-title', __('Create Expense'))
@section('page-subtitle', __('Record an operational or production-linked expense'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/expenses/expenses-form.css') }}">
@endpush

@section('content')
    <nav aria-label="breadcrumb" class="breadcrumb-shell mb-4">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('expenses.index') }}">Expenses</a></li>
            <li class="breadcrumb-item active" aria-current="page">Create</li>
        </ol>
    </nav>

    <div class="content-card form-card">
        <div class="mb-4">
            <h2 class="section-title mb-1">New Expense</h2>
            <p class="section-text mb-0">Capture the expense with the correct category, amount, date, and optional production linkage.</p>
        </div>

        <form method="POST" action="{{ route('expenses.store') }}">
            @csrf
            @include('expenses.partials.form')

            <div class="d-flex flex-wrap gap-2 mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i>
                    Save Expense
                </button>
                <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-x-circle me-1"></i>
                    Cancel
                </a>
            </div>
        </form>
    </div>
@endsection
