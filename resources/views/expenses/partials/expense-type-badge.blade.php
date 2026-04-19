@php
    $name = $expenseType->name ?? '';
    $translationKey = 'expenses.types.' . $name;
    $label = \Illuminate\Support\Facades\Lang::has($translationKey)
        ? __($translationKey)
        : (\App\Models\ExpenseType::defaultOptions()[$name] ?? ucfirst($name));
    $class = match ($name) {
        \App\Models\ExpenseType::NAME_PRODUCTION => 'text-bg-primary',
        \App\Models\ExpenseType::NAME_TRANSPORT => 'badge-soft-warning',
        \App\Models\ExpenseType::NAME_SALARY => 'badge-soft-success',
        \App\Models\ExpenseType::NAME_MAINTENANCE => 'text-bg-secondary',
        \App\Models\ExpenseType::NAME_UTILITY => 'text-bg-info',
        default => 'text-bg-light',
    };
@endphp

<span class="badge {{ $class }}">{{ $label }}</span>
