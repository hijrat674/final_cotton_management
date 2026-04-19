@extends('layouts.app')

@section('title', __('employees.show.title'))
@section('page-title', __('employees.show.page_title'))
@section('page-subtitle', __('employees.show.page_subtitle'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/employees/employees-show.css') }}">
@endpush

@section('content')
    <nav aria-label="breadcrumb" class="breadcrumb-shell mb-4">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('employees.index') }}">{{ __('employees.index.title') }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $employee->full_name }}</li>
        </ol>
    </nav>

    <div class="employee-hero-card mb-4">
        <div>
            <span class="employee-kicker">{{ __('employees.show.kicker') }}</span>
            <h2 class="mb-1">{{ $employee->full_name }}</h2>
            <p class="text-muted mb-0">{{ $employee->position }} - {{ $employee->department }}</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            @include('employees.partials.status-badge', ['employee' => $employee])
            @if($canManageEmployees)
                <a href="{{ route('employees.edit', $employee) }}" class="btn btn-outline-primary">{{ __('employees.actions.edit') }}</a>
            @endif
            @if($canDeleteEmployee)
                <form method="POST" action="{{ route('employees.destroy', $employee) }}" data-confirm="{{ __('employees.messages.delete_confirm_full', ['name' => $employee->full_name]) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger">{{ __('employees.actions.delete') }}</button>
                </form>
            @endif
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-4">
            <div class="content-card">
                <h3 class="section-title mb-3">{{ __('employees.show.profile_summary') }}</h3>
                <div class="employee-detail-grid">
                    <div class="employee-detail-item">
                        <span class="detail-label">{{ __('employees.fields.full_name') }}</span>
                        <span class="detail-value">{{ $employee->full_name }}</span>
                    </div>
                    <div class="employee-detail-item">
                        <span class="detail-label">{{ __('employees.fields.phone') }}</span>
                        <span class="detail-value">{{ $employee->phone }}</span>
                    </div>
                    <div class="employee-detail-item">
                        <span class="detail-label">{{ __('employees.fields.hire_date') }}</span>
                        <span class="detail-value">{{ $employee->hire_date->translatedFormat('M d, Y') }}</span>
                    </div>
                    <div class="employee-detail-item">
                        <span class="detail-label">{{ __('employees.fields.salary') }}</span>
                        <span class="detail-value">{{ number_format((float) $employee->salary, 2) }}</span>
                    </div>
                    <div class="employee-detail-item">
                        <span class="detail-label">{{ __('employees.fields.status') }}</span>
                        <span class="detail-value">@include('employees.partials.status-badge', ['employee' => $employee])</span>
                    </div>
                    <div class="employee-detail-item">
                        <span class="detail-label">{{ __('employees.fields.linked_user') }}</span>
                        <span class="detail-value">{{ $employee->user?->name ?? __('employees.messages.no_linked_account') }}</span>
                    </div>
                </div>
                <div class="notes-panel employee-detail-item employee-address-block mt-4">
                    <span class="detail-label">{{ __('employees.fields.address') }}</span>
                    <p class="detail-value mb-0">{{ $employee->address ?: __('employees.messages.no_address') }}</p>
                </div>
            </div>
        </div>

        <div class="col-xl-8">
            <div class="content-card mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h3 class="section-title mb-1">{{ __('employees.show.salary_history.title') }}</h3>
                        <p class="section-text mb-0">{{ __('employees.show.salary_history.text') }}</p>
                    </div>
                    <div class="summary-card-meta">{{ trans_choice('employees.show.salary_history.count', $employee->salaryRecords->count(), ['count' => $employee->salaryRecords->count()]) }}</div>
                </div>
                <div class="table-responsive">
                    <table class="table admin-table mb-0">
                        <thead><tr><th>{{ __('employees.show.salary_history.period') }}</th><th>{{ __('employees.show.salary_history.total') }}</th><th>{{ __('employees.show.salary_history.paid') }}</th><th>{{ __('employees.show.salary_history.remaining') }}</th><th>{{ __('employees.show.salary_history.status') }}</th></tr></thead>
                        <tbody>
                            @forelse($employee->salaryRecords as $salaryRecord)
                                <tr>
                                    <td><a href="{{ route('salary-records.show', $salaryRecord) }}" class="text-decoration-none fw-semibold">{{ $salaryRecord->period_label }}</a></td>
                                    <td>{{ number_format((float) $salaryRecord->total_salary, 2) }}</td>
                                    <td>{{ number_format((float) $salaryRecord->paid_amount, 2) }}</td>
                                    <td>{{ number_format((float) $salaryRecord->remaining_amount, 2) }}</td>
                                    <td>@include('salary_records.partials.status-badge', ['salaryRecord' => $salaryRecord])</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center py-4 text-muted">{{ __('employees.show.salary_history.empty') }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="content-card mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h3 class="section-title mb-1">{{ __('employees.show.advance_history.title') }}</h3>
                        <p class="section-text mb-0">{{ __('employees.show.advance_history.text') }}</p>
                    </div>
                    <div class="summary-card-meta">{{ trans_choice('employees.show.advance_history.count', $employee->advances->count(), ['count' => $employee->advances->count()]) }}</div>
                </div>
                <div class="table-responsive">
                    <table class="table admin-table mb-0">
                        <thead><tr><th>{{ __('employees.show.advance_history.date') }}</th><th>{{ __('employees.show.advance_history.amount') }}</th><th>{{ __('employees.show.advance_history.status') }}</th><th>{{ __('employees.show.advance_history.reason') }}</th></tr></thead>
                        <tbody>
                            @forelse($employee->advances as $advance)
                                <tr>
                                    <td><a href="{{ route('employee-advances.show', $advance) }}" class="text-decoration-none fw-semibold">{{ $advance->advance_date->translatedFormat('M d, Y') }}</a></td>
                                    <td>{{ number_format((float) $advance->amount, 2) }}</td>
                                    <td>@include('employee_advances.partials.status-badge', ['employeeAdvance' => $advance])</td>
                                    <td>{{ $advance->reason ?: __('employees.messages.no_reason') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center py-4 text-muted">{{ __('employees.show.advance_history.empty') }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="content-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h3 class="section-title mb-1">{{ __('employees.show.production_activity.title') }}</h3>
                        <p class="section-text mb-0">{{ __('employees.show.production_activity.text') }}</p>
                    </div>
                    <div class="summary-card-meta">{{ trans_choice('employees.show.production_activity.count', $employee->productionStages->count(), ['count' => $employee->productionStages->count()]) }}</div>
                </div>
                <div class="table-responsive">
                    <table class="table admin-table mb-0">
                        <thead><tr><th>{{ __('employees.show.production_activity.stage') }}</th><th>{{ __('employees.show.production_activity.date') }}</th><th>{{ __('employees.show.production_activity.source_material') }}</th><th class="text-end">{{ __('employees.show.production_activity.action') }}</th></tr></thead>
                        <tbody>
                            @forelse($employee->productionStages as $stage)
                                <tr>
                                    <td>{{ $stage->stage_name }}</td>
                                    <td>{{ $stage->stage_date->translatedFormat('M d, Y') }}</td>
                                    <td>{{ $stage->sourceInventoryItem->product_name }}</td>
                                    <td class="text-end"><a href="{{ route('production-stages.show', $stage) }}" class="btn btn-sm btn-outline-primary">{{ __('employees.actions.view') }}</a></td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center py-4 text-muted">{{ __('employees.show.production_activity.empty') }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
