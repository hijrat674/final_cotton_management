@extends('layouts.app')

@section('title', __('accounting.journal_entries'))
@section('page-title', __('accounting.journal_entries'))
@section('page-subtitle', __('accounting.journal_entries_subtitle'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/accounting/reports.css') }}">
@endpush

@section('content')
    @include('accounting.reports.partials.nav')

    <div class="content-card mb-4">
        <form method="GET" action="{{ route('accounting.journal-entries.index') }}" class="filters-form">
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="reference_type" class="form-label">{{ __('accounting.reference_type') }}</label>
                    <select id="reference_type" name="reference_type" class="form-select">
                        <option value="">{{ __('accounting.all_types') }}</option>
                        @foreach($referenceTypes as $value => $label)
                            @php($referenceKey = 'accounting.references.' . $value)
                            <option value="{{ $value }}" @selected(($filters['reference_type'] ?? '') === $value)>{{ \Illuminate\Support\Facades\Lang::has($referenceKey) ? __($referenceKey) : $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3"><label for="date_from" class="form-label">{{ __('accounting.from') }}</label><input type="date" id="date_from" name="date_from" value="{{ $filters['date_from'] ?? '' }}" class="form-control"></div>
                <div class="col-md-3"><label for="date_to" class="form-label">{{ __('accounting.to') }}</label><input type="date" id="date_to" name="date_to" value="{{ $filters['date_to'] ?? '' }}" class="form-control"></div>
                <div class="col-md-2 d-flex align-items-end"><button type="submit" class="btn btn-primary w-100">{{ __('accounting.apply') }}</button></div>
            </div>
        </form>
    </div>

    <div class="content-card">
        <div class="table-responsive">
            <table class="table admin-table mb-0">
                <thead><tr><th>{{ __('accounting.date') }}</th><th>{{ __('accounting.reference') }}</th><th>{{ __('accounting.description') }}</th><th>{{ __('accounting.created_by') }}</th><th class="text-end">{{ __('accounting.actions') }}</th></tr></thead>
                <tbody>
                    @foreach($entries as $entry)
                        <tr>
                            <td>{{ $entry->entry_date->format('M d, Y') }}</td>
                            @php($referenceKey = 'accounting.references.' . $entry->reference_type)
                            <td>{{ \Illuminate\Support\Facades\Lang::has($referenceKey) ? __($referenceKey) : ($referenceTypes[$entry->reference_type] ?? ucfirst($entry->reference_type)) }} #{{ $entry->reference_id }}</td>
                            <td>{{ $entry->description }}</td>
                            <td>{{ $entry->creator->name }}</td>
                            <td class="text-end"><a href="{{ route('accounting.journal-entries.show', $entry) }}" class="btn btn-sm btn-outline-primary">{{ __('accounting.view') }}</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $entries->links() }}</div>
    </div>
@endsection
