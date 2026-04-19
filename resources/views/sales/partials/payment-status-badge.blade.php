@php
    $statusClass = match ($sale->payment_status) {
        \App\Models\Sale::STATUS_PAID => 'badge-soft-success',
        \App\Models\Sale::STATUS_PARTIAL => 'badge-soft-warning',
        default => 'badge-soft-danger',
    };
@endphp

<span class="badge {{ $statusClass }}">
    {{ \App\Models\Sale::paymentStatusOptions()[$sale->payment_status] ?? ucfirst($sale->payment_status) }}
</span>
