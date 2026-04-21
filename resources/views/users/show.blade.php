@extends('layouts.app')

@section('title', __('users.show.title'))
@section('page-title', __('users.show.page_title'))
@section('page-subtitle', __('users.show.page_subtitle'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/auth/users.css') }}">
@endpush

@section('content')
    <div class="row g-4">
        <div class="col-xl-8">
            <div class="content-card detail-card">
                <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 mb-4">
                    <div>
                        <h2 class="section-title mb-1">{{ $user->name }}</h2>
                        <p class="section-text mb-0">{{ $user->email }}</p>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <span class="badge text-bg-light">{{ __("roles.{$user->role}") !== "roles.{$user->role}" ? __("roles.{$user->role}") : (\App\Models\User::roleOptions()[$user->role] ?? ucfirst($user->role)) }}</span>
                        <span class="badge {{ $user->isActive() ? 'badge-soft-success' : 'badge-soft-danger' }}">{{ __("users.statuses.{$user->status}") }}</span>
                    </div>
                </div>

                <div class="detail-grid">
                    <div>
                        <span class="detail-label">{{ __('users.show.details.created_at') }}</span>
                        <span class="detail-value">{{ $user->created_at->translatedFormat('M d, Y h:i A') }}</span>
                    </div>
                    <div>
                        <span class="detail-label">{{ __('users.show.details.updated_at') }}</span>
                        <span class="detail-value">{{ $user->updated_at->translatedFormat('M d, Y h:i A') }}</span>
                    </div>
                    <div>
                        <span class="detail-label">{{ __('users.show.details.account_status') }}</span>
                        <span class="detail-value">{{ __("users.statuses.{$user->status}") }}</span>
                    </div>
                    <div>
                        <span class="detail-label">{{ __('users.show.details.role_scope') }}</span>
                        <span class="detail-value">{{ __("roles.{$user->role}") !== "roles.{$user->role}" ? __("roles.{$user->role}") : (\App\Models\User::roleOptions()[$user->role] ?? ucfirst($user->role)) }}</span>
                    </div>
                </div>

                <div class="d-flex flex-wrap gap-2 mt-4">
                    @include('layouts.partials.back-button', ['fallback' => route('users.index')])
                    <a href="{{ route('users.edit', $user) }}" class="btn btn-primary">{{ __('users.actions.edit_user') }}</a>

                    @if(! $user->is(auth()->user()))
                        <form method="POST" action="{{ route('users.status', $user) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-outline-warning">
                                {{ $user->isActive() ? __('users.actions.deactivate_user') : __('users.actions.activate_user') }}
                            </button>
                        </form>
                    @endif

                    @unless($user->isAdmin())
                        @if(! $user->is(auth()->user()))
                            <form method="POST" action="{{ route('users.destroy', $user) }}" data-confirm="{{ __('users.messages.delete_confirm', ['name' => $user->name]) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger">{{ __('users.actions.delete_user') }}</button>
                            </form>
                        @endif
                    @endunless
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="content-card detail-card">
                <h3 class="section-title mb-3">{{ __('users.password.title') }}</h3>
                <form method="POST" action="{{ route('users.password.update', $user) }}">
                    @csrf
                    @method('PATCH')

                    <div class="mb-3">
                        <label for="password" class="form-label">{{ __('users.password.new_password') }}</label>
                        <div class="password-field">
                            <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                            <button
                                type="button"
                                class="password-toggle"
                                data-password-toggle
                                data-password-icon-toggle
                                data-target="password"
                                data-label-show="{{ __('users.password.show') }}"
                                data-label-hide="{{ __('users.password.hide') }}"
                                aria-label="{{ __('users.password.show') }}"
                                aria-pressed="false"
                            >
                                <i class="bi bi-eye password-toggle-icon" aria-hidden="true"></i>
                            </button>
                        </div>
                        @error('password')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">{{ __('users.password.confirm_password') }}</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">{{ __('users.password.reset') }}</button>
                </form>
            </div>
        </div>
    </div>
@endsection
