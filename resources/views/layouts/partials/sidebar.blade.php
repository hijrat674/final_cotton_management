@php
    $user = auth()->user();
    $currentRoute = request()->route()?->getName();
    $isRtl = in_array(app()->getLocale(), ['ps', 'fa'], true);
    $sidebarSectionLabelClass = 'sidebar-section-label' . ($isRtl ? ' text-end' : '');
    $sidebarLinkClass = 'sidebar-link' . ($isRtl ? ' flex-row-reverse justify-content-end text-end' : '');
    $sidebarLinkTextClass = 'sidebar-link-text' . ($isRtl ? ' text-end' : '');
    $sidebarBrandClass = 'sidebar-brand sidebar-panel text-center';
    $sidebarBrandContentClass = 'sidebar-brand-content text-center';
    $sidebarFooterClass = 'sidebar-footer sidebar-panel text-center';
@endphp

<aside class="sidebar" id="sidebar">
    <div class="{{ $sidebarBrandClass }}">
        <div class="sidebar-brand-mark">FM</div>
        <div class="{{ $sidebarBrandContentClass }}">
            <div class="sidebar-brand-title">{{ __('app.short_name') }}</div>
            <div class="sidebar-brand-subtitle">{{ __('app.operations_center') }}</div>
        </div>
    </div>

    <div class="sidebar-nav-shell">
        <nav class="sidebar-nav">
            <div class="sidebar-group">
                <div class="{{ $sidebarSectionLabelClass }}">{{ __('sidebar.overview') }}</div>
                <a href="{{ route('dashboard') }}" class="{{ $sidebarLinkClass }} {{ $currentRoute === 'dashboard' ? 'active' : '' }}">
                    <span class="sidebar-link-icon" aria-hidden="true"><i class="bi bi-grid-1x2-fill"></i></span>
                    <span class="{{ $sidebarLinkTextClass }}">{{ __('sidebar.dashboard') }}</span>
                </a>
            </div>

            @if($user?->isAdmin())
                <div class="sidebar-group">
                    <div class="{{ $sidebarSectionLabelClass }}">{{ __('sidebar.administration') }}</div>
                    <a href="{{ route('users.index') }}" class="{{ $sidebarLinkClass }} {{ str_starts_with((string) $currentRoute, 'users.') ? 'active' : '' }}">
                        <span class="sidebar-link-icon" aria-hidden="true"><i class="bi bi-people"></i></span>
                        <span class="{{ $sidebarLinkTextClass }}">{{ __('sidebar.user_management') }}</span>
                    </a>
                </div>
            @endif

            <div class="sidebar-group">
                <div class="{{ $sidebarSectionLabelClass }}">{{ __('sidebar.operations') }}</div>

                @if($user?->hasRole(\App\Models\User::ROLE_ADMIN, \App\Models\User::ROLE_MANAGER, \App\Models\User::ROLE_PRODUCTION))
                    <a href="{{ route('employees.index') }}" class="{{ $sidebarLinkClass }} {{ str_starts_with((string) $currentRoute, 'employees.') ? 'active' : '' }}">
                        <span class="sidebar-link-icon" aria-hidden="true"><i class="bi bi-badge-ad"></i></span>
                        <span class="{{ $sidebarLinkTextClass }}">{{ __('sidebar.employees') }}</span>
                    </a>
                @endif

                <a href="{{ route('inventory-items.index') }}" class="{{ $sidebarLinkClass }} {{ str_starts_with((string) $currentRoute, 'inventory-items.') ? 'active' : '' }}">
                    <span class="sidebar-link-icon" aria-hidden="true"><i class="bi bi-box-seam"></i></span>
                    <span class="{{ $sidebarLinkTextClass }}">{{ __('sidebar.inventory_items') }}</span>
                </a>

                <a href="{{ route('inventory-transactions.index') }}" class="{{ $sidebarLinkClass }} {{ str_starts_with((string) $currentRoute, 'inventory-transactions.') ? 'active' : '' }}">
                    <span class="sidebar-link-icon" aria-hidden="true"><i class="bi bi-arrow-left-right"></i></span>
                    <span class="{{ $sidebarLinkTextClass }}">{{ __('sidebar.stock_ledger') }}</span>
                </a>

                @if($user?->hasRole(\App\Models\User::ROLE_ADMIN, \App\Models\User::ROLE_MANAGER, \App\Models\User::ROLE_PRODUCTION))
                    <a href="{{ route('cotton-entries.index') }}" class="{{ $sidebarLinkClass }} {{ str_starts_with((string) $currentRoute, 'cotton-entries.') ? 'active' : '' }}">
                        <span class="sidebar-link-icon" aria-hidden="true"><i class="bi bi-truck"></i></span>
                        <span class="{{ $sidebarLinkTextClass }}">{{ __('sidebar.cotton_entries') }}</span>
                    </a>

                    <a href="{{ route('production-stages.index') }}" class="{{ $sidebarLinkClass }} {{ str_starts_with((string) $currentRoute, 'production-stages.') ? 'active' : '' }}">
                        <span class="sidebar-link-icon" aria-hidden="true"><i class="bi bi-gear-wide-connected"></i></span>
                        <span class="{{ $sidebarLinkTextClass }}">{{ __('sidebar.production_stages') }}</span>
                    </a>

                    <a href="{{ route('expenses.index') }}" class="{{ $sidebarLinkClass }} {{ str_starts_with((string) $currentRoute, 'expenses.') ? 'active' : '' }}">
                        <span class="sidebar-link-icon" aria-hidden="true"><i class="bi bi-receipt-cutoff"></i></span>
                        <span class="{{ $sidebarLinkTextClass }}">{{ __('sidebar.expenses') }}</span>
                    </a>
                @endif
            </div>

            @if($user?->hasRole(\App\Models\User::ROLE_ADMIN, \App\Models\User::ROLE_MANAGER, \App\Models\User::ROLE_SALES))
                <div class="sidebar-group">
                    <div class="{{ $sidebarSectionLabelClass }}">{{ __('sidebar.commerce') }}</div>
                    <a href="{{ route('customers.index') }}" class="{{ $sidebarLinkClass }} {{ str_starts_with((string) $currentRoute, 'customers.') ? 'active' : '' }}">
                        <span class="sidebar-link-icon" aria-hidden="true"><i class="bi bi-people-fill"></i></span>
                        <span class="{{ $sidebarLinkTextClass }}">{{ __('sidebar.customers') }}</span>
                    </a>

                    <a href="{{ route('sales.index') }}" class="{{ $sidebarLinkClass }} {{ str_starts_with((string) $currentRoute, 'sales.') || str_starts_with((string) $currentRoute, 'sale-payments.') ? 'active' : '' }}">
                        <span class="sidebar-link-icon" aria-hidden="true"><i class="bi bi-receipt"></i></span>
                        <span class="{{ $sidebarLinkTextClass }}">{{ __('sidebar.sales') }}</span>
                    </a>
                </div>
            @endif

            @if($user?->hasRole(\App\Models\User::ROLE_ADMIN, \App\Models\User::ROLE_MANAGER))
                <div class="sidebar-group">
                    <div class="{{ $sidebarSectionLabelClass }}">{{ __('sidebar.finance') }}</div>
                    <a href="{{ route('accounting.dashboard') }}" class="{{ $sidebarLinkClass }} {{ str_starts_with((string) $currentRoute, 'accounting.') ? 'active' : '' }}">
                        <span class="sidebar-link-icon" aria-hidden="true"><i class="bi bi-bank"></i></span>
                        <span class="{{ $sidebarLinkTextClass }}">{{ __('sidebar.accounting') }}</span>
                    </a>

                    <a href="{{ route('salary-records.index') }}" class="{{ $sidebarLinkClass }} {{ str_starts_with((string) $currentRoute, 'salary-records.') || str_starts_with((string) $currentRoute, 'salary-payments.') ? 'active' : '' }}">
                        <span class="sidebar-link-icon" aria-hidden="true"><i class="bi bi-cash-coin"></i></span>
                        <span class="{{ $sidebarLinkTextClass }}">{{ __('sidebar.payroll') }}</span>
                    </a>

                    <a href="{{ route('employee-advances.index') }}" class="{{ $sidebarLinkClass }} {{ str_starts_with((string) $currentRoute, 'employee-advances.') ? 'active' : '' }}">
                        <span class="sidebar-link-icon" aria-hidden="true"><i class="bi bi-wallet2"></i></span>
                        <span class="{{ $sidebarLinkTextClass }}">{{ __('sidebar.employee_advances') }}</span>
                    </a>
                </div>
            @endif
        </nav>
    </div>

    <div class="{{ $sidebarFooterClass }}">
        <div class="sidebar-footer-content">
            <div class="sidebar-footer-label">{{ __('app.current_role') }}</div>
            <div class="sidebar-footer-role">{{ __('roles.'.$user->role) }}</div>
        </div>
    </div>
</aside>
