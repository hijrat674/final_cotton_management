@extends('layouts.app')

@section('title', __('Create Customer'))
@section('page-title', __('Create Customer'))
@section('page-subtitle', __('Add a new customer account for sales invoices and collection tracking'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/customers/customers-form.css') }}">
@endpush

@section('content')
    <nav aria-label="breadcrumb" class="breadcrumb-shell mb-4">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('customers.index') }}">Customers</a></li>
            <li class="breadcrumb-item active" aria-current="page">Create</li>
        </ol>
    </nav>

    <form method="POST" action="{{ route('customers.store') }}">
        @csrf
        @include('customers.partials.form')

        <div class="d-flex flex-wrap gap-2 mt-4">
            <button type="submit" class="btn btn-primary">Save Customer</button>
            @include('layouts.partials.back-button', ['fallback' => route('customers.index')])
        </div>
    </form>
@endsection
