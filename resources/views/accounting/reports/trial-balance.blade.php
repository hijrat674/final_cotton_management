@extends('layouts.app')

@section('title', __('accounting.trial_balance'))
@section('page-title', __('accounting.trial_balance'))
@section('page-subtitle', __('accounting.trial_balance_subtitle'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/accounting/reports.css') }}">
@endpush

@section('content')
    @include('accounting.reports.partials.nav')

    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="summary-card"><div class="summary-card-label">{{ __('accounting.total_debit') }}</div><div class="summary-card-value">{{ number_format($report['total_debit'], 2) }}</div></div>
        </div>
        <div class="col-md-6">
            <div class="summary-card"><div class="summary-card-label">{{ __('accounting.total_credit') }}</div><div class="summary-card-value">{{ number_format($report['total_credit'], 2) }}</div></div>
        </div>
    </div>

    <div class="content-card">
        <div class="table-responsive">
            <table class="table admin-table mb-0">
                <thead>
                    <tr>
                        <th>{{ __('accounting.code') }}</th>
                        <th>{{ __('accounting.account') }}</th>
                        <th>{{ __('accounting.type') }}</th>
                        <th class="text-end">{{ __('accounting.debit') }}</th>
                        <th class="text-end">{{ __('accounting.credit') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($report['accounts'] as $account)
                        <tr>
                            <td>{{ $account->code }}</td>
                            <td>{{ $account->account_name }}</td>
                            @php($accountTypeKey = 'accounting.account_types.' . $account->account_type)
                            <td>{{ \Illuminate\Support\Facades\Lang::has($accountTypeKey) ? __($accountTypeKey) : (\App\Models\Account::typeOptions()[$account->account_type] ?? ucfirst($account->account_type)) }}</td>
                            <td class="text-end">{{ number_format((float) $account->journal_entry_lines_sum_debit, 2) }}</td>
                            <td class="text-end">{{ number_format((float) $account->journal_entry_lines_sum_credit, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="3">{{ __('accounting.totals') }}</th>
                        <th class="text-end">{{ number_format($report['total_debit'], 2) }}</th>
                        <th class="text-end">{{ number_format($report['total_credit'], 2) }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
@endsection
