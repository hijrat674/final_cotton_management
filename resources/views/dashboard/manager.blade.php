@extends('layouts.app')

@section('title', __('dashboard.manager_title'))
@section('page-title', __('dashboard.manager_title'))
@section('page-subtitle', __('dashboard.manager_subtitle'))

@push('styles')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/auth/dashboard.css') }}">
@endpush

@section('content')
    @include('dashboard.partials.role-dashboard', [
        'title' => __('roles.manager'),
        'user' => $user,
        'message' => __('dashboard.manager_message'),
        'nextStep' => __('dashboard.manager_next_step')
    ])

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
