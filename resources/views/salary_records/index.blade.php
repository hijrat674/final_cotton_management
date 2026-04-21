@extends('layouts.app')

@section('title', __('payroll.index.title'))
@section('page-title', __('payroll.index.page_title'))
@section('page-subtitle', __('payroll.index.page_subtitle'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/payroll/payroll-index.css') }}">
@endpush

@section('content')
    <nav aria-label="breadcrumb" class="breadcrumb-shell mb-4">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ __('payroll.index.title') }}</li>
        </ol>
    </nav>

    <div class="row g-4 mb-4">
        <div class="col-md-3"><div class="summary-card"><div class="summary-card-label">{{ __('payroll.summary.total_salary') }}</div><div class="summary-card-value">{{ number_format($summary['total_salary'], 2) }}</div></div></div>
        <div class="col-md-3"><div class="summary-card"><div class="summary-card-label">{{ __('payroll.summary.advance_deduction') }}</div><div class="summary-card-value">{{ number_format($summary['advance_deduction'], 2) }}</div></div></div>
        <div class="col-md-3"><div class="summary-card"><div class="summary-card-label">{{ __('payroll.summary.paid') }}</div><div class="summary-card-value">{{ number_format($summary['paid_amount'], 2) }}</div></div></div>
        <div class="col-md-3"><div class="summary-card"><div class="summary-card-label">{{ __('payroll.summary.remaining') }}</div><div class="summary-card-value">{{ number_format($summary['remaining_amount'], 2) }}</div></div></div>
        <div class="col-md-3"><div class="summary-card"><div class="summary-card-label">{{ __('payroll.summary.employees_fully_paid') }}</div><div class="summary-card-value">{{ $summary['employees_paid'] }}</div></div></div>
    </div>

    <div class="content-card mb-4">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-3">
            <div>
                <h2 class="section-title mb-1">{{ __('payroll.index.records_title') }}</h2>
            </div>
            @if($canManagePayroll)
                <a href="{{ route('salary-records.create') }}" class="btn btn-primary">{{ __('payroll.actions.create_salary_record') }}</a>
            @endif
        </div>

        <form method="GET" action="{{ route('salary-records.index') }}" class="filters-form" id="salaryRecordsFilterForm">
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="employee_id" class="form-label">{{ __('payroll.filters.employee') }}</label>
                    <select id="employee_id" name="employee_id" class="form-select">
                        <option value="">{{ __('payroll.filters.all_employees') }}</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" @selected($filters['employee_id'] === (string) $employee->id)>{{ $employee->full_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="salary_month" class="form-label">{{ __('payroll.filters.month') }}</label>
                    <select id="salary_month" name="salary_month" class="form-select">
                        <option value="">{{ __('payroll.filters.all_months') }}</option>
                        @foreach($months as $value => $label)
                            @php($monthKey = 'payroll.months.' . strtolower($label))
                            <option value="{{ $value }}" @selected($filters['salary_month'] === (string) $value)>{{ \Illuminate\Support\Facades\Lang::has($monthKey) ? __($monthKey) : $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="salary_year" class="form-label">{{ __('payroll.filters.year') }}</label>
                    <select id="salary_year" name="salary_year" class="form-select">
                        <option value="">{{ __('payroll.filters.all_years') }}</option>
                        @foreach($years as $year)
                            <option value="{{ $year }}" @selected($filters['salary_year'] === (string) $year)>{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="sort" class="form-label">{{ __('payroll.filters.sort') }}</label>
                    <select id="sort" name="sort" class="form-select">
                        <option value="latest" @selected($filters['sort'] === 'latest')>{{ __('payroll.filters.latest') }}</option>
                        <option value="oldest" @selected($filters['sort'] === 'oldest')>{{ __('payroll.filters.oldest') }}</option>
                    </select>
                </div>
            </div>

            <div class="d-flex flex-wrap gap-2 mt-3">
                <button type="submit" class="btn btn-primary">{{ __('payroll.actions.apply_filters') }}</button>
                <button type="button" class="btn btn-outline-secondary" data-reset-filters="#salaryRecordsFilterForm">{{ __('payroll.actions.reset') }}</button>
            </div>
        </form>
    </div>

    <div class="content-card">
        <div class="table-responsive">
            <table class="table admin-table align-middle mb-0">
                <thead><tr><th>{{ __('payroll.table.employee') }}</th><th>{{ __('payroll.table.period') }}</th><th>{{ __('payroll.table.gross') }}</th><th>{{ __('payroll.table.advance') }}</th><th>{{ __('payroll.table.net') }}</th><th>{{ __('payroll.table.paid') }}</th><th>{{ __('payroll.table.remaining') }}</th><th>{{ __('payroll.table.status') }}</th><th class="text-end">{{ __('payroll.table.actions') }}</th></tr></thead>
                <tbody>
                    @forelse($salaryRecords as $salaryRecord)
                        <tr>
                            <td><div class="fw-semibold">{{ $salaryRecord->employee->full_name }}</div><div class="text-muted small">{{ $salaryRecord->employee->department }}</div></td>
                            @php($periodMonth = \App\Models\SalaryRecord::monthOptions()[$salaryRecord->salary_month] ?? '')
                            @php($periodMonthKey = 'payroll.months.' . strtolower($periodMonth))
                            <td>{{ \Illuminate\Support\Facades\Lang::has($periodMonthKey) ? __($periodMonthKey) : $periodMonth }} {{ $salaryRecord->salary_year }}</td>
                            <td>{{ number_format((float) $salaryRecord->gross_salary, 2) }}</td>
                            <td>{{ number_format((float) $salaryRecord->advance_deduction, 2) }}</td>
                            <td>{{ number_format((float) $salaryRecord->total_salary, 2) }}</td>
                            <td>{{ number_format((float) $salaryRecord->paid_amount, 2) }}</td>
                            <td>{{ number_format((float) $salaryRecord->remaining_amount, 2) }}</td>
                            <td>@include('salary_records.partials.status-badge', ['salaryRecord' => $salaryRecord])</td>
                            <td class="text-end"><a href="{{ route('salary-records.show', $salaryRecord) }}" class="btn btn-sm btn-outline-primary">{{ __('payroll.actions.view') }}</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="text-center py-5 text-muted">{{ __('payroll.messages.empty') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $salaryRecords->links() }}</div>
    </div>
@endsection
