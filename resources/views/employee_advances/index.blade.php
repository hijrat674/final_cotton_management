@extends('layouts.app')

@section('title', __('advances.index.title'))
@section('page-title', __('advances.index.page_title'))
@section('page-subtitle', __('advances.index.page_subtitle'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/advance/advance-index.css') }}">
@endpush

@section('content')
    <nav aria-label="breadcrumb" class="breadcrumb-shell mb-4">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ __('advances.index.title') }}</li>
        </ol>
    </nav>

    <div class="row g-4 mb-4">
        <div class="col-md-4"><div class="summary-card"><div class="summary-card-label">{{ __('advances.summary.total') }}</div><div class="summary-card-value">{{ number_format($summary['total_advances'], 2) }}</div></div></div>
        <div class="col-md-4"><div class="summary-card"><div class="summary-card-label">{{ __('advances.summary.pending') }}</div><div class="summary-card-value">{{ number_format($summary['pending_advances'], 2) }}</div></div></div>
        <div class="col-md-4"><div class="summary-card"><div class="summary-card-label">{{ __('advances.summary.deducted') }}</div><div class="summary-card-value">{{ number_format($summary['deducted_advances'], 2) }}</div></div></div>
    </div>

    <div class="content-card mb-4">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-3">
            <div>
                <h2 class="section-title mb-1">{{ __('advances.index.register_title') }}</h2>
                <p class="section-text mb-0">{{ __('advances.index.register_text') }}</p>
            </div>
            @if($canManageAdvances)
                <a href="{{ route('employee-advances.create') }}" class="btn btn-primary">{{ __('advances.actions.record') }}</a>
            @endif
        </div>

        <form method="GET" action="{{ route('employee-advances.index') }}" class="filters-form" id="employeeAdvancesFilterForm">
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="employee_id" class="form-label">{{ __('advances.filters.employee') }}</label>
                    <select id="employee_id" name="employee_id" class="form-select">
                        <option value="">{{ __('advances.filters.all_employees') }}</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" @selected($filters['employee_id'] === (string) $employee->id)>{{ $employee->full_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="status" class="form-label">{{ __('advances.filters.status') }}</label>
                    <select id="status" name="status" class="form-select">
                        <option value="">{{ __('advances.filters.all_statuses') }}</option>
                        @foreach($statuses as $value => $label)
                            @php($statusKey = 'advances.statuses.' . $value)
                            <option value="{{ $value }}" @selected($filters['status'] === $value)>{{ \Illuminate\Support\Facades\Lang::has($statusKey) ? __($statusKey) : $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="d-flex flex-wrap gap-2 mt-3">
                <button type="submit" class="btn btn-primary">{{ __('advances.actions.apply_filters') }}</button>
                <button type="button" class="btn btn-outline-secondary" data-reset-filters="#employeeAdvancesFilterForm">{{ __('advances.actions.reset') }}</button>
            </div>
        </form>
    </div>

    <div class="content-card">
        <div class="table-responsive">
            <table class="table admin-table align-middle mb-0">
                <thead><tr><th>{{ __('advances.table.employee') }}</th><th>{{ __('advances.table.date') }}</th><th>{{ __('advances.table.amount') }}</th><th>{{ __('advances.table.status') }}</th><th>{{ __('advances.table.created_by') }}</th><th class="text-end">{{ __('advances.table.action') }}</th></tr></thead>
                <tbody>
                    @forelse($advances as $employeeAdvance)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $employeeAdvance->employee->full_name }}</div>
                                <div class="text-muted small">{{ $employeeAdvance->employee->department }}</div>
                            </td>
                            <td>{{ $employeeAdvance->advance_date->format('M d, Y') }}</td>
                            <td>{{ number_format((float) $employeeAdvance->amount, 2) }}</td>
                            <td>@include('employee_advances.partials.status-badge', ['employeeAdvance' => $employeeAdvance])</td>
                            <td>{{ $employeeAdvance->creator->name }}</td>
                            <td class="text-end"><a href="{{ route('employee-advances.show', $employeeAdvance) }}" class="btn btn-sm btn-outline-primary">{{ __('advances.actions.view') }}</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center py-5 text-muted">{{ __('advances.messages.empty') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $advances->links() }}</div>
    </div>
@endsection
