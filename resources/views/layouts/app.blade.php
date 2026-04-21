@php
    $layoutLocale = $currentLocale ?? app()->getLocale();
    $layoutDirection = in_array($layoutLocale, ['ps', 'fa'], true) ? 'rtl' : 'ltr';
    $isModalRequest = request()->boolean('modal');
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', $layoutLocale) }}" dir="{{ $layoutDirection }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', __('app.name'))</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Arabic:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    @include('layouts.partials.frontend-head-assets')
    @stack('styles')
</head>
<body class="admin-body {{ $layoutDirection === 'rtl' ? 'rtl' : 'ltr' }}{{ $isModalRequest ? ' modal-page-body' : '' }}">
    @if($isModalRequest)
        <main class="modal-page-shell">
            @include('layouts.partials.alerts')
            @yield('content')
        </main>
    @else
        <div class="app-shell" id="appShell">
            @include('layouts.partials.sidebar')

            <div class="app-content">
                <button class="btn btn-light sidebar-mobile-toggle d-lg-none" type="button" data-sidebar-toggle aria-label="{{ __('app.menu') }}">
                    {{ __('app.menu') }}
                </button>

                <main class="page-content">
                    @include('layouts.partials.alerts')
                    @yield('content')
                </main>
            </div>
        </div>
        @include('layouts.partials.action-modal')
    @endif

    @include('layouts.partials.frontend-body-assets')
    @stack('scripts')
</body>
</html>
