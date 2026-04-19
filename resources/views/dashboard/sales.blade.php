@extends('layouts.app')

@section('title', __('dashboard.sales_title'))
@section('page-title', __('dashboard.sales_title'))
@section('page-subtitle', __('dashboard.sales_subtitle'))

@push('styles')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/auth/dashboard.css') }}">
@endpush

@section('content')
    @include('dashboard.partials.role-dashboard', [
        'title' => __('roles.sales'),
        'user' => $user,
        'message' => __('dashboard.sales_message'),
        'nextStep' => __('dashboard.sales_next_step')
    ])

    @include('dashboard.partials.sales-summary', ['salesSummary' => $salesSummary])
@endsection
