@extends('layouts.app')

@section('title', __('cotton.show.title'))
@section('page-title', __('cotton.show.page_title'))
@section('page-subtitle', __('cotton.show.page_subtitle'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/cotton/cotton-show.css') }}">
@endpush

@section('content')
    <nav aria-label="breadcrumb" class="breadcrumb-shell mb-4">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('cotton-entries.index') }}">{{ __('cotton.index.title') }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $entry->truck_number }}</li>
        </ol>
    </nav>

    <div class="row g-4">
        <div class="col-xl-8">
            <div class="content-card detail-card">
                <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 mb-4">
                    <div>
                        <h2 class="section-title mb-1">{{ $entry->truck_number }}</h2>
                        <p class="section-text mb-0">{{ __('cotton.show.recorded_on', ['date' => $entry->entry_date->translatedFormat('M d, Y')]) }}</p>
                    </div>
                    <div class="cotton-net-pill">
                        <i class="bi bi-arrow-down-circle"></i>
                        <span>{{ number_format((float) $entry->net_weight, 3) }}</span>
                    </div>
                </div>

                <div class="detail-grid">
                    <div>
                        <span class="detail-label">{{ __('cotton.fields.driver_name') }}</span>
                        <span class="detail-value">{{ $entry->driver_name }}</span>
                    </div>
                    <div>
                        <span class="detail-label">{{ __('cotton.fields.driver_phone') }}</span>
                        <span class="detail-value">{{ $entry->driver_phone ?: __('cotton.messages.not_provided') }}</span>
                    </div>
                    <div>
                        <span class="detail-label">{{ __('cotton.fields.material_category') }}</span>
                        <span class="detail-value">{{ $entry->inventoryItem->product_name }}</span>
                    </div>
                    <div>
                        <span class="detail-label">{{ __('cotton.fields.created_by') }}</span>
                        <span class="detail-value">{{ $entry->creator->name }}</span>
                    </div>
                    <div>
                        <span class="detail-label">{{ __('cotton.fields.gross_weight') }}</span>
                        <span class="detail-value">{{ number_format((float) $entry->gross_weight, 3) }}</span>
                    </div>
                    <div>
                        <span class="detail-label">{{ __('cotton.fields.tare_weight') }}</span>
                        <span class="detail-value">{{ number_format((float) $entry->tare_weight, 3) }}</span>
                    </div>
                    <div>
                        <span class="detail-label">{{ __('cotton.fields.net_weight') }}</span>
                        <span class="detail-value">{{ number_format((float) $entry->net_weight, 3) }}</span>
                    </div>
                    <div>
                        <span class="detail-label">{{ __('cotton.fields.inventory_reference') }}</span>
                        <span class="detail-value">{{ $entry->inventoryTransaction ? __('cotton.show.transaction_reference', ['id' => $entry->inventoryTransaction->id]) : __('cotton.messages.missing') }}</span>
                    </div>
                </div>

                <div class="notes-panel mt-4">
                    <span class="detail-label">{{ __('cotton.fields.notes') }}</span>
                    <p class="mb-0">{{ $entry->notes ?: __('cotton.messages.no_notes') }}</p>
                </div>

                <div class="d-flex flex-wrap gap-2 mt-4">
                    @include('layouts.partials.back-button', ['fallback' => route('cotton-entries.index')])
                    @if($canManageEntries)
                        <a href="{{ route('cotton-entries.edit', $entry) }}" class="btn btn-primary">
                            <i class="bi bi-pencil-square me-1"></i>
                            {{ __('cotton.actions.edit_entry') }}
                        </a>
                        <form method="POST" action="{{ route('cotton-entries.destroy', $entry) }}" data-confirm="{{ __('cotton.messages.delete_confirm_detail', ['truck' => $entry->truck_number]) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger">
                                <i class="bi bi-trash me-1"></i>
                                {{ __('cotton.actions.delete_entry') }}
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="content-card detail-card">
                <h3 class="section-title mb-3">{{ __('cotton.show.linked_transaction.title') }}</h3>

                @if($entry->inventoryTransaction)
                    <div class="snapshot-list">
                        <div class="snapshot-row">
                            <span>{{ __('cotton.show.linked_transaction.transaction_type') }}</span>
                            <strong>{{ __('cotton.show.linked_transaction.types.' . $entry->inventoryTransaction->transaction_type) }}</strong>
                        </div>
                        <div class="snapshot-row">
                            <span>{{ __('cotton.show.linked_transaction.reference') }}</span>
                            <strong>{{ __('cotton.show.linked_transaction.references.' . $entry->inventoryTransaction->reference_type) }} #{{ $entry->inventoryTransaction->reference_id }}</strong>
                        </div>
                        <div class="snapshot-row">
                            <span>{{ __('cotton.show.linked_transaction.quantity_in') }}</span>
                            <strong>{{ number_format((float) $entry->inventoryTransaction->quantity_in, 3) }}</strong>
                        </div>
                        <div class="snapshot-row">
                            <span>{{ __('cotton.show.linked_transaction.transaction_date') }}</span>
                            <strong>{{ $entry->inventoryTransaction->transaction_date->translatedFormat('M d, Y') }}</strong>
                        </div>
                    </div>

                    <a href="{{ route('inventory-transactions.show', $entry->inventoryTransaction) }}" class="btn btn-outline-primary w-100 mt-4">
                        <i class="bi bi-box-arrow-up-right me-1"></i>
                        {{ __('cotton.actions.view_inventory_transaction') }}
                    </a>
                @else
                    <div class="alert alert-danger alert-modern mb-0">
                        {{ __('cotton.messages.missing_inventory_transaction') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
