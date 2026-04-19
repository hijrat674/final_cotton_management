@extends('layouts.app')

@section('title', __('production.show.title'))
@section('page-title', __('production.show.page_title'))
@section('page-subtitle', __('production.show.page_subtitle'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/production/production-show.css') }}">
@endpush

@section('content')
    <nav aria-label="breadcrumb" class="breadcrumb-shell mb-4">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('production-stages.index') }}">{{ __('production.index.title') }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $stage->stage_name }}</li>
        </ol>
    </nav>

    <div class="row g-4">
        <div class="col-xl-8">
            <div class="content-card detail-card">
                <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 mb-4">
                    <div>
                        <h2 class="section-title mb-1">{{ $stage->stage_name }}</h2>
                        <p class="section-text mb-0">{{ __('production.show.posted_on', ['date' => $stage->stage_date->translatedFormat('M d, Y'), 'name' => $stage->handledByEmployee?->full_name ?? $stage->handler->name]) }}</p>
                    </div>
                    <div class="production-total-pill">
                        <i class="bi bi-box-arrow-in-right"></i>
                        <span>{{ number_format((float) $stage->input_quantity, 3) }} {{ strtoupper($stage->sourceInventoryItem->unit) }}</span>
                    </div>
                </div>

                <div class="detail-grid">
                    <div>
                        <span class="detail-label">{{ __('production.fields.source_material') }}</span>
                        <span class="detail-value">{{ $stage->sourceInventoryItem->product_name }}</span>
                    </div>
                    <div>
                        <span class="detail-label">{{ __('production.fields.input_quantity') }}</span>
                        <span class="detail-value">{{ number_format((float) $stage->input_quantity, 3) }} {{ strtoupper($stage->sourceInventoryItem->unit) }}</span>
                    </div>
                    <div>
                        <span class="detail-label">{{ __('production.fields.handled_by') }}</span>
                        <span class="detail-value">{{ $stage->handledByEmployee?->full_name ?? __('production.messages.not_assigned') }}</span>
                    </div>
                    <div>
                        <span class="detail-label">{{ __('production.fields.recorded_by_user') }}</span>
                        <span class="detail-value">{{ $stage->handler->name }}</span>
                    </div>
                    <div>
                        <span class="detail-label">{{ __('production.fields.total_outputs') }}</span>
                        <span class="detail-value">{{ number_format((float) $stage->total_output_quantity, 3) }}</span>
                    </div>
                </div>

                <div class="notes-panel mt-4">
                    <span class="detail-label">{{ __('production.fields.notes') }}</span>
                    <p class="mb-0">{{ $stage->notes ?: __('production.messages.no_notes') }}</p>
                </div>

                <div class="mt-4">
                    <h3 class="section-title mb-3">{{ __('production.show.outputs.title') }}</h3>
                    <div class="output-detail-list">
                        @foreach($stage->outputs as $output)
                            <div class="output-detail-card">
                                <div>
                                    <div class="output-detail-title">{{ $output->inventoryItem->product_name }}</div>
                                    <div class="text-muted small">{{ __('production.output_types.' . $output->output_type) }}</div>
                                </div>
                                <strong>{{ number_format((float) $output->quantity, 3) }} {{ strtoupper($output->unit) }}</strong>
                            </div>
                        @endforeach
                    </div>
                </div>

                @if($canManageStages)
                    <div class="d-flex flex-wrap gap-2 mt-4">
                        <a href="{{ route('production-stages.edit', $stage) }}" class="btn btn-primary">
                            <i class="bi bi-pencil-square me-1"></i>
                            {{ __('production.actions.edit_stage') }}
                        </a>
                        <form method="POST" action="{{ route('production-stages.destroy', $stage) }}" data-confirm="{{ __('production.messages.delete_confirm', ['name' => $stage->stage_name]) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger" @disabled($hasDownstreamDependencies)>
                                <i class="bi bi-trash me-1"></i>
                                {{ __('production.actions.delete_stage') }}
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>

        <div class="col-xl-4">
            <div class="content-card detail-card mb-4">
                <h3 class="section-title mb-3">{{ __('production.show.linked_expense.title') }}</h3>

                @if($productionExpense)
                    <div class="snapshot-list">
                        <div class="snapshot-row">
                            <span>{{ __('production.show.linked_expense.expense_title') }}</span>
                            <strong>{{ $productionExpense->title }}</strong>
                        </div>
                        <div class="snapshot-row">
                            <span>{{ __('production.show.linked_expense.amount') }}</span>
                            <strong>{{ number_format((float) $productionExpense->amount, 2) }}</strong>
                        </div>
                        <div class="snapshot-row">
                            <span>{{ __('production.show.linked_expense.expense_type') }}</span>
                            <strong>@include('expenses.partials.expense-type-badge', ['expenseType' => $productionExpense->expenseType])</strong>
                        </div>
                        <div class="snapshot-row">
                            <span>{{ __('production.show.linked_expense.created_by') }}</span>
                            <strong>{{ $productionExpense->creator->name }}</strong>
                        </div>
                    </div>

                    <a href="{{ route('expenses.show', $productionExpense) }}" class="btn btn-outline-primary w-100 mt-4">
                        <i class="bi bi-receipt me-1"></i>
                        {{ __('production.actions.view_expense_record') }}
                    </a>
                @else
                    <div class="alert alert-danger alert-modern mb-0">
                        {{ __('production.messages.missing_expense') }}
                    </div>
                @endif
            </div>

            <div class="content-card detail-card">
                <h3 class="section-title mb-3">{{ __('production.show.safety_status.title') }}</h3>

                @if($hasDownstreamDependencies)
                    <div class="alert alert-warning alert-modern mb-0">
                        {{ __('production.show.safety_status.locked') }}
                    </div>
                @else
                    <div class="alert alert-success alert-modern mb-0">
                        {{ __('production.show.safety_status.safe') }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="content-card detail-card mt-4">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-3">
            <div>
                <h3 class="section-title mb-1">{{ __('production.show.inventory_movements.title') }}</h3>
                <p class="section-text mb-0">{{ __('production.show.inventory_movements.text') }}</p>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table admin-table align-middle mb-0">
                <thead>
                    <tr>
                        <th>{{ __('production.show.inventory_movements.item') }}</th>
                        <th>{{ __('production.show.inventory_movements.type') }}</th>
                        <th>{{ __('production.show.inventory_movements.quantity_in') }}</th>
                        <th>{{ __('production.show.inventory_movements.quantity_out') }}</th>
                        <th>{{ __('production.show.inventory_movements.date') }}</th>
                        <th>{{ __('production.show.inventory_movements.created_by') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($inventoryMovements as $movement)
                        <tr>
                            <td>{{ $movement->inventoryItem->product_name }}</td>
                            <td>{{ __('production.transaction_types.' . $movement->transaction_type) }}</td>
                            <td>{{ number_format((float) $movement->quantity_in, 3) }}</td>
                            <td>{{ number_format((float) $movement->quantity_out, 3) }}</td>
                            <td>{{ $movement->transaction_date->translatedFormat('M d, Y') }}</td>
                            <td>{{ $movement->creator->name }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">{{ __('production.show.inventory_movements.empty') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
