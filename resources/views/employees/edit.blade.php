@extends('layouts.app')

@section('title', __('employees.edit.title'))
@section('page-title', __('employees.edit.page_title'))
@section('page-subtitle', __('employees.edit.page_subtitle'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/employees/employees-form.css') }}">
@endpush

@section('content')
    <nav aria-label="breadcrumb" class="breadcrumb-shell mb-4">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('employees.index') }}">{{ __('employees.index.title') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('employees.show', $employee) }}">{{ $employee->full_name }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ __('employees.actions.edit') }}</li>
        </ol>
    </nav>

    <form method="POST" action="{{ route('employees.update', $employee) }}">
        @csrf
        @method('PUT')
        @include('employees.partials.form', ['employee' => $employee])
        <div class="d-flex flex-wrap gap-2 mt-4">
            <button type="submit" class="btn btn-primary">{{ __('employees.actions.update') }}</button>
            <a href="{{ route('employees.show', $employee) }}" class="btn btn-outline-secondary">{{ __('employees.actions.cancel') }}</a>
        </div>
    </form>
@endsection
