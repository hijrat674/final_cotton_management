@extends('layouts.app')

@section('title', __('accounting.journal_entry_number', ['id' => $entry->id]))
@section('page-title', __('accounting.journal_entry'))
@section('page-subtitle', __('accounting.journal_entry_subtitle'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/accounting/reports.css') }}">
@endpush

@section('content')
    @include('accounting.reports.partials.nav')

    <div class="content-card mb-4">
        <div class="row g-3">
            <div class="col-md-3"><strong>{{ __('accounting.date') }}</strong><div>{{ $entry->entry_date->format('M d, Y') }}</div></div>
            <div class="col-md-3">@php($referenceKey = 'accounting.references.' . $entry->reference_type)<strong>{{ __('accounting.reference') }}</strong><div>{{ \Illuminate\Support\Facades\Lang::has($referenceKey) ? __($referenceKey) : (\App\Models\JournalEntry::referenceTypeOptions()[$entry->reference_type] ?? ucfirst($entry->reference_type)) }} #{{ $entry->reference_id }}</div></div>
            <div class="col-md-3"><strong>{{ __('accounting.created_by') }}</strong><div>{{ $entry->creator->name }}</div></div>
            <div class="col-md-3"><strong>{{ __('accounting.description') }}</strong><div>{{ $entry->description }}</div></div>
        </div>
    </div>

    <div class="content-card">
        <div class="table-responsive">
            <table class="table admin-table mb-0">
                <thead><tr><th>{{ __('accounting.account') }}</th><th>{{ __('accounting.description') }}</th><th class="text-end">{{ __('accounting.debit') }}</th><th class="text-end">{{ __('accounting.credit') }}</th></tr></thead>
                <tbody>
                    @foreach($entry->lines as $line)
                        <tr>
                            <td>{{ $line->account->code }} - {{ $line->account->account_name }}</td>
                            <td>{{ $line->description }}</td>
                            <td class="text-end">{{ number_format((float) $line->debit, 2) }}</td>
                            <td class="text-end">{{ number_format((float) $line->credit, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
