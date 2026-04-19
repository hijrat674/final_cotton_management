@extends('layouts.app')

@section('title', __('Create Employee'))
@section('page-title', __('Create Employee'))
@section('page-subtitle', __('Add a new workforce record for HR visibility and production accountability'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/employees/employees-form.css') }}">
@endpush

@section('content')
    <nav aria-label="breadcrumb" class="breadcrumb-shell mb-4">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('employees.index') }}">Employees</a></li>
            <li class="breadcrumb-item active" aria-current="page">Create</li>
        </ol>
    </nav>

    <form method="POST" action="{{ route('employees.store') }}">
        @csrf
        @include('employees.partials.form')
        <div class="d-flex flex-wrap gap-2 mt-4">
            <button type="submit" class="btn btn-primary">Save Employee</button>
            <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
@endsection
