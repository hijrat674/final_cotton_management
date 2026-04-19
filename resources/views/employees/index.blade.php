@extends('layouts.app')

@section('title', __('employees.index.title'))
@section('page-title', __('employees.index.page_title'))
@section('page-subtitle', __('employees.index.page_subtitle'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/employees/employees-index.css') }}">
@endpush

@section('content')
    <nav aria-label="breadcrumb" class="breadcrumb-shell mb-4">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ __('employees.index.title') }}</li>
        </ol>
    </nav>

    <div class="row g-4 mb-4">
        <div class="col-md-3"><div class="summary-card"><div class="summary-card-label">{{ __('employees.summary.total') }}</div><div class="summary-card-value">{{ $summary['total_employees'] }}</div></div></div>
        <div class="col-md-3"><div class="summary-card"><div class="summary-card-label">{{ __('employees.summary.active') }}</div><div class="summary-card-value">{{ $summary['active_employees'] }}</div></div></div>
        <div class="col-md-3"><div class="summary-card"><div class="summary-card-label">{{ __('employees.summary.inactive') }}</div><div class="summary-card-value">{{ $summary['inactive_employees'] }}</div></div></div>
        <div class="col-md-3"><div class="summary-card"><div class="summary-card-label">{{ __('employees.summary.departments') }}</div><div class="summary-card-value">{{ $summary['departments_count'] }}</div></div></div>
    </div>

    <div class="content-card mb-4">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-3">
            <div>
                <h2 class="section-title mb-1">{{ __('employees.index.register_title') }}</h2>
                <p class="section-text mb-0">{{ __('employees.index.register_text') }}</p>
            </div>
            @if($canManageEmployees)
                <a href="{{ route('employees.create') }}" class="btn btn-primary">{{ __('employees.actions.new') }}</a>
            @endif
        </div>

        <form method="GET" action="{{ route('employees.index') }}" class="filters-form" id="employeesFilterForm">
            <div class="row g-3">
                <div class="col-md-3"><label for="full_name" class="form-label">{{ __('employees.filters.name') }}</label><input type="text" id="full_name" name="full_name" value="{{ $filters['full_name'] }}" class="form-control"></div>
                <div class="col-md-2"><label for="phone" class="form-label">{{ __('employees.filters.phone') }}</label><input type="text" id="phone" name="phone" value="{{ $filters['phone'] }}" class="form-control"></div>
                <div class="col-md-2"><label for="department" class="form-label">{{ __('employees.filters.department') }}</label><select id="department" name="department" class="form-select"><option value="">{{ __('employees.filters.all') }}</option>@foreach($departments as $department)<option value="{{ $department }}" @selected($filters['department'] === $department)>{{ $department }}</option>@endforeach</select></div>
                <div class="col-md-2"><label for="position" class="form-label">{{ __('employees.filters.position') }}</label><select id="position" name="position" class="form-select"><option value="">{{ __('employees.filters.all') }}</option>@foreach($positions as $position)<option value="{{ $position }}" @selected($filters['position'] === $position)>{{ $position }}</option>@endforeach</select></div>
                <div class="col-md-1"><label for="status" class="form-label">{{ __('employees.filters.status') }}</label><select id="status" name="status" class="form-select"><option value="">{{ __('employees.filters.all') }}</option>@foreach($statuses as $value => $label)<option value="{{ $value }}" @selected($filters['status'] === $value)>{{ $label }}</option>@endforeach</select></div>
                <div class="col-md-2"><label for="sort" class="form-label">{{ __('employees.filters.sort') }}</label><select id="sort" name="sort" class="form-select"><option value="latest" @selected($filters['sort'] === 'latest')>{{ __('employees.filters.latest') }}</option><option value="oldest" @selected($filters['sort'] === 'oldest')>{{ __('employees.filters.oldest') }}</option></select></div>
            </div>
            <div class="d-flex flex-wrap gap-2 mt-3">
                <button type="submit" class="btn btn-primary">{{ __('employees.actions.apply_filters') }}</button>
                <button type="button" class="btn btn-outline-secondary" data-reset-filters="#employeesFilterForm">{{ __('employees.actions.reset') }}</button>
            </div>
        </form>
    </div>

    <div class="content-card">
        <div class="table-responsive">
            <table class="table admin-table align-middle mb-0">
                <thead><tr><th>{{ __('employees.table.employee') }}</th><th>{{ __('employees.table.department') }}</th><th>{{ __('employees.table.position') }}</th><th>{{ __('employees.table.salary') }}</th><th>{{ __('employees.table.status') }}</th><th>{{ __('employees.table.linked_user') }}</th><th class="text-end">{{ __('employees.table.actions') }}</th></tr></thead>
                <tbody>
                    @forelse($employees as $employee)
                        <tr>
                            <td><div class="fw-semibold">{{ $employee->full_name }}</div><div class="text-muted small">{{ $employee->phone }}</div></td>
                            <td>{{ $employee->department }}</td>
                            <td>{{ $employee->position }}</td>
                            <td>{{ number_format((float) $employee->salary, 2) }}</td>
                            <td>@include('employees.partials.status-badge', ['employee' => $employee])</td>
                            <td>{{ $employee->user?->name ?? __('employees.messages.no_linked_account') }}</td>
                            <td class="text-end">
                                <div class="table-actions">
                                    <a
                                        href="{{ route('employees.show', $employee) }}"
                                        class="btn btn-sm btn-outline-primary action-icon-btn"
                                        title="{{ __('employees.actions.view') }}"
                                        aria-label="{{ __('employees.actions.view') }}"
                                    >
                                        <i class="bi bi-eye" aria-hidden="true"></i>
                                    </a>
                                    @if($canManageEmployees)
                                        <a
                                            href="{{ route('employees.edit', $employee) }}"
                                            class="btn btn-sm btn-outline-secondary action-icon-btn"
                                            title="{{ __('employees.actions.edit') }}"
                                            aria-label="{{ __('employees.actions.edit') }}"
                                        >
                                            <i class="bi bi-pencil-square" aria-hidden="true"></i>
                                        </a>
                                        <form method="POST" action="{{ route('employees.destroy', $employee) }}" data-confirm="{{ __('employees.messages.delete_confirm', ['name' => $employee->full_name]) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                type="submit"
                                                class="btn btn-sm btn-outline-danger action-icon-btn"
                                                title="{{ __('employees.actions.delete') }}"
                                                aria-label="{{ __('employees.actions.delete') }}"
                                                @disabled(! $employee->canBeDeletedSafely())
                                            >
                                                <i class="bi bi-trash" aria-hidden="true"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center py-5 text-muted">{{ __('employees.messages.empty') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $employees->links() }}</div>
    </div>
@endsection
