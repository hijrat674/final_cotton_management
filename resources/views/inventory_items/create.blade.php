@extends('layouts.app')

@section('title', __('Create Inventory Item'))
@section('page-title', __('Create Inventory Item'))
@section('page-subtitle', __('Add a new material or product to the inventory master list'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/inventory/inventory-items-form.css') }}">
@endpush

@section('content')
    <nav aria-label="breadcrumb" class="breadcrumb-shell mb-4">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('inventory-items.index') }}">Inventory Items</a></li>
            <li class="breadcrumb-item active" aria-current="page">Create</li>
        </ol>
    </nav>

    <div class="content-card form-card">
        <div class="mb-4">
            <h2 class="section-title mb-1">New Inventory Item</h2>
            <p class="section-text mb-0">This item becomes part of the central stock source of truth for future factory workflows.</p>
        </div>

        <form method="POST" action="{{ route('inventory-items.store') }}">
            @csrf
            @include('inventory_items.partials.form')

            <div class="d-flex flex-wrap gap-2 mt-4">
                <button type="submit" class="btn btn-primary">Create Item</button>
                <a href="{{ route('inventory-items.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
@endsection
