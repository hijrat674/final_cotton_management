@extends('layouts.app')

@section('title', __('Edit Inventory Item'))
@section('page-title', __('Edit Inventory Item'))
@section('page-subtitle', __('Update the inventory master record safely'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/inventory/inventory-items-form.css') }}">
@endpush

@section('content')
    <nav aria-label="breadcrumb" class="breadcrumb-shell mb-4">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('inventory-items.index') }}">Inventory Items</a></li>
            <li class="breadcrumb-item"><a href="{{ route('inventory-items.show', $item) }}">{{ $item->product_name }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit</li>
        </ol>
    </nav>

    <div class="content-card form-card">
        <div class="mb-4">
            <h2 class="section-title mb-1">Edit {{ $item->product_name }}</h2>
            <p class="section-text mb-0">Update naming, classification, unit, and minimum stock details without altering ledger history.</p>
        </div>

        <form method="POST" action="{{ route('inventory-items.update', $item) }}">
            @csrf
            @method('PUT')
            @include('inventory_items.partials.form')

            <div class="d-flex flex-wrap gap-2 mt-4">
                <button type="submit" class="btn btn-primary">Save Changes</button>
                @include('layouts.partials.back-button', ['fallback' => route('inventory-items.show', $item)])
            </div>
        </form>
    </div>
@endsection
