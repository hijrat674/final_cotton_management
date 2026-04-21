@php
    $user = auth()->user();
    $languageLabels = [
        'en' => 'English',
        'ps' => 'پښتو',
        'fa' => 'فارسی',
    ];
@endphp

<header class="topbar">
    <div class="topbar-left topbar-title-block">
        <button class="btn btn-light d-lg-none topbar-toggle" type="button" data-sidebar-toggle aria-label="{{ __('app.menu') }}">
            {{ __('app.menu') }}
        </button>
        <div class="topbar-heading">
            <h1 class="page-title">@yield('page-title', __('sidebar.dashboard'))</h1>
        </div>
    </div>

    <div class="dropdown topbar-language">
        <button class="btn user-menu language-menu dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            <span class="language-menu-icon" aria-hidden="true">
                <i class="bi bi-globe2"></i>
            </span>
            <span class="text-start">
                <span class="d-block fw-semibold">{{ __('app.language') }}</span>
                <span class="d-block small text-muted language-current-label" lang="{{ $currentLocale }}">
                    {!! $languageLabels[$currentLocale] ?? e($supportedLocales[$currentLocale]['native'] ?? strtoupper($currentLocale)) !!}
                </span>
            </span>
        </button>
        <ul class="dropdown-menu {{ ($isRtl ?? false) ? 'dropdown-menu-start' : 'dropdown-menu-end' }} shadow-sm border-0">
            @foreach($supportedLocales as $localeCode => $localeConfig)
                <li>
                    <a href="{{ route('language.switch', ['locale' => $localeCode]) }}" class="dropdown-item language-option {{ $currentLocale === $localeCode ? 'active' : '' }}">
                        <span class="language-option-label" lang="{{ $localeCode }}">
                            {!! $languageLabels[$localeCode] ?? e($localeConfig['native']) !!}
                        </span>
                        @if($currentLocale === $localeCode)
                            <i class="bi bi-check2"></i>
                        @endif
                    </a>
                </li>
            @endforeach
        </ul>
    </div>

</header>
