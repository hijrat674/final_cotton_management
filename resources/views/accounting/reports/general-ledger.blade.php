@extends('layouts.app')

@section('title', __('accounting.general_ledger'))
@section('page-title', __('accounting.general_ledger'))
@section('page-subtitle', __('accounting.general_ledger_subtitle'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/accounting/reports.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/accounting/ledger.css') }}">
@endpush

@section('content')
    @include('accounting.reports.partials.nav')

    <div class="content-card mb-4">
        <form method="GET" action="{{ route('accounting.reports.general-ledger') }}" class="filters-form">
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="account_id" class="form-label">{{ __('accounting.account') }}</label>
                    <select id="account_id" name="account_id" class="form-select">
                        <option value="">{{ __('accounting.all_accounts') }}</option>
                        @foreach($accounts as $account)
                            <option value="{{ $account->id }}" @selected(($filters['account_id'] ?? '') == $account->id)>{{ $account->code }} - {{ $account->account_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="date_from" class="form-label">{{ __('accounting.from') }}</label>
                    <input type="date" id="date_from" name="date_from" value="{{ $filters['date_from'] ?? '' }}" class="form-control">
                </div>
                <div class="col-md-3">
                    <label for="date_to" class="form-label">{{ __('accounting.to') }}</label>
                    <input type="date" id="date_to" name="date_to" value="{{ $filters['date_to'] ?? '' }}" class="form-control">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">{{ __('accounting.apply') }}</button>
                </div>
            </div>
        </form>
    </div>

    @foreach($report['accounts'] as $account)
        <div class="content-card ledger-card mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h2 class="section-title mb-1">{{ $account->code }} - {{ $account->account_name }}</h2>
                    @php($accountTypeKey = 'accounting.account_types.' . $account->account_type)
                    <p class="section-text mb-0">{{ \Illuminate\Support\Facades\Lang::has($accountTypeKey) ? __($accountTypeKey) : (\App\Models\Account::typeOptions()[$account->account_type] ?? ucfirst($account->account_type)) }}</p>
                </div>
                <div class="ledger-balance">{{ __('accounting.balance') }}: {{ number_format($account->balance, 2) }}</div>
            </div>

            <div class="table-responsive">
                <table class="table admin-table mb-0">
                    <thead><tr><th>{{ __('accounting.date') }}</th><th>{{ __('accounting.reference') }}</th><th>{{ __('accounting.description') }}</th><th class="text-end">{{ __('accounting.debit') }}</th><th class="text-end">{{ __('accounting.credit') }}</th></tr></thead>
                    <tbody>
                        @forelse($account->journalEntryLines as $line)
                            <tr>
                                <td>{{ $line->journalEntry->entry_date->format('M d, Y') }}</td>
                                @php($referenceKey = 'accounting.references.' . $line->journalEntry->reference_type)
                                <td>{{ \Illuminate\Support\Facades\Lang::has($referenceKey) ? __($referenceKey) : ucfirst($line->journalEntry->reference_type) }} #{{ $line->journalEntry->reference_id }}</td>
                                <td>{{ $line->description ?: $line->journalEntry->description }}</td>
                                <td class="text-end">{{ number_format((float) $line->debit, 2) }}</td>
                                <td class="text-end">{{ number_format((float) $line->credit, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center py-4 text-muted">{{ __('accounting.no_ledger_transactions_for_this_account') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach
@endsection
