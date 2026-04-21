@extends('layouts.app')

@section('title', __('users.edit.title'))
@section('page-title', __('users.edit.page_title'))
@section('page-subtitle', __('users.edit.page_subtitle'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/auth/users.css') }}">
@endpush

@section('content')
    <div class="content-card form-card">
        <div class="mb-4">
            <h2 class="section-title mb-1">{{ __('users.edit.heading', ['name' => $user->name]) }}</h2>
        </div>

        <form method="POST" action="{{ route('users.update', $user) }}">
            @csrf
            @method('PUT')
            @include('users.partials.form-fields')

            <div class="d-flex flex-wrap gap-2 mt-4">
                <button type="submit" class="btn btn-primary">{{ __('users.actions.save_changes') }}</button>
                @include('layouts.partials.back-button', ['fallback' => route('users.show', $user)])
            </div>
        </form>
    </div>
@endsection
