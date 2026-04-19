@extends('layouts.app')

@section('title', __('Edit Sale'))
@section('page-title', __('Edit Sale'))
@section('page-subtitle', __('Adjust invoice lines carefully while keeping inventory and balances synchronized'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/sales/sales-form.css') }}">
@endpush

@section('content')
    <nav aria-label="breadcrumb" class="breadcrumb-shell mb-4">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('sales.index') }}">Sales</a></li>
            <li class="breadcrumb-item"><a href="{{ route('sales.show', $sale) }}">Sale #{{ $sale->id }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit</li>
        </ol>
    </nav>

    <form method="POST" action="{{ route('sales.update', $sale) }}">
        @csrf
        @method('PUT')
        @include('sales.partials.form', ['sale' => $sale])

        <div class="d-flex flex-wrap gap-2 mt-4">
            <button type="submit" class="btn btn-primary">Update Sale</button>
            <a href="{{ route('sales.show', $sale) }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
@endsection
