@extends('layouts.app')

@section('title', __('Create User'))
@section('page-title', __('Create User'))
@section('page-subtitle', __('Add a new internal account with controlled access'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/auth/users.css') }}">
@endpush

@section('content')
    <div class="content-card form-card">
        <div class="mb-4">
            <h2 class="section-title mb-1">New User Account</h2>
            <p class="section-text mb-0">Public registration is disabled. Only admins can create accounts from this panel.</p>
        </div>

        <form method="POST" action="{{ route('users.store') }}">
            @csrf
            @php($includePasswordFields = true)
            @include('users.partials.form-fields')

            <div class="d-flex flex-wrap gap-2 mt-4">
                <button type="submit" class="btn btn-primary">Create User</button>
                <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
@endsection
