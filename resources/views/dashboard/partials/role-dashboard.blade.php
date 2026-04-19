<div class="placeholder-dashboard">
    <div class="placeholder-hero">
        <span class="placeholder-kicker">{{ __('dashboard.role_workspace', ['role' => $title]) }}</span>
        <h2>{{ __('app.welcome_user', ['name' => $user->name]) }}</h2>
        <p>{{ $message }}</p>
    </div>

    <div class="row g-4">
        <div class="col-md-6">
            <div class="content-card h-100">
                <h3 class="section-title">{{ __('dashboard.current_access') }}</h3>
                <p class="section-text">{{ __('dashboard.current_access_text', ['role' => $title]) }}</p>
            </div>
        </div>
        <div class="col-md-6">
            <div class="content-card h-100">
                <h3 class="section-title">{{ __('dashboard.next_modules') }}</h3>
                <p class="section-text mb-0">{{ $nextStep }}</p>
            </div>
        </div>
    </div>
</div>
