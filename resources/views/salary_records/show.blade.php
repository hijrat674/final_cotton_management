@extends('layouts.app')

@section('title', __('Salary Record'))
@section('page-title', __('Salary Record'))
@section('page-subtitle', __('Salary detail, payment history, advance deductions, and outstanding payroll balance'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/payroll/payroll-payments.css') }}">
@endpush

@section('content')
    <nav aria-label="breadcrumb" class="breadcrumb-shell mb-4">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('salary-records.index') }}">Payroll</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $salaryRecord->employee->full_name }} - {{ $salaryRecord->period_label }}</li>
        </ol>
    </nav>

    <div class="payroll-hero-card mb-4">
        <div>
            <span class="payroll-kicker">Payroll Period</span>
            <h2 class="mb-1">{{ $salaryRecord->employee->full_name }}</h2>
            <p class="text-muted mb-0">{{ $salaryRecord->period_label }} - {{ $salaryRecord->employee->department }}</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            @include('salary_records.partials.status-badge', ['salaryRecord' => $salaryRecord])
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-3"><div class="summary-card"><div class="summary-card-label">Net Payable</div><div class="summary-card-value">{{ number_format((float) $salaryRecord->total_salary, 2) }}</div></div></div>
        <div class="col-md-3"><div class="summary-card"><div class="summary-card-label">Paid</div><div class="summary-card-value">{{ number_format((float) $salaryRecord->paid_amount, 2) }}</div></div></div>
        <div class="col-md-3"><div class="summary-card"><div class="summary-card-label">Remaining</div><div class="summary-card-value">{{ number_format((float) $salaryRecord->remaining_amount, 2) }}</div></div></div>
        <div class="col-md-3"><div class="summary-card"><div class="summary-card-label">Created By</div><div class="summary-card-value fs-4">{{ $salaryRecord->creator->name }}</div></div></div>
    </div>

    <div class="row g-4">
        <div class="col-xl-7">
            <div class="content-card mb-4">
                <h3 class="section-title mb-3">Salary Breakdown</h3>
                <div class="summary-breakdown">
                    <div><span>Basic Salary</span><strong>{{ number_format((float) $salaryRecord->basic_salary, 2) }}</strong></div>
                    <div><span>Bonus</span><strong>{{ number_format((float) $salaryRecord->bonus, 2) }}</strong></div>
                    <div><span>Deduction</span><strong>{{ number_format((float) $salaryRecord->deduction, 2) }}</strong></div>
                    <div><span>Gross Salary</span><strong>{{ number_format((float) $salaryRecord->gross_salary, 2) }}</strong></div>
                    <div><span>Advance Deduction</span><strong>{{ number_format((float) $salaryRecord->advance_deduction, 2) }}</strong></div>
                    <div><span>Net Payable</span><strong>{{ number_format((float) $salaryRecord->total_salary, 2) }}</strong></div>
                </div>
                <div class="notes-panel mt-4">
                    <span class="detail-label">Notes</span>
                    <p class="mb-0">{{ $salaryRecord->notes ?: 'No notes were added for this salary record.' }}</p>
                </div>
                <div class="content-card mt-4">
                    <h4 class="section-title mb-3">Employee Advance Snapshot</h4>
                    <div class="summary-breakdown">
                        <div><span>Deducted This Period</span><strong>{{ number_format((float) $salaryRecord->advance_deduction, 2) }}</strong></div>
                        <div><span>Current Pending Advances</span><strong>{{ number_format((float) $salaryRecord->employee->advances->where('status', \App\Models\EmployeeAdvance::STATUS_PENDING)->sum('amount'), 2) }}</strong></div>
                    </div>
                </div>
            </div>

            <div class="content-card">
                <h3 class="section-title mb-3">Payment History</h3>
                <div class="table-responsive">
                    <table class="table admin-table mb-0">
                        <thead><tr><th>Date</th><th>Method</th><th>Received By</th><th>Notes</th><th class="text-end">Amount</th></tr></thead>
                        <tbody>
                            @forelse($salaryRecord->payments as $payment)
                                <tr>
                                    <td>{{ $payment->payment_date->format('M d, Y') }}</td>
                                    <td>{{ $paymentMethods[$payment->payment_method] ?? ucfirst($payment->payment_method) }}</td>
                                    <td>{{ $payment->receiver->name }}</td>
                                    <td>{{ $payment->notes ?: 'No notes' }}</td>
                                    <td class="text-end">{{ number_format((float) $payment->amount, 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center py-4 text-muted">No salary payments recorded yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-xl-5">
            <div class="content-card">
                <h3 class="section-title mb-3">Record Salary Payment</h3>
                @if($canManagePayroll)
                    <form method="POST" action="{{ route('salary-payments.store') }}" data-salary-payment-form>
                        @csrf
                        <input type="hidden" name="salary_record_id" value="{{ $salaryRecord->id }}">
                        <input type="hidden" name="employee_id" value="{{ $salaryRecord->employee_id }}">
                        <input type="hidden" value="{{ number_format((float) $salaryRecord->remaining_amount, 2, '.', '') }}" data-salary-payment-remaining>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="payment_date" class="form-label">Payment Date</label>
                                <input type="date" id="payment_date" name="payment_date" value="{{ old('payment_date', now()->toDateString()) }}" class="form-control @error('payment_date') is-invalid @enderror" required>
                                @error('payment_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="amount" class="form-label">Amount</label>
                                <input type="number" step="0.01" min="0.01" max="{{ number_format((float) $salaryRecord->remaining_amount, 2, '.', '') }}" id="amount" name="amount" value="{{ old('amount') }}" class="form-control @error('amount') is-invalid @enderror" required data-salary-payment-amount>
                                @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label for="payment_method" class="form-label">Payment Method</label>
                                <select id="payment_method" name="payment_method" class="form-select @error('payment_method') is-invalid @enderror" required>
                                    <option value="">Select method</option>
                                    @foreach($paymentMethods as $value => $label)
                                        <option value="{{ $value }}" @selected(old('payment_method') === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('payment_method')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label for="notes" class="form-label">Notes</label>
                                <textarea id="notes" name="notes" rows="4" class="form-control @error('notes') is-invalid @enderror">{{ old('notes') }}</textarea>
                                @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="summary-breakdown mt-4">
                            <div><span>Remaining Before</span><strong>{{ number_format((float) $salaryRecord->remaining_amount, 2) }}</strong></div>
                            <div><span>Remaining After</span><strong data-salary-payment-after>{{ number_format((float) $salaryRecord->remaining_amount, 2) }}</strong></div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 mt-4" @disabled((float) $salaryRecord->remaining_amount <= 0)>Save Payment</button>
                    </form>
                @else
                    <div class="alert alert-info mb-0">Managers can review payroll but only admins can post salary payments.</div>
                @endif
            </div>
        </div>
    </div>
@endsection
