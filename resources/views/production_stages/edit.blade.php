@extends('layouts.app')

@section('title', __('production.edit.title'))
@section('page-title', __('production.edit.page_title'))
@section('page-subtitle', __('production.edit.page_subtitle'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/production/production-form.css') }}">
@endpush

@section('content')
    <nav aria-label="breadcrumb" class="breadcrumb-shell mb-4">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('production-stages.index') }}">{{ __('production.index.title') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('production-stages.show', $stage) }}">{{ $stage->stage_name }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ __('production.actions.edit') }}</li>
        </ol>
    </nav>

    <div class="content-card form-card">
        <div class="mb-4">
            <h2 class="section-title mb-1">{{ __('production.edit.heading') }}</h2>
        </div>

        <form method="POST" action="{{ route('production-stages.update', $stage) }}" data-production-stage-form>
            @csrf
            @method('PUT')
            @include('production_stages.partials.form')

            <div class="d-flex flex-wrap gap-2 mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i>
                    {{ __('production.actions.update') }}
                </button>
                @include('layouts.partials.back-button', ['fallback' => route('production-stages.show', $stage)])
            </div>
        </form>
    </div>
@endsection
