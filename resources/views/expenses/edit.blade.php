@extends('layouts.app')

@section('title', __('expenses.edit.title'))
@section('page-title', __('expenses.edit.page_title'))
@section('page-subtitle', __('expenses.edit.page_subtitle'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/expenses/expenses-form.css') }}">
@endpush

@section('content')
    @php($isRtl = in_array(app()->getLocale(), ['ps', 'fa'], true))

    <div @class(['expense-page-shell', 'rtl' => $isRtl]) @if($isRtl) dir="rtl" @endif>
        <nav aria-label="breadcrumb" class="breadcrumb-shell mb-4">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('expenses.index') }}">{{ __('expenses.index.title') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('expenses.show', $expense) }}">{{ $expense->title }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ __('expenses.actions.edit') }}</li>
            </ol>
        </nav>

        <div class="content-card form-card">
            <div class="mb-4">
                <h2 class="section-title mb-1">{{ __('expenses.edit.heading') }}</h2>
                <p class="section-text mb-0">{{ __('expenses.edit.description') }}</p>
            </div>

            <form method="POST" action="{{ route('expenses.update', $expense) }}">
                @csrf
                @method('PUT')
                @include('expenses.partials.form')

                <div class="d-flex flex-wrap gap-2 mt-4 expense-action-row">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i>
                        {{ __('expenses.actions.update') }}
                    </button>
                    <a href="{{ route('expenses.show', $expense) }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle me-1"></i>
                        {{ __('expenses.actions.cancel') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
