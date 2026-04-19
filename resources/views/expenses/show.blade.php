@extends('layouts.app')

@section('title', __('expenses.show.title'))
@section('page-title', __('expenses.show.page_title'))
@section('page-subtitle', __('expenses.show.page_subtitle'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/expenses/expenses-show.css') }}">
@endpush

@section('content')
    @php($isRtl = in_array(app()->getLocale(), ['ps', 'fa'], true))

    <div @class(['expense-page-shell', 'rtl' => $isRtl]) @if($isRtl) dir="rtl" @endif>
        <nav aria-label="breadcrumb" class="breadcrumb-shell mb-4">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('expenses.index') }}">{{ __('expenses.index.title') }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $expense->title }}</li>
            </ol>
        </nav>

        <div class="row g-4">
            <div class="col-xl-8">
                <div class="content-card detail-card">
                    <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 mb-4 expense-detail-header">
                        <div>
                            <h2 class="section-title mb-1">{{ $expense->title }}</h2>
                            <p class="section-text mb-0">{{ $expense->expense_date->translatedFormat('M d, Y') }}</p>
                        </div>
                        <div class="expense-amount-pill">
                            {{ number_format((float) $expense->amount, 2) }}
                        </div>
                    </div>

                    <div class="detail-grid">
                        <div class="detail-item">
                            <span class="detail-label">{{ __('expenses.fields.type') }}</span>
                            <span class="detail-value">@include('expenses.partials.expense-type-badge', ['expenseType' => $expense->expenseType])</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">{{ __('expenses.fields.amount') }}</span>
                            <span class="detail-value">{{ number_format((float) $expense->amount, 2) }}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">{{ __('expenses.fields.date') }}</span>
                            <span class="detail-value">{{ $expense->expense_date->translatedFormat('M d, Y') }}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">{{ __('expenses.fields.created_by') }}</span>
                            <span class="detail-value">{{ $expense->creator->name }}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">{{ __('expenses.fields.created_at') }}</span>
                            <span class="detail-value">{{ $expense->created_at->translatedFormat('M d, Y h:i A') }}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">{{ __('expenses.fields.updated_at') }}</span>
                            <span class="detail-value">{{ $expense->updated_at->translatedFormat('M d, Y h:i A') }}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">{{ __('expenses.fields.linked_stage') }}</span>
                            <span class="detail-value">
                                @if($expense->productionStage)
                                    <a href="{{ route('production-stages.show', $expense->productionStage) }}" class="stage-link-pill text-decoration-none">
                                        {{ $expense->productionStage->stage_name }}
                                    </a>
                                @else
                                    {{ __('expenses.messages.general_expense') }}
                                @endif
                            </span>
                        </div>
                    </div>

                    <div class="notes-panel mt-4">
                        <span class="detail-label">{{ __('expenses.fields.note') }}</span>
                        <p class="mb-0">{{ $expense->notes ?: __('expenses.messages.no_notes') }}</p>
                    </div>

                    @if($canManageExpenses)
                        <div class="d-flex flex-wrap gap-2 mt-4 expense-action-row">
                            <a href="{{ route('expenses.edit', $expense) }}" class="btn btn-primary">
                                <i class="bi bi-pencil-square me-1"></i>
                                {{ __('expenses.actions.edit') }}
                            </a>
                            <form method="POST" action="{{ route('expenses.destroy', $expense) }}" data-confirm="{{ __('expenses.messages.delete_confirm', ['title' => $expense->title]) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger" @disabled(! $canDeleteExpense)>
                                    <i class="bi bi-trash me-1"></i>
                                    {{ __('expenses.actions.delete') }}
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>

            <div class="col-xl-4">
                <div class="content-card detail-card">
                    <h3 class="section-title mb-3">{{ __('expenses.show.integrity_title') }}</h3>

                    @if($canDeleteExpense)
                        <div class="alert alert-success alert-modern mb-0">
                            {{ __('expenses.show.manageable') }}
                        </div>
                    @else
                        <div class="alert alert-warning alert-modern mb-0">
                            {{ __('expenses.show.system_managed') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
