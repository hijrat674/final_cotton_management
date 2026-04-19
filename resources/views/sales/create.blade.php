@extends('layouts.app')

@section('title', __('Create Sale'))
@section('page-title', __('Create Sale'))
@section('page-subtitle', __('Issue a customer invoice, deduct stock, and record any opening payment'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/sales/sales-form.css') }}">
@endpush

@section('content')
    <nav aria-label="breadcrumb" class="breadcrumb-shell mb-4">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('sales.index') }}">Sales</a></li>
            <li class="breadcrumb-item active" aria-current="page">Create</li>
        </ol>
    </nav>

    <form method="POST" action="{{ route('sales.store') }}">
        @csrf
        @include('sales.partials.form')

        <div class="d-flex flex-wrap gap-2 mt-4">
            <button type="submit" class="btn btn-primary">Post Sale</button>
            <a href="{{ route('sales.index') }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
@endsection
