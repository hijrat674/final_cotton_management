@extends('layouts.app')

@section('title', __('Create Cotton Entry'))
@section('page-title', __('Create Cotton Entry'))
@section('page-subtitle', __('Record a truck intake and post the material to inventory'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/cotton/cotton-form.css') }}">
@endpush

@section('content')
    <div class="cotton-entry-create-page">
        <nav aria-label="breadcrumb" class="breadcrumb-shell cotton-entry-create-breadcrumb mb-3">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('cotton-entries.index') }}">Cotton Entries</a></li>
                <li class="breadcrumb-item active" aria-current="page">Create</li>
            </ol>
        </nav>

        <div class="content-card form-card cotton-entry-create-card">
            <div class="cotton-entry-create-header">
                <h2 class="section-title mb-0">New Cotton Intake</h2>
            </div>

            <form method="POST" action="{{ route('cotton-entries.store') }}" data-cotton-entry-form class="cotton-entry-create-form">
                @csrf
                @include('cotton_entries.partials.form')

                <div class="d-flex flex-wrap gap-2 mt-3 cotton-entry-create-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i>
                        Save Cotton Entry
                    </button>
                    @include('layouts.partials.back-button', ['fallback' => route('cotton-entries.index')])
                </div>
            </form>
        </div>
    </div>
@endsection
