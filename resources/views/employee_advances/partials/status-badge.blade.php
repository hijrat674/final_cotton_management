<span class="badge {{ $employeeAdvance->status === \App\Models\EmployeeAdvance::STATUS_PENDING ? 'badge-soft-danger' : 'badge-soft-success' }}">
    @php($statusKey = 'advances.statuses.' . $employeeAdvance->status)
    {{ \Illuminate\Support\Facades\Lang::has($statusKey) ? __($statusKey) : (\App\Models\EmployeeAdvance::statusOptions()[$employeeAdvance->status] ?? ucfirst($employeeAdvance->status)) }}
</span>
