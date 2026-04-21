@extends('layouts.app')

@section('title', __('Create Production Stage'))
@section('page-title', __('Create Production Stage'))
@section('page-subtitle', __('Issue input stock, record outputs, and post production cost'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/production/production-form.css') }}">
@endpush

@section('content')
    <nav aria-label="breadcrumb" class="breadcrumb-shell mb-4">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('production-stages.index') }}">Production Stages</a></li>
            <li class="breadcrumb-item active" aria-current="page">Create</li>
        </ol>
    </nav>

    <div class="content-card form-card">
        <div class="mb-4">
            <h2 class="section-title mb-1">New Production Stage</h2>
        </div>

        <form method="POST" action="{{ route('production-stages.store') }}" data-production-stage-form>
            @csrf
            @include('production_stages.partials.form')

            <div class="d-flex flex-wrap gap-2 mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i>
                    Save Production Stage
                </button>
                @include('layouts.partials.back-button', ['fallback' => route('production-stages.index')])
            </div>
        </form>
    </div>
@endsection
