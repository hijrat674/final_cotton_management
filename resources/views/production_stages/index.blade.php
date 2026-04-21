@extends('layouts.app')

@section('title', __('production.index.title'))
@section('page-title', __('production.index.page_title'))
@section('page-subtitle', __('production.index.page_subtitle'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/production/production-index.css') }}">
@endpush

@section('content')
    <nav aria-label="breadcrumb" class="breadcrumb-shell mb-4">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ __('production.index.title') }}</li>
        </ol>
    </nav>

    <div class="content-card mb-4">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-3">
            <div>
                <h2 class="section-title mb-1">{{ __('production.index.register_title') }}</h2>
            </div>
            @if($canCreateStages)
                <a href="{{ route('production-stages.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i>
                    {{ __('production.actions.new_stage') }}
                </a>
            @endif
        </div>

        <form method="GET" action="{{ route('production-stages.index') }}" class="filters-form" id="productionStagesFilterForm">
            <div class="row g-3">
                <div class="col-md-5">
                    <label for="stage_name" class="form-label">{{ __('production.filters.stage_name') }}</label>
                    <input type="text" id="stage_name" name="stage_name" value="{{ $filters['stage_name'] }}" class="form-control" placeholder="{{ __('production.filters.search_stage_name') }}">
                </div>
                <div class="col-md-3">
                    <label for="stage_date" class="form-label">{{ __('production.filters.stage_date') }}</label>
                    <input type="date" id="stage_date" name="stage_date" value="{{ $filters['stage_date'] }}" class="form-control">
                </div>
                <div class="col-md-2">
                    <label for="sort" class="form-label">{{ __('production.filters.sort') }}</label>
                    <select id="sort" name="sort" class="form-select">
                        <option value="latest" @selected($filters['sort'] === 'latest')>{{ __('production.filters.latest') }}</option>
                        <option value="oldest" @selected($filters['sort'] === 'oldest')>{{ __('production.filters.oldest') }}</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-funnel me-1"></i>
                        {{ __('production.actions.filter') }}
                    </button>
                </div>
            </div>

            <div class="d-flex flex-wrap gap-2 mt-3">
                <button type="button" class="btn btn-outline-secondary" data-reset-filters="#productionStagesFilterForm">
                    <i class="bi bi-arrow-counterclockwise me-1"></i>
                    {{ __('production.actions.reset') }}
                </button>
            </div>
        </form>
    </div>

    <div class="content-card">
        <div class="table-responsive">
            <table class="table admin-table align-middle mb-0">
                <thead>
                    <tr>
                        <th>{{ __('production.table.stage') }}</th>
                        <th>{{ __('production.table.date') }}</th>
                        <th>{{ __('production.table.input_material') }}</th>
                        <th>{{ __('production.table.input_qty') }}</th>
                        <th>{{ __('production.table.output_summary') }}</th>
                        <th>{{ __('production.table.user') }}</th>
                        <th>{{ __('production.table.employee') }}</th>
                        <th class="text-end">{{ __('production.table.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stages as $stage)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $stage->stage_name }}</div>
                                <div class="text-muted small">{{ __('production.messages.stage_number', ['id' => $stage->id]) }}</div>
                            </td>
                            <td>{{ $stage->stage_date->format('M d, Y') }}</td>
                            <td>
                                <div>{{ $stage->sourceInventoryItem->product_name }}</div>
                                <div class="text-muted small">{{ strtoupper($stage->sourceInventoryItem->unit) }}</div>
                            </td>
                            <td class="fw-semibold">{{ number_format((float) $stage->input_quantity, 3) }}</td>
                            <td>
                                <div class="output-summary-list">
                                    @foreach($stage->outputs as $output)
                                        <span class="output-summary-pill">
                                            {{ $output->inventoryItem->product_name }}:
                                            {{ number_format((float) $output->quantity, 3) }}
                                            {{ strtoupper($output->unit) }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                            <td>{{ $stage->handler->name }}</td>
                            <td>{{ $stage->handledByEmployee?->full_name ?? __('production.messages.not_assigned') }}</td>
                            <td class="text-end">
                                <div class="table-actions">
                                    <a href="{{ route('production-stages.show', $stage) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if($canManageStages)
                                        <a href="{{ route('production-stages.edit', $stage) }}" class="btn btn-sm btn-outline-secondary">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <form method="POST" action="{{ route('production-stages.destroy', $stage) }}" data-confirm="{{ __('production.messages.delete_confirm', ['name' => $stage->stage_name]) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">{{ __('production.messages.empty') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $stages->links() }}
        </div>
    </div>
@endsection
