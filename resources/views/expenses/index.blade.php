@extends('layouts.app')

@section('title', __('expenses.index.title'))
@section('page-title', __('expenses.index.page_title'))
@section('page-subtitle', __('expenses.index.page_subtitle'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/expenses/expenses-index.css') }}">
@endpush

@section('content')
    <nav aria-label="breadcrumb" class="breadcrumb-shell mb-4">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ __('expenses.index.title') }}</li>
        </ol>
    </nav>

    <div class="row g-4 mb-4">
        <div class="col-md-6 col-xl-3">
            <div class="summary-card">
                <div class="summary-card-label">{{ __('expenses.summary.total_expenses') }}</div>
                <div class="summary-card-value">{{ $summary['total_expenses'] }}</div>
                <div class="summary-card-meta">{{ __('expenses.summary.total_expenses_text') }}</div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="summary-card">
                <div class="summary-card-label">{{ __('expenses.summary.total_amount') }}</div>
                <div class="summary-card-value">{{ number_format($summary['total_amount'], 2) }}</div>
                <div class="summary-card-meta">{{ __('expenses.summary.total_amount_text') }}</div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="summary-card">
                <div class="summary-card-label">{{ __('expenses.summary.production_linked') }}</div>
                <div class="summary-card-value">{{ $summary['production_linked_count'] }}</div>
                <div class="summary-card-meta">{{ __('expenses.summary.production_linked_text') }}</div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="summary-card">
                <div class="summary-card-label">{{ __('expenses.summary.general_expenses') }}</div>
                <div class="summary-card-value">{{ $summary['general_expenses_count'] }}</div>
                <div class="summary-card-meta">{{ __('expenses.summary.general_expenses_text') }}</div>
            </div>
        </div>
    </div>

    <div class="content-card mb-4">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-3">
            <div>
                <h2 class="section-title mb-1">{{ __('expenses.index.register_title') }}</h2>
            </div>
            @if($canCreateExpenses)
                <a href="{{ route('expenses.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i>
                    {{ __('expenses.actions.new') }}
                </a>
            @endif
        </div>

        <form method="GET" action="{{ route('expenses.index') }}" class="filters-form" id="expensesFilterForm">
            <div class="row g-3">
                <div class="col-md-6 col-xl-3">
                    <label for="title" class="form-label">{{ __('expenses.filters.title') }}</label>
                    <input type="text" id="title" name="title" value="{{ $filters['title'] }}" class="form-control" placeholder="{{ __('expenses.filters.search_title') }}">
                </div>
                <div class="col-md-6 col-xl-2">
                    <label for="expense_type_id" class="form-label">{{ __('expenses.filters.expense_type') }}</label>
                    <select id="expense_type_id" name="expense_type_id" class="form-select">
                        <option value="">{{ __('expenses.filters.all_types') }}</option>
                        @foreach($expenseTypes as $expenseType)
                            @php($expenseTypeKey = 'expenses.types.' . $expenseType->name)
                            <option value="{{ $expenseType->id }}" @selected($filters['expense_type_id'] === (string) $expenseType->id)>
                                {{ \Illuminate\Support\Facades\Lang::has($expenseTypeKey) ? __($expenseTypeKey) : (\App\Models\ExpenseType::defaultOptions()[$expenseType->name] ?? ucfirst($expenseType->name)) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 col-xl-2">
                    <label for="date_from" class="form-label">{{ __('expenses.filters.from') }}</label>
                    <input type="date" id="date_from" name="date_from" value="{{ $filters['date_from'] }}" class="form-control">
                </div>
                <div class="col-md-6 col-xl-2">
                    <label for="date_to" class="form-label">{{ __('expenses.filters.to') }}</label>
                    <input type="date" id="date_to" name="date_to" value="{{ $filters['date_to'] }}" class="form-control">
                </div>
                <div class="col-md-6 col-xl-2">
                    <label for="production_stage_id" class="form-label">{{ __('expenses.filters.production_link') }}</label>
                    <select id="production_stage_id" name="production_stage_id" class="form-select">
                        <option value="">{{ __('expenses.filters.all') }}</option>
                        <option value="general" @selected($filters['production_stage_id'] === 'general')>{{ __('expenses.summary.general_expenses') }}</option>
                        @foreach($productionStages as $stage)
                            <option value="{{ $stage->id }}" @selected($filters['production_stage_id'] === (string) $stage->id)>
                                {{ $stage->stage_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 col-xl-1">
                    <label for="sort" class="form-label">{{ __('expenses.filters.sort') }}</label>
                    <select id="sort" name="sort" class="form-select">
                        <option value="latest" @selected($filters['sort'] === 'latest')>{{ __('expenses.filters.latest') }}</option>
                        <option value="oldest" @selected($filters['sort'] === 'oldest')>{{ __('expenses.filters.oldest') }}</option>
                    </select>
                </div>
            </div>

            <div class="d-flex flex-wrap gap-2 mt-3">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-funnel me-1"></i>
                    {{ __('expenses.actions.apply_filters') }}
                </button>
                <button type="button" class="btn btn-outline-secondary" data-reset-filters="#expensesFilterForm">
                    <i class="bi bi-arrow-counterclockwise me-1"></i>
                    {{ __('expenses.actions.reset') }}
                </button>
            </div>
        </form>
    </div>

    <div class="content-card">
        <div class="table-responsive">
            <table class="table admin-table align-middle mb-0">
                <thead>
                    <tr>
                        <th>{{ __('expenses.table.date') }}</th>
                        <th>{{ __('expenses.table.title') }}</th>
                        <th>{{ __('expenses.table.type') }}</th>
                        <th>{{ __('expenses.table.amount') }}</th>
                        <th>{{ __('expenses.table.linked_stage') }}</th>
                        <th>{{ __('expenses.table.created_by') }}</th>
                        <th class="text-end">{{ __('expenses.table.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($expenses as $expense)
                        <tr>
                            <td>{{ $expense->expense_date->format('M d, Y') }}</td>
                            <td>
                                <div class="fw-semibold">{{ $expense->title }}</div>
                                <div class="text-muted small">{{ __('expenses.messages.number', ['id' => $expense->id]) }}</div>
                            </td>
                            <td>@include('expenses.partials.expense-type-badge', ['expenseType' => $expense->expenseType])</td>
                            <td class="fw-semibold">{{ number_format((float) $expense->amount, 2) }}</td>
                            <td>
                                @if($expense->productionStage)
                                    <span class="stage-link-pill">{{ $expense->productionStage->stage_name }}</span>
                                @else
                                    <span class="text-muted">{{ __('expenses.messages.general_expense') }}</span>
                                @endif
                            </td>
                            <td>{{ $expense->creator->name }}</td>
                            <td class="text-end">
                                <div class="table-actions">
                                    <a href="{{ route('expenses.show', $expense) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if($canManageExpenses)
                                        <a href="{{ route('expenses.edit', $expense) }}" class="btn btn-sm btn-outline-secondary">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <form method="POST" action="{{ route('expenses.destroy', $expense) }}" data-confirm="{{ __('expenses.messages.delete_confirm', ['title' => $expense->title]) }}">
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
                            <td colspan="7" class="text-center py-5 text-muted">{{ __('expenses.messages.empty') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $expenses->links() }}
        </div>
    </div>
@endsection
