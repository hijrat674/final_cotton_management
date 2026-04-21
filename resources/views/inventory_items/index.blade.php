@extends('layouts.app')

@section('title', __('inventory.index.title'))
@section('page-title', __('inventory.index.page_title'))
@section('page-subtitle', __('inventory.index.page_subtitle'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/inventory/inventory-items-index.css') }}">
@endpush

@section('content')
    <nav aria-label="breadcrumb" class="breadcrumb-shell mb-4">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ __('inventory.index.title') }}</li>
        </ol>
    </nav>

    <section class="summary-showcase inventory-summary-section content-card mb-4">
        <div class="users-summary-header">
            <div>
                <h2 class="users-summary-title">{{ __('inventory.summary.title') }}</h2>
                <p class="users-summary-text mb-0">{{ __('inventory.summary.text') }}</p>
            </div>
        </div>

        <div class="row g-4 users-summary-grid">
            <div class="col-sm-6 col-xl-3">
                <article class="users-summary-card inventory-summary-card-total">
                    <span class="users-summary-icon" aria-hidden="true">
                        <i class="bi bi-box-seam-fill"></i>
                    </span>
                    <div class="users-summary-copy inventory-summary-copy">
                        <div class="inventory-summary-heading">
                            <span class="users-summary-label">{{ __('inventory.summary.total_items') }}</span>
                        </div>
                        <div class="users-summary-value-wrap inventory-summary-value-wrap">
                            <strong class="users-summary-value inventory-summary-value">{{ $summary['total_items'] }}</strong>
                        </div>
                        <span class="users-summary-meta inventory-summary-meta">{{ __('inventory.summary.total_items_text') }}</span>
                    </div>
                </article>
            </div>
            <div class="col-sm-6 col-xl-3">
                <article class="users-summary-card inventory-summary-card-balance">
                    <span class="users-summary-icon" aria-hidden="true">
                        <i class="bi bi-bar-chart-line-fill"></i>
                    </span>
                    <div class="users-summary-copy inventory-summary-copy">
                        <div class="inventory-summary-heading">
                            <span class="users-summary-label">{{ __('inventory.summary.total_stock_quantity') }}</span>
                        </div>
                        <div class="users-summary-value-wrap inventory-summary-value-wrap">
                            <strong class="users-summary-value inventory-summary-value">{{ number_format($summary['total_stock_quantity'], 3) }}</strong>
                        </div>
                        <span class="users-summary-meta inventory-summary-meta">{{ __('inventory.summary.total_stock_quantity_text') }}</span>
                    </div>
                </article>
            </div>
            <div class="col-sm-6 col-xl-3">
                <article class="users-summary-card inventory-summary-card-low">
                    <span class="users-summary-icon" aria-hidden="true">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                    </span>
                    <div class="users-summary-copy inventory-summary-copy">
                        <div class="inventory-summary-heading">
                            <span class="users-summary-label">{{ __('inventory.summary.low_stock_items') }}</span>
                        </div>
                        <div class="users-summary-value-wrap inventory-summary-value-wrap">
                            <strong class="users-summary-value inventory-summary-value">{{ $summary['low_stock_count'] }}</strong>
                        </div>
                        <span class="users-summary-meta inventory-summary-meta">{{ __('inventory.summary.low_stock_items_text') }}</span>
                    </div>
                </article>
            </div>
            <div class="col-sm-6 col-xl-3">
                <article class="users-summary-card inventory-summary-card-empty">
                    <span class="users-summary-icon" aria-hidden="true">
                        <i class="bi bi-x-octagon-fill"></i>
                    </span>
                    <div class="users-summary-copy inventory-summary-copy">
                        <div class="inventory-summary-heading">
                            <span class="users-summary-label">{{ __('inventory.summary.out_of_stock') }}</span>
                        </div>
                        <div class="users-summary-value-wrap inventory-summary-value-wrap">
                            <strong class="users-summary-value inventory-summary-value">{{ $summary['out_of_stock_count'] }}</strong>
                        </div>
                        <span class="users-summary-meta inventory-summary-meta">{{ __('inventory.summary.out_of_stock_text') }}</span>
                    </div>
                </article>
            </div>
        </div>

        @php
            $inventoryItemIcons = [
                'Raw Cotton' => ['icon' => 'bi-flower1', 'class' => 'inventory-item-card-raw-cotton'],
                'Processed Cotton' => ['icon' => 'bi-box-seam-fill', 'class' => 'inventory-item-card-processed-cotton'],
                'Kernel' => ['icon' => 'bi-circle-fill', 'class' => 'inventory-item-card-kernel'],
                'Cotton Oil' => ['icon' => 'bi-droplet-fill', 'class' => 'inventory-item-card-cotton-oil'],
                'Cotton Meal' => ['icon' => 'bi-archive-fill', 'class' => 'inventory-item-card-cotton-meal'],
                'Waste' => ['icon' => 'bi-trash3-fill', 'class' => 'inventory-item-card-waste'],
                'Shell' => ['icon' => 'bi-layers-fill', 'class' => 'inventory-item-card-shell'],
            ];
            $inventoryTypeIcons = [
                'raw_material' => ['icon' => 'bi-box-fill', 'class' => 'inventory-item-card-raw-material'],
                'semi_finished' => ['icon' => 'bi-layers-fill', 'class' => 'inventory-item-card-semi-finished'],
                'finished_product' => ['icon' => 'bi-box-seam-fill', 'class' => 'inventory-item-card-finished-product'],
                'byproduct' => ['icon' => 'bi-droplet-half', 'class' => 'inventory-item-card-byproduct'],
                'waste' => ['icon' => 'bi-exclamation-octagon-fill', 'class' => 'inventory-item-card-waste'],
            ];
        @endphp

        @if($summaryItems->isNotEmpty())
            <div class="inventory-item-summary-head">
                <div>
                    <h3 class="users-summary-title inventory-item-summary-title">{{ __('inventory.summary.by_item_title') }}</h3>
                    <p class="users-summary-text mb-0">{{ __('inventory.summary.by_item_text') }}</p>
                </div>
            </div>

            <div class="row g-4 users-summary-grid mt-1">
                @foreach($summaryItems as $summaryItem)
                    @php
                        $iconConfig = $inventoryItemIcons[$summaryItem->product_name]
                            ?? $inventoryTypeIcons[$summaryItem->product_type]
                            ?? ['icon' => 'bi-box-fill', 'class' => 'inventory-item-card-default'];
                    @endphp
                    <div class="col-sm-6 col-xl-3">
                        <article class="users-summary-card inventory-item-card {{ $iconConfig['class'] }}">
                            <span class="users-summary-icon" aria-hidden="true">
                                <i class="bi {{ $iconConfig['icon'] }}"></i>
                            </span>
                            <div class="users-summary-copy inventory-summary-copy">
                                <div class="inventory-card-head inventory-summary-heading">
                                    <span class="users-summary-label inventory-item-label">{{ $summaryItem->product_name }}</span>
                                </div>
                                <div class="users-summary-value-wrap inventory-stock-wrap inventory-summary-value-wrap">
                                    <strong class="users-summary-value inventory-stock-line">
                                        <span class="inventory-stock-value">{{ number_format($summaryItem->current_stock, 3) }}</span>
                                        <span class="inventory-stock-unit">{{ strtoupper($summaryItem->unit) }}</span>
                                    </strong>
                                </div>
                                <div class="inventory-card-foot inventory-summary-meta-wrap">
                                    <span class="users-summary-meta inventory-summary-meta">{{ __($productTypes[$summaryItem->product_type] ?? ucfirst(str_replace('_', ' ', $summaryItem->product_type))) }}</span>
                                </div>
                            </div>
                        </article>
                    </div>
                @endforeach
            </div>
        @endif
    </section>

    <div class="content-card mb-4">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-3">
            <div>
                <h2 class="section-title mb-1">{{ __('inventory.index.catalog_title') }}</h2>
                <p class="section-text mb-0">{{ __('inventory.index.catalog_text') }}</p>
            </div>
            @if($canManageItems)
                <a
                    href="{{ route('inventory-items.create') }}"
                    class="btn btn-primary"
                    data-modal-open
                    data-modal-size="xl"
                    data-modal-title="{{ __('inventory.actions.create') }}"
                >{{ __('inventory.actions.create') }}</a>
            @endif
        </div>

        <form method="GET" action="{{ route('inventory-items.index') }}" class="filters-form" id="inventoryItemsFilterForm">
            <div class="row g-3">
                <div class="col-md-6 col-xl-4">
                    <label for="product_name" class="form-label">{{ __('inventory.filters.search_product') }}</label>
                    <input type="text" id="product_name" name="product_name" value="{{ $filters['product_name'] }}" class="form-control" placeholder="{{ __('inventory.filters.search_by_product_name') }}">
                </div>
                <div class="col-md-6 col-xl-3">
                    <label for="product_type" class="form-label">{{ __('inventory.filters.product_type') }}</label>
                    <select id="product_type" name="product_type" class="form-select">
                        <option value="">{{ __('inventory.filters.all_types') }}</option>
                        @foreach($productTypes as $value => $label)
                            <option value="{{ $value }}" @selected($filters['product_type'] === $value)>{{ __($label) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 col-xl-3">
                    <label for="unit" class="form-label">{{ __('inventory.filters.unit') }}</label>
                    <select id="unit" name="unit" class="form-select">
                        <option value="">{{ __('inventory.filters.all_units') }}</option>
                        @foreach($units as $value => $label)
                            <option value="{{ $value }}" @selected($filters['unit'] === $value)>{{ __($label) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 col-xl-2">
                    <label for="sort" class="form-label">{{ __('inventory.filters.sort') }}</label>
                    <select id="sort" name="sort" class="form-select">
                        <option value="latest" @selected($filters['sort'] === 'latest')>{{ __('inventory.filters.latest') }}</option>
                        <option value="oldest" @selected($filters['sort'] === 'oldest')>{{ __('inventory.filters.oldest') }}</option>
                    </select>
                </div>
            </div>

            <div class="d-flex flex-wrap gap-2 mt-3">
                <button type="submit" class="btn btn-primary">{{ __('inventory.actions.apply_filters') }}</button>
                <button type="button" class="btn btn-outline-secondary" data-reset-filters="#inventoryItemsFilterForm">{{ __('inventory.actions.reset') }}</button>
            </div>
        </form>
    </div>

    <div class="content-card">
        <div class="table-responsive">
            <table class="table admin-table align-middle mb-0">
                <thead>
                    <tr>
                        <th>{{ __('inventory.table.product') }}</th>
                        <th>{{ __('inventory.table.type') }}</th>
                        <th>{{ __('inventory.table.unit') }}</th>
                        <th>{{ __('inventory.table.minimum_stock') }}</th>
                        <th>{{ __('inventory.table.current_stock') }}</th>
                        <th>{{ __('inventory.table.status') }}</th>
                        <th class="text-end">{{ __('inventory.table.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $item->product_name }}</div>
                                <div class="text-muted small">{{ $item->product_code ?: __('inventory.messages.no_product_code') }}</div>
                            </td>
                            <td><span class="badge text-bg-light">{{ __($productTypes[$item->product_type] ?? ucfirst(str_replace('_', ' ', $item->product_type))) }}</span></td>
                            <td>{{ strtoupper($item->unit) }}</td>
                            <td>{{ number_format((float) $item->minimum_stock, 3) }}</td>
                            <td class="fw-semibold">{{ number_format($item->current_stock, 3) }}</td>
                            <td>@include('inventory_items.partials.stock-status-badge', ['item' => $item])</td>
                            <td class="text-end">
                                <div class="table-actions">
                                    <a
                                        href="{{ route('inventory-items.show', $item) }}"
                                        class="btn btn-sm btn-outline-primary action-icon-btn"
                                        title="{{ __('inventory.actions.view') }}"
                                        aria-label="{{ __('inventory.actions.view') }}"
                                        data-modal-open
                                        data-modal-size="lg"
                                        data-modal-title="{{ __('inventory.actions.view') }}"
                                    >
                                        <i class="bi bi-eye" aria-hidden="true"></i>
                                    </a>
                                    @if($canManageItems)
                                        <a
                                            href="{{ route('inventory-items.edit', $item) }}"
                                            class="btn btn-sm btn-outline-secondary action-icon-btn"
                                            title="{{ __('inventory.actions.edit') }}"
                                            aria-label="{{ __('inventory.actions.edit') }}"
                                            data-modal-open
                                            data-modal-size="xl"
                                            data-modal-title="{{ __('inventory.actions.edit') }}"
                                        >
                                            <i class="bi bi-pencil-square" aria-hidden="true"></i>
                                        </a>
                                        <form method="POST" action="{{ route('inventory-items.destroy', $item) }}" data-confirm="{{ __('inventory.messages.delete_confirm', ['name' => $item->product_name]) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                type="submit"
                                                class="btn btn-sm btn-outline-danger action-icon-btn"
                                                title="{{ __('inventory.actions.delete') }}"
                                                aria-label="{{ __('inventory.actions.delete') }}"
                                                @disabled($item->transactions_count > 0)
                                            >
                                                <i class="bi bi-trash" aria-hidden="true"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">{{ __('inventory.messages.empty') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $items->links() }}
        </div>
    </div>
@endsection
