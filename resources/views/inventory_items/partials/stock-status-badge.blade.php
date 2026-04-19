@php
    $status = $status ?? $item->stock_status;

    $classes = match ($status) {
        \App\Models\InventoryItem::STATUS_OUT_OF_STOCK => 'badge-soft-danger',
        \App\Models\InventoryItem::STATUS_LOW_STOCK => 'badge-soft-warning',
        default => 'badge-soft-success',
    };

    $labels = \App\Models\InventoryItem::stockStatusOptions();
@endphp

<span class="badge {{ $classes }}">
    {{ $labels[$status] ?? ucfirst(str_replace('_', ' ', $status)) }}
</span>
