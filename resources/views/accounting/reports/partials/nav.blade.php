<div class="content-card mb-4">
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('accounting.dashboard') }}" class="btn {{ request()->routeIs('accounting.dashboard') ? 'btn-primary' : 'btn-outline-primary' }}">{{ __('accounting.dashboard') }}</a>
        <a href="{{ route('accounting.reports.profit-loss') }}" class="btn {{ request()->routeIs('accounting.reports.profit-loss') ? 'btn-primary' : 'btn-outline-primary' }}">{{ __('accounting.profit_loss') }}</a>
        <a href="{{ route('accounting.reports.cash-summary') }}" class="btn {{ request()->routeIs('accounting.reports.cash-summary') ? 'btn-primary' : 'btn-outline-primary' }}">{{ __('accounting.cash_summary') }}</a>
        <a href="{{ route('accounting.reports.receivables') }}" class="btn {{ request()->routeIs('accounting.reports.receivables') ? 'btn-primary' : 'btn-outline-primary' }}">{{ __('accounting.receivables') }}</a>
        <a href="{{ route('accounting.reports.general-ledger') }}" class="btn {{ request()->routeIs('accounting.reports.general-ledger') ? 'btn-primary' : 'btn-outline-primary' }}">{{ __('accounting.general_ledger') }}</a>
        <a href="{{ route('accounting.reports.trial-balance') }}" class="btn {{ request()->routeIs('accounting.reports.trial-balance') ? 'btn-primary' : 'btn-outline-primary' }}">{{ __('accounting.trial_balance') }}</a>
        <a href="{{ route('accounting.accounts.index') }}" class="btn {{ request()->routeIs('accounting.accounts.*') ? 'btn-primary' : 'btn-outline-primary' }}">{{ __('accounting.accounts') }}</a>
        <a href="{{ route('accounting.journal-entries.index') }}" class="btn {{ request()->routeIs('accounting.journal-entries.*') ? 'btn-primary' : 'btn-outline-primary' }}">{{ __('accounting.journal_entries') }}</a>
    </div>
</div>
