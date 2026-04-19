@extends('layouts.app')

@section('title', __('cotton.edit.title'))
@section('page-title', __('cotton.edit.page_title'))
@section('page-subtitle', __('cotton.edit.page_subtitle'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/cotton/cotton-form.css') }}">
@endpush

@section('content')
    <nav aria-label="breadcrumb" class="breadcrumb-shell mb-4">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('cotton-entries.index') }}">{{ __('cotton.index.title') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('cotton-entries.show', $entry) }}">{{ $entry->truck_number }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ __('cotton.actions.edit') }}</li>
        </ol>
    </nav>

    <div class="content-card form-card">
        <div class="mb-4">
            <h2 class="section-title mb-1">{{ __('cotton.edit.heading', ['truck' => $entry->truck_number]) }}</h2>
            <p class="section-text mb-0">{{ __('cotton.edit.description') }}</p>
        </div>

        <form method="POST" action="{{ route('cotton-entries.update', $entry) }}" data-cotton-entry-form>
            @csrf
            @method('PUT')
            @include('cotton_entries.partials.form')

            <div class="d-flex flex-wrap gap-2 mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i>
                    {{ __('cotton.actions.update') }}
                </button>
                <a href="{{ route('cotton-entries.show', $entry) }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>
                    {{ __('cotton.actions.back') }}
                </a>
            </div>
        </form>
    </div>
@endsection
