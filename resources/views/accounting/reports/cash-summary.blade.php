@extends('layouts.app')

@section('title', __('accounting.cash_summary'))
@section('page-title', __('accounting.cash_summary'))
@section('page-subtitle', __('accounting.cash_summary_subtitle'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/accounting/reports.css') }}">
@endpush

@section('content')
    @include('accounting.reports.partials.nav')

    <div class="row g-4 mb-4">
        <div class="col-md-4"><div class="summary-card"><div class="summary-card-label">{{ __('accounting.cash_in') }}</div><div class="summary-card-value">{{ number_format($report['total_cash_in'], 2) }}</div></div></div>
        <div class="col-md-4"><div class="summary-card"><div class="summary-card-label">{{ __('accounting.cash_out') }}</div><div class="summary-card-value">{{ number_format($report['total_cash_out'], 2) }}</div></div></div>
        <div class="col-md-4"><div class="summary-card"><div class="summary-card-label">{{ __('accounting.cash_balance') }}</div><div class="summary-card-value">{{ number_format($report['cash_balance'], 2) }}</div></div></div>
    </div>

    <div class="content-card">
        <h2 class="section-title mb-3">{{ __('accounting.cash_ledger') }}</h2>
        <div class="table-responsive">
            <table class="table admin-table mb-0">
                <thead>
                    <tr>
                        <th>{{ __('accounting.date') }}</th>
                        <th>{{ __('accounting.reference') }}</th>
                        <th>{{ __('accounting.description') }}</th>
                        <th class="text-end">{{ __('accounting.cash_in') }}</th>
                        <th class="text-end">{{ __('accounting.cash_out') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($report['transactions'] as $line)
                        <tr>
                            <td>{{ $line->journalEntry->entry_date->format('M d, Y') }}</td>
                            @php($referenceKey = 'accounting.references.' . $line->journalEntry->reference_type)
                            <td>{{ \Illuminate\Support\Facades\Lang::has($referenceKey) ? __($referenceKey) : (\App\Models\JournalEntry::referenceTypeOptions()[$line->journalEntry->reference_type] ?? ucfirst($line->journalEntry->reference_type)) }} #{{ $line->journalEntry->reference_id }}</td>
                            <td>{{ $line->description ?: $line->journalEntry->description }}</td>
                            <td class="text-end">{{ number_format((float) $line->debit, 2) }}</td>
                            <td class="text-end">{{ number_format((float) $line->credit, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center py-4 text-muted">{{ __('accounting.no_cash_postings_found') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
