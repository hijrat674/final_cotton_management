@extends('layouts.app')

@section('title', __('users.index.title'))
@section('page-title', __('users.index.page_title'))
@section('page-subtitle', __('users.index.page_subtitle'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/auth/users.css') }}">
@endpush

@section('content')
    <div class="content-card mb-4">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-3">
            <div>
                <h2 class="section-title mb-1">{{ __('users.index.directory_title') }}</h2>
                <p class="section-text mb-0">{{ __('users.index.directory_text') }}</p>
            </div>
            <a href="{{ route('users.create') }}" class="btn btn-primary">{{ __('users.actions.create') }}</a>
        </div>

        <form method="GET" action="{{ route('users.index') }}" class="filters-form" id="userFiltersForm">
            <div class="row g-3">
                <div class="col-md-6 col-xl-3">
                    <label for="name" class="form-label">{{ __('users.filters.search_name') }}</label>
                    <input type="text" id="name" name="name" value="{{ $filters['name'] }}" class="form-control" placeholder="{{ __('users.filters.search_by_name') }}">
                </div>
                <div class="col-md-6 col-xl-3">
                    <label for="email" class="form-label">{{ __('users.filters.search_email') }}</label>
                    <input type="text" id="email" name="email" value="{{ $filters['email'] }}" class="form-control" placeholder="{{ __('users.filters.search_by_email') }}">
                </div>
                <div class="col-md-6 col-xl-2">
                    <label for="role" class="form-label">{{ __('users.fields.role') }}</label>
                    <select id="role" name="role" class="form-select">
                        <option value="">{{ __('users.filters.all_roles') }}</option>
                        @foreach($roles as $value => $label)
                            <option value="{{ $value }}" @selected($filters['role'] === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 col-xl-2">
                    <label for="status" class="form-label">{{ __('users.fields.status') }}</label>
                    <select id="status" name="status" class="form-select">
                        <option value="">{{ __('users.filters.all_statuses') }}</option>
                        @foreach($statuses as $value => $label)
                            <option value="{{ $value }}" @selected($filters['status'] === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 col-xl-2">
                    <label for="sort" class="form-label">{{ __('users.filters.sort') }}</label>
                    <select id="sort" name="sort" class="form-select">
                        <option value="latest" @selected($filters['sort'] === 'latest')>{{ __('users.filters.latest') }}</option>
                        <option value="oldest" @selected($filters['sort'] === 'oldest')>{{ __('users.filters.oldest') }}</option>
                    </select>
                </div>
            </div>

            <div class="d-flex flex-wrap gap-2 mt-3">
                <button type="submit" class="btn btn-primary">{{ __('users.actions.apply_filters') }}</button>
                <button type="button" class="btn btn-outline-secondary" data-reset-filters="#userFiltersForm">{{ __('users.actions.reset') }}</button>
            </div>
        </form>
    </div>

    <div class="content-card">
        <div class="table-responsive">
            <table class="table admin-table align-middle mb-0">
                <thead>
                    <tr>
                        <th>{{ __('users.table.user') }}</th>
                        <th>{{ __('users.table.role') }}</th>
                        <th>{{ __('users.table.status') }}</th>
                        <th>{{ __('users.table.created') }}</th>
                        <th class="text-end">{{ __('users.table.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $user->name }}</div>
                                <div class="text-muted small">{{ $user->email }}</div>
                            </td>
                            <td>
                                <span class="badge text-bg-light">{{ $roles[$user->role] ?? ucfirst($user->role) }}</span>
                            </td>
                            <td>
                                <span class="badge {{ $user->isActive() ? 'badge-soft-success' : 'badge-soft-danger' }}">
                                    {{ __(ucfirst($user->status)) }}
                                </span>
                            </td>
                            <td>{{ $user->created_at->format('M d, Y') }}</td>
                            <td class="text-end">
                                <div class="table-actions">
                                    <a
                                        href="{{ route('users.show', $user) }}"
                                        class="btn btn-sm btn-outline-primary action-icon-btn"
                                        title="{{ __('users.actions.view') }}"
                                        aria-label="{{ __('users.actions.view') }}"
                                    >
                                        <i class="bi bi-eye" aria-hidden="true"></i>
                                    </a>
                                    <a
                                        href="{{ route('users.edit', $user) }}"
                                        class="btn btn-sm btn-outline-secondary action-icon-btn"
                                        title="{{ __('users.actions.edit') }}"
                                        aria-label="{{ __('users.actions.edit') }}"
                                    >
                                        <i class="bi bi-pencil-square" aria-hidden="true"></i>
                                    </a>

                                    <form method="POST" action="{{ route('users.status', $user) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button
                                            type="submit"
                                            class="btn btn-sm btn-outline-warning action-icon-btn"
                                            title="{{ $user->isActive() ? __('users.actions.deactivate') : __('users.actions.activate') }}"
                                            aria-label="{{ $user->isActive() ? __('users.actions.deactivate') : __('users.actions.activate') }}"
                                        >
                                            <i class="bi {{ $user->isActive() ? 'bi-toggle-on' : 'bi-toggle-off' }}" aria-hidden="true"></i>
                                        </button>
                                    </form>

                                    @unless($user->isAdmin())
                                        @if(! $user->is(auth()->user()))
                                            <form method="POST" action="{{ route('users.destroy', $user) }}" data-confirm="{{ __('users.messages.delete_confirm', ['name' => $user->name]) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button
                                                    type="submit"
                                                    class="btn btn-sm btn-outline-danger action-icon-btn"
                                                    title="{{ __('users.actions.delete') }}"
                                                    aria-label="{{ __('users.actions.delete') }}"
                                                >
                                                    <i class="bi bi-trash" aria-hidden="true"></i>
                                                </button>
                                            </form>
                                        @endif
                                    @endunless
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">{{ __('users.messages.empty') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $users->links() }}
        </div>
    </div>
@endsection
