@extends('layouts.app')

@section('title', __('accounting.chart_of_accounts'))
@section('page-title', __('accounting.chart_of_accounts'))
@section('page-subtitle', __('accounting.chart_of_accounts_subtitle'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/accounting/reports.css') }}">
@endpush

@section('content')
    @include('accounting.reports.partials.nav')

    <div class="content-card mb-4">
        <form method="GET" action="{{ route('accounting.accounts.index') }}" class="filters-form">
            <div class="row g-3">
                <div class="col-md-5">
                    <label for="search" class="form-label">{{ __('accounting.search') }}</label>
                    <input type="text" id="search" name="search" value="{{ $filters['search'] ?? '' }}" class="form-control" placeholder="{{ __('accounting.search_by_account') }}">
                </div>
                <div class="col-md-5">
                    <label for="account_type" class="form-label">{{ __('accounting.account_type') }}</label>
                    <select id="account_type" name="account_type" class="form-select">
                        <option value="">{{ __('accounting.all_types') }}</option>
                        @foreach($accountTypes as $value => $label)
                            @php($accountTypeKey = 'accounting.account_types.' . $value)
                            <option value="{{ $value }}" @selected(($filters['account_type'] ?? '') === $value)>{{ \Illuminate\Support\Facades\Lang::has($accountTypeKey) ? __($accountTypeKey) : $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">{{ __('accounting.apply') }}</button>
                </div>
            </div>
        </form>
    </div>

    <div class="content-card">
        <div class="table-responsive">
            <table class="table admin-table mb-0">
                <thead><tr><th>{{ __('accounting.code') }}</th><th>{{ __('accounting.account') }}</th><th>{{ __('accounting.type') }}</th><th>{{ __('accounting.parent') }}</th><th class="text-end">{{ __('accounting.balance') }}</th></tr></thead>
                <tbody>
                    @foreach($accounts as $account)
                        <tr>
                            <td>{{ $account->code }}</td>
                            <td>{{ $account->account_name }}</td>
                            @php($accountTypeKey = 'accounting.account_types.' . $account->account_type)
                            <td>{{ \Illuminate\Support\Facades\Lang::has($accountTypeKey) ? __($accountTypeKey) : ($accountTypes[$account->account_type] ?? ucfirst($account->account_type)) }}</td>
                            <td>{{ $account->parent?->account_name ?? __('accounting.primary') }}</td>
                            <td class="text-end">{{ number_format($account->balance, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $accounts->links() }}</div>
    </div>
@endsection
