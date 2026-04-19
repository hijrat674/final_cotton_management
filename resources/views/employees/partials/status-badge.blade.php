<span class="badge {{ $employee->isActive() ? 'badge-soft-success' : 'badge-soft-danger' }}">
    {{ __('employees.statuses.' . $employee->status) }}
</span>
