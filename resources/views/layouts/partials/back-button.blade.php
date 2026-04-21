@php
    $isRtl = in_array(app()->getLocale(), ['ps', 'fa'], true);
    $currentUrl = url()->current();
    $previousUrl = url()->previous();
    $backUrl = filled($previousUrl) && $previousUrl !== $currentUrl
        ? $previousUrl
        : ($fallback ?? route('dashboard'));
    $buttonClass = $class ?? 'btn btn-outline-secondary back-nav-button';
    $buttonLabel = $label ?? __('app.back');
    $iconClass = $isRtl ? 'bi-arrow-right' : 'bi-arrow-left';
@endphp

<a href="{{ $backUrl }}" class="{{ $buttonClass }}">
    <i class="bi {{ $iconClass }}" aria-hidden="true"></i>
    <span>{{ $buttonLabel }}</span>
</a>
