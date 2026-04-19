@extends('layouts.app')

@section('title', __('dashboard.admin_title'))
@section('page-title', __('dashboard.admin_title'))
@section('page-subtitle', __('dashboard.admin_subtitle'))

@push('styles')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/auth/dashboard.css') }}">
@endpush

@section('content')
    <section class="summary-showcase users-summary-section content-card mb-4">
        <div class="users-summary-header">
            <div>
                <h2 class="users-summary-title">{{ __('dashboard.users_summary_title') }}</h2>
                <p class="users-summary-text mb-0">{{ __('dashboard.users_summary_text') }}</p>
            </div>
            <a href="{{ route('users.index') }}" class="btn btn-outline-primary users-summary-action">{{ __('dashboard.users_manage_action') }}</a>
        </div>

        <div class="row g-4 users-summary-grid">
            <div class="col-sm-6 col-xl-3">
                <article class="users-summary-card users-summary-card-total">
                    <span class="users-summary-icon" aria-hidden="true">
                        <i class="bi bi-people-fill"></i>
                    </span>
                    <div class="users-summary-copy">
                        <span class="users-summary-label">{{ __('dashboard.total_users') }}</span>
                        <div class="users-summary-value-wrap">
                            <strong class="users-summary-value">{{ $totalUsers }}</strong>
                        </div>
                    </div>
                </article>
            </div>

            <div class="col-sm-6 col-xl-3">
                <article class="users-summary-card users-summary-card-active">
                    <span class="users-summary-icon" aria-hidden="true">
                        <i class="bi bi-person-check-fill"></i>
                    </span>
                    <div class="users-summary-copy">
                        <span class="users-summary-label">{{ __('dashboard.active_users') }}</span>
                        <div class="users-summary-value-wrap">
                            <strong class="users-summary-value">{{ $activeUsers }}</strong>
                        </div>
                    </div>
                </article>
            </div>

            <div class="col-sm-6 col-xl-3">
                <article class="users-summary-card users-summary-card-inactive">
                    <span class="users-summary-icon" aria-hidden="true">
                        <i class="bi bi-person-x-fill"></i>
                    </span>
                    <div class="users-summary-copy">
                        <span class="users-summary-label">{{ __('dashboard.inactive_users') }}</span>
                        <div class="users-summary-value-wrap">
                            <strong class="users-summary-value">{{ $inactiveUsers }}</strong>
                        </div>
                    </div>
                </article>
            </div>

            <div class="col-sm-6 col-xl-3">
                <article class="users-summary-card users-summary-card-admin">
                    <span class="users-summary-icon" aria-hidden="true">
                        <i class="bi bi-shield-lock-fill"></i>
                    </span>
                    <div class="users-summary-copy">
                        <span class="users-summary-label">{{ __('dashboard.total_admins') }}</span>
                        <div class="users-summary-value-wrap">
                            <strong class="users-summary-value">{{ $roleCounts[\App\Models\User::ROLE_ADMIN]['count'] ?? 0 }}</strong>
                        </div>
                    </div>
                </article>
            </div>

            <div class="col-sm-6 col-xl-3">
                <article class="users-summary-card users-summary-card-manager">
                    <span class="users-summary-icon" aria-hidden="true">
                        <i class="bi bi-briefcase-fill"></i>
                    </span>
                    <div class="users-summary-copy">
                        <span class="users-summary-label">{{ __('dashboard.total_managers') }}</span>
                        <div class="users-summary-value-wrap">
                            <strong class="users-summary-value">{{ $roleCounts[\App\Models\User::ROLE_MANAGER]['count'] ?? 0 }}</strong>
                        </div>
                    </div>
                </article>
            </div>

            <div class="col-sm-6 col-xl-3">
                <article class="users-summary-card users-summary-card-production">
                    <span class="users-summary-icon" aria-hidden="true">
                        <i class="bi bi-gear-fill"></i>
                    </span>
                    <div class="users-summary-copy">
                        <span class="users-summary-label">{{ __('dashboard.total_production_users') }}</span>
                        <div class="users-summary-value-wrap">
                            <strong class="users-summary-value">{{ $roleCounts[\App\Models\User::ROLE_PRODUCTION]['count'] ?? 0 }}</strong>
                        </div>
                    </div>
                </article>
            </div>

            <div class="col-sm-6 col-xl-3">
                <article class="users-summary-card users-summary-card-sales">
                    <span class="users-summary-icon" aria-hidden="true">
                        <i class="bi bi-cart-check-fill"></i>
                    </span>
                    <div class="users-summary-copy">
                        <span class="users-summary-label">{{ __('dashboard.total_sales_users') }}</span>
                        <div class="users-summary-value-wrap">
                            <strong class="users-summary-value">{{ $roleCounts[\App\Models\User::ROLE_SALES]['count'] ?? 0 }}</strong>
                        </div>
                    </div>
                </article>
            </div>
        </div>
    </section>

    @include('dashboard.partials.inventory-summary', ['inventorySummary' => $inventorySummary])
    @include('dashboard.partials.cotton-summary', ['cottonSummary' => $cottonSummary])
    @include('dashboard.partials.production-summary', ['productionSummary' => $productionSummary])
    @include('dashboard.partials.expenses-summary', ['expenseSummary' => $expenseSummary])
    @include('dashboard.partials.sales-summary', ['salesSummary' => $salesSummary])
    @include('dashboard.partials.accounting-summary', ['accountingSummary' => $accountingSummary])
    @include('dashboard.partials.employee-summary', ['employeeSummary' => $employeeSummary])
    @include('dashboard.partials.payroll-summary', ['payrollSummary' => $payrollSummary])
    @include('dashboard.partials.advance-summary', ['advanceSummary' => $advanceSummary])
@endsection
