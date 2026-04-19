@extends('layouts.app')

@section('title', __('Edit Customer'))
@section('page-title', __('Edit Customer'))
@section('page-subtitle', __('Maintain customer identity, contact details, and account notes'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/customers/customers-form.css') }}">
@endpush

@section('content')
    <nav aria-label="breadcrumb" class="breadcrumb-shell mb-4">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('customers.index') }}">Customers</a></li>
            <li class="breadcrumb-item"><a href="{{ route('customers.show', $customer) }}">{{ $customer->full_name }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit</li>
        </ol>
    </nav>

    <form method="POST" action="{{ route('customers.update', $customer) }}">
        @csrf
        @method('PUT')
        @include('customers.partials.form', ['customer' => $customer])

        <div class="d-flex flex-wrap gap-2 mt-4">
            <button type="submit" class="btn btn-primary">Update Customer</button>
            <a href="{{ route('customers.show', $customer) }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
@endsection
