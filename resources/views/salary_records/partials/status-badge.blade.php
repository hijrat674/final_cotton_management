@php
    $statusClass = match ($salaryRecord->payment_status) {
        \App\Models\SalaryRecord::STATUS_PAID => 'badge-soft-success',
        \App\Models\SalaryRecord::STATUS_PARTIAL => 'badge-soft-warning',
        default => 'badge-soft-danger',
    };
@endphp

<span class="badge {{ $statusClass }}">
    @php($statusKey = 'payroll.statuses.' . $salaryRecord->payment_status)
    {{ \Illuminate\Support\Facades\Lang::has($statusKey) ? __($statusKey) : (\App\Models\SalaryRecord::paymentStatusOptions()[$salaryRecord->payment_status] ?? ucfirst($salaryRecord->payment_status)) }}
</span>
