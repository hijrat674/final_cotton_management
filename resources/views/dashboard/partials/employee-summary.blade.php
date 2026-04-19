<section class="summary-showcase employee-summary-section content-card mt-4">
    <div class="users-summary-header">
        <div>
            <h3 class="users-summary-title">{{ __('dashboard.employee.title') }}</h3>
            <p class="users-summary-text mb-0">{{ __('dashboard.employee.text') }}</p>
        </div>
        <a href="{{ route('employees.index') }}" class="btn btn-outline-primary users-summary-action">
            {{ __('dashboard.employee.open') }}
        </a>
    </div>

    <div class="row g-4 users-summary-grid">
        <div class="col-sm-6 col-xl-3">
            <article class="users-summary-card employee-summary-card-total">
                <span class="users-summary-icon" aria-hidden="true">
                    <i class="bi bi-people-fill"></i>
                </span>
                <div class="users-summary-copy">
                    <span class="users-summary-label">{{ __('dashboard.employee.total_employees') }}</span>
                    <div class="users-summary-value-wrap">
                        <strong class="users-summary-value">{{ $employeeSummary['total_employees'] }}</strong>
                    </div>
                </div>
            </article>
        </div>

        <div class="col-sm-6 col-xl-3">
            <article class="users-summary-card employee-summary-card-active">
                <span class="users-summary-icon" aria-hidden="true">
                    <i class="bi bi-person-check-fill"></i>
                </span>
                <div class="users-summary-copy">
                    <span class="users-summary-label">{{ __('dashboard.employee.active_employees') }}</span>
                    <div class="users-summary-value-wrap">
                        <strong class="users-summary-value">{{ $employeeSummary['active_employees'] }}</strong>
                    </div>
                </div>
            </article>
        </div>

        <div class="col-sm-6 col-xl-3">
            <article class="users-summary-card employee-summary-card-inactive">
                <span class="users-summary-icon" aria-hidden="true">
                    <i class="bi bi-person-x-fill"></i>
                </span>
                <div class="users-summary-copy">
                    <span class="users-summary-label">{{ __('dashboard.employee.inactive_employees') }}</span>
                    <div class="users-summary-value-wrap">
                        <strong class="users-summary-value">{{ $employeeSummary['inactive_employees'] }}</strong>
                    </div>
                </div>
            </article>
        </div>

        <div class="col-sm-6 col-xl-3">
            <article class="users-summary-card employee-summary-card-departments">
                <span class="users-summary-icon" aria-hidden="true">
                    <i class="bi bi-diagram-3-fill"></i>
                </span>
                <div class="users-summary-copy">
                    <span class="users-summary-label">{{ __('dashboard.employee.departments') }}</span>
                    <div class="users-summary-value-wrap">
                        <strong class="users-summary-value">{{ $employeeSummary['departments_count'] }}</strong>
                    </div>
                </div>
            </article>
        </div>
    </div>

    <div class="employee-summary-breakdown">
        <div class="inventory-item-summary-head">
            <div>
                <h4 class="users-summary-title inventory-item-summary-title">{{ __('dashboard.employee.distribution_title') }}</h4>
                <p class="users-summary-text mb-0">{{ __('dashboard.employee.distribution_text') }}</p>
            </div>
        </div>

        <div class="row g-4 users-summary-grid mt-1">
            @forelse($employeeSummary['department_distribution'] as $department)
                <div class="col-sm-6 col-xl-4">
                    <article class="users-summary-card employee-distribution-card">
                        <span class="users-summary-icon" aria-hidden="true">
                            <i class="bi bi-buildings-fill"></i>
                        </span>
                        <div class="users-summary-copy">
                            <span class="users-summary-label employee-distribution-label">{{ $department['label'] }}</span>
                            <div class="users-summary-value-wrap">
                                <strong class="users-summary-value">{{ $department['count'] }}</strong>
                            </div>
                        </div>
                    </article>
                </div>
            @empty
                <div class="col-12">
                    <div class="employee-summary-empty">
                        <span class="employee-summary-empty-icon" aria-hidden="true">
                            <i class="bi bi-person-lines-fill"></i>
                        </span>
                        <div>
                            <h4 class="employee-summary-empty-title">{{ __('dashboard.employee.empty_title') }}</h4>
                            <p class="users-summary-text mb-0">{{ __('dashboard.employee.empty_text') }}</p>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</section>
