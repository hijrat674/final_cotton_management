@extends('layouts.app')

@section('title', __('Create Salary Record'))
@section('page-title', __('Create Salary Record'))
@section('page-subtitle', __('Generate a payroll period for one employee with automatic salary totals'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/payroll/payroll-form.css') }}">
@endpush

@section('content')
    <nav aria-label="breadcrumb" class="breadcrumb-shell mb-4">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('salary-records.index') }}">Payroll</a></li>
            <li class="breadcrumb-item active" aria-current="page">Create</li>
        </ol>
    </nav>

    <form method="POST" action="{{ route('salary-records.store') }}">
        @csrf
        @include('salary_records.partials.form')
        <div class="d-flex flex-wrap gap-2 mt-4">
            <button type="submit" class="btn btn-primary">Save Salary Record</button>
            <a href="{{ route('salary-records.index') }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
@endsection
