@extends('layouts.app')

@section('title', __('Employee Advance'))
@section('page-title', __('Employee Advance'))
@section('page-subtitle', __('Advance detail, deduction status, and employee visibility'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/advance/advance-form.css') }}">
@endpush

@section('content')
    <nav aria-label="breadcrumb" class="breadcrumb-shell mb-4">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('employee-advances.index') }}">Employee Advances</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $employeeAdvance->employee->full_name }}</li>
        </ol>
    </nav>

    <div class="payroll-hero-card mb-4">
        <div>
            <span class="payroll-kicker">Employee Advance</span>
            <h2 class="mb-1">{{ $employeeAdvance->employee->full_name }}</h2>
            <p class="text-muted mb-0">{{ $employeeAdvance->advance_date->format('F d, Y') }} - {{ $employeeAdvance->employee->department }}</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            @include('employee_advances.partials.status-badge', ['employeeAdvance' => $employeeAdvance])
            @if($canManageAdvances)
                <a href="{{ route('employee-advances.create') }}" class="btn btn-outline-primary">New Advance</a>
            @endif
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-4"><div class="summary-card"><div class="summary-card-label">Amount</div><div class="summary-card-value">{{ number_format((float) $employeeAdvance->amount, 2) }}</div></div></div>
        <div class="col-lg-4"><div class="summary-card"><div class="summary-card-label">Status</div><div class="summary-card-value fs-4">{{ ucfirst($employeeAdvance->status) }}</div></div></div>
        <div class="col-lg-4"><div class="summary-card"><div class="summary-card-label">Created By</div><div class="summary-card-value fs-4">{{ $employeeAdvance->creator->name }}</div></div></div>
    </div>

    <div class="content-card mt-4">
        <h3 class="section-title mb-3">Advance Details</h3>
        <div class="summary-breakdown">
            <div><span>Employee</span><strong>{{ $employeeAdvance->employee->full_name }}</strong></div>
            <div><span>Linked User</span><strong>{{ $employeeAdvance->employee->user?->name ?? 'No linked account' }}</strong></div>
            <div><span>Department</span><strong>{{ $employeeAdvance->employee->department }}</strong></div>
            <div><span>Position</span><strong>{{ $employeeAdvance->employee->position }}</strong></div>
        </div>

        <div class="notes-panel mt-4">
            <span class="detail-label">Reason</span>
            <p class="mb-0">{{ $employeeAdvance->reason ?: 'No reason was added for this advance.' }}</p>
        </div>
    </div>
@endsection
