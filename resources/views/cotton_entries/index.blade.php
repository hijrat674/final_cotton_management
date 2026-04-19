@extends('layouts.app')

@section('title', __('cotton.index.title'))
@section('page-title', __('cotton.index.page_title'))
@section('page-subtitle', __('cotton.index.page_subtitle'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/cotton/cotton-index.css') }}">
@endpush

@section('content')
    <nav aria-label="breadcrumb" class="breadcrumb-shell mb-4">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ __('cotton.index.title') }}</li>
        </ol>
    </nav>

    <div class="content-card mb-4">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-3">
            <div>
                <h2 class="section-title mb-1">{{ __('cotton.index.register_title') }}</h2>
                <p class="section-text mb-0">{{ __('cotton.index.register_text') }}</p>
            </div>
            @if($canCreateEntries)
                <a href="{{ route('cotton-entries.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i>
                    {{ __('cotton.actions.new_entry') }}
                </a>
            @endif
        </div>

        <form method="GET" action="{{ route('cotton-entries.index') }}" class="filters-form" id="cottonEntriesFilterForm">
            <div class="row g-3">
                <div class="col-md-6 col-xl-3">
                    <label for="truck_number" class="form-label">{{ __('cotton.filters.truck_number') }}</label>
                    <input type="text" id="truck_number" name="truck_number" value="{{ $filters['truck_number'] }}" class="form-control" placeholder="{{ __('cotton.filters.search_truck_number') }}">
                </div>
                <div class="col-md-6 col-xl-3">
                    <label for="driver_name" class="form-label">{{ __('cotton.filters.driver_name') }}</label>
                    <input type="text" id="driver_name" name="driver_name" value="{{ $filters['driver_name'] }}" class="form-control" placeholder="{{ __('cotton.filters.search_driver_name') }}">
                </div>
                <div class="col-md-6 col-xl-2">
                    <label for="date_from" class="form-label">{{ __('cotton.filters.from') }}</label>
                    <input type="date" id="date_from" name="date_from" value="{{ $filters['date_from'] }}" class="form-control">
                </div>
                <div class="col-md-6 col-xl-2">
                    <label for="date_to" class="form-label">{{ __('cotton.filters.to') }}</label>
                    <input type="date" id="date_to" name="date_to" value="{{ $filters['date_to'] }}" class="form-control">
                </div>
                <div class="col-md-6 col-xl-2">
                    <label for="sort" class="form-label">{{ __('cotton.filters.sort') }}</label>
                    <select id="sort" name="sort" class="form-select">
                        <option value="latest" @selected($filters['sort'] === 'latest')>{{ __('cotton.filters.latest') }}</option>
                        <option value="oldest" @selected($filters['sort'] === 'oldest')>{{ __('cotton.filters.oldest') }}</option>
                    </select>
                </div>
            </div>

            <div class="d-flex flex-wrap gap-2 mt-3">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-funnel me-1"></i>
                    {{ __('cotton.actions.apply_filters') }}
                </button>
                <button type="button" class="btn btn-outline-secondary" data-reset-filters="#cottonEntriesFilterForm">
                    <i class="bi bi-arrow-counterclockwise me-1"></i>
                    {{ __('cotton.actions.reset') }}
                </button>
            </div>
        </form>
    </div>

    <div class="content-card">
        <div class="table-responsive">
            <table class="table admin-table align-middle mb-0">
                <thead>
                    <tr>
                        <th>{{ __('cotton.table.truck') }}</th>
                        <th>{{ __('cotton.table.driver') }}</th>
                        <th>{{ __('cotton.table.entry_date') }}</th>
                        <th>{{ __('cotton.table.material_category') }}</th>
                        <th>{{ __('cotton.table.net_weight') }}</th>
                        <th>{{ __('cotton.table.created_by') }}</th>
                        <th class="text-end">{{ __('cotton.table.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($entries as $entry)
                        <tr>
                            <td class="fw-semibold">{{ $entry->truck_number }}</td>
                            <td>
                                <div>{{ $entry->driver_name }}</div>
                                <div class="text-muted small">{{ $entry->driver_phone ?: __('cotton.messages.no_phone') }}</div>
                            </td>
                            <td>{{ $entry->entry_date->format('M d, Y') }}</td>
                            <td>{{ $entry->inventoryItem->product_name }}</td>
                            <td class="fw-semibold">{{ number_format((float) $entry->net_weight, 3) }}</td>
                            <td>{{ $entry->creator->name }}</td>
                            <td class="text-end">
                                <div class="table-actions">
                                    <a href="{{ route('cotton-entries.show', $entry) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if($canManageEntries)
                                        <a href="{{ route('cotton-entries.edit', $entry) }}" class="btn btn-sm btn-outline-secondary">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <form method="POST" action="{{ route('cotton-entries.destroy', $entry) }}" data-confirm="{{ __('cotton.messages.delete_confirm', ['truck' => $entry->truck_number]) }}">
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
                            <td colspan="7" class="text-center py-5 text-muted">{{ __('cotton.messages.empty') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $entries->links() }}
        </div>
    </div>
@endsection
