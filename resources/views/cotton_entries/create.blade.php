@extends('layouts.app')

@section('title', __('Create Cotton Entry'))
@section('page-title', __('Create Cotton Entry'))
@section('page-subtitle', __('Record a truck intake and post the material to inventory'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/cotton/cotton-form.css') }}">
@endpush

@section('content')
    <nav aria-label="breadcrumb" class="breadcrumb-shell mb-4">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('cotton-entries.index') }}">Cotton Entries</a></li>
            <li class="breadcrumb-item active" aria-current="page">Create</li>
        </ol>
    </nav>

    <div class="content-card form-card">
        <div class="mb-4">
            <h2 class="section-title mb-1">New Cotton Intake</h2>
            <p class="section-text mb-0">This intake will create both the operational truck record and the matching inventory intake transaction.</p>
        </div>

        <form method="POST" action="{{ route('cotton-entries.store') }}" data-cotton-entry-form>
            @csrf
            @include('cotton_entries.partials.form')

            <div class="d-flex flex-wrap gap-2 mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i>
                    Save Cotton Entry
                </button>
                <a href="{{ route('cotton-entries.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-x-circle me-1"></i>
                    Cancel
                </a>
            </div>
        </form>
    </div>
@endsection
