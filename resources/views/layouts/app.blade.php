@php
    $layoutLocale = $currentLocale ?? app()->getLocale();
    $layoutDirection = in_array($layoutLocale, ['ps', 'fa'], true) ? 'rtl' : 'ltr';
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
<body class="admin-body {{ $layoutDirection === 'rtl' ? 'rtl' : 'ltr' }}">
    <div class="app-shell" id="appShell">
        @include('layouts.partials.sidebar')

        <div class="app-content">
            @include('layouts.partials.navbar')

            <main class="page-content">
                @include('layouts.partials.alerts')
                @yield('content')
            </main>
        </div>
    </div>

    @include('layouts.partials.frontend-body-assets')
    @stack('scripts')
</body>
</html>
