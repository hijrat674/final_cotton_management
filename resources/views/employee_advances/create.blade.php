@extends('layouts.app')

@section('title', __('Record Employee Advance'))
@section('page-title', __('Record Employee Advance'))
@section('page-subtitle', __('Issue an advance safely and let payroll deduct it automatically in the next salary cycle'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/advance/advance-form.css') }}">
@endpush

@section('content')
    <nav aria-label="breadcrumb" class="breadcrumb-shell mb-4">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('employee-advances.index') }}">Employee Advances</a></li>
            <li class="breadcrumb-item active" aria-current="page">Record Advance</li>
        </ol>
    </nav>

    <form method="POST" action="{{ route('employee-advances.store') }}">
        @csrf
        @include('employee_advances.partials.form')

        <div class="d-flex justify-content-end gap-2 mt-4">
            <a href="{{ route('employee-advances.index') }}" class="btn btn-outline-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">Save Advance</button>
        </div>
    </form>
@endsection
