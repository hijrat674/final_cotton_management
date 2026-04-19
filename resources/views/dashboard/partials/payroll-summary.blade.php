<section class="summary-showcase payroll-summary-section content-card mt-4">
    <div class="users-summary-header">
        <div>
            <h3 class="users-summary-title">{{ __('dashboard.payroll.title') }}</h3>
            <p class="users-summary-text mb-0">{{ __('dashboard.payroll.text') }}</p>
        </div>
        <a href="{{ route('salary-records.index') }}" class="btn btn-outline-primary users-summary-action">
            {{ __('dashboard.payroll.open') }}
        </a>
    </div>

    <div class="row g-4 users-summary-grid">
        <div class="col-sm-6 col-xl-4">
            <article class="users-summary-card payroll-summary-card-paid">
                <span class="users-summary-icon" aria-hidden="true">
                    <i class="bi bi-wallet2"></i>
                </span>
                <div class="users-summary-copy">
                    <span class="users-summary-label">{{ __('dashboard.payroll.salary_paid') }}</span>
                    <div class="users-summary-value-wrap">
                        <div class="payroll-summary-value-block">
                            <strong class="payroll-summary-amount">{{ number_format($payrollSummary['monthly_paid_total'], 2) }}</strong>
                            <span class="payroll-summary-currency">AFG</span>
                        </div>
                    </div>
                </div>
            </article>
        </div>

        <div class="col-sm-6 col-xl-4">
            <article class="users-summary-card payroll-summary-card-unpaid">
                <span class="users-summary-icon" aria-hidden="true">
                    <i class="bi bi-clock-history"></i>
                </span>
                <div class="users-summary-copy">
                    <span class="users-summary-label">{{ __('dashboard.payroll.salary_unpaid') }}</span>
                    <div class="users-summary-value-wrap">
                        <div class="payroll-summary-value-block">
                            <strong class="payroll-summary-amount">{{ number_format($payrollSummary['monthly_unpaid_total'], 2) }}</strong>
                            <span class="payroll-summary-currency">AFG</span>
                        </div>
                    </div>
                </div>
            </article>
        </div>

        <div class="col-sm-6 col-xl-4">
            <article class="users-summary-card payroll-summary-card-employees">
                <span class="users-summary-icon" aria-hidden="true">
                    <i class="bi bi-person-check-fill"></i>
                </span>
                <div class="users-summary-copy">
                    <span class="users-summary-label">{{ __('dashboard.payroll.paid_employees') }}</span>
                    <div class="users-summary-value-wrap">
                        <strong class="users-summary-value">{{ $payrollSummary['monthly_employees_paid'] }}</strong>
                    </div>
                </div>
            </article>
        </div>
    </div>

    <div class="row g-4 payroll-chart-grid">
        <div class="col-xl-7">
            <section class="payroll-chart-panel payroll-chart-panel-trend">
                <div class="payroll-chart-head">
                    <div>
                        <h4 class="payroll-chart-title">{{ __('dashboard.payroll.trend_title') }}</h4>
                        <p class="payroll-chart-text mb-0">{{ __('dashboard.payroll.trend_text') }}</p>
                    </div>
                    <span class="payroll-chart-badge">{{ __('dashboard.payroll.trend_badge') }}</span>
                </div>
                <div class="payroll-chart-canvas-wrap payroll-chart-canvas-wrap-lg">
                    <canvas id="payrollTrendChart"></canvas>
                </div>
            </section>
        </div>

        <div class="col-xl-5">
            <section class="payroll-chart-panel payroll-chart-panel-status">
                <div class="payroll-chart-head">
                    <div>
                        <h4 class="payroll-chart-title">{{ __('dashboard.payroll.status_title') }}</h4>
                        <p class="payroll-chart-text mb-0">{{ __('dashboard.payroll.status_text') }}</p>
                    </div>
                    <span class="payroll-chart-badge">{{ __('dashboard.payroll.status_badge') }}</span>
                </div>
                <div class="payroll-chart-canvas-wrap payroll-chart-canvas-wrap-md">
                    <canvas id="payrollStatusChart"></canvas>
                </div>
            </section>
        </div>

        <div class="col-12">
            <section class="payroll-chart-panel payroll-chart-panel-department">
                <div class="payroll-chart-head">
                    <div>
                        <h4 class="payroll-chart-title">{{ __('dashboard.payroll.department_title') }}</h4>
                        <p class="payroll-chart-text mb-0">{{ __('dashboard.payroll.department_text') }}</p>
                    </div>
                    <span class="payroll-chart-badge">{{ __('dashboard.payroll.department_badge') }}</span>
                </div>
                <div class="payroll-chart-canvas-wrap payroll-chart-canvas-wrap-lg">
                    <canvas id="payrollDepartmentChart"></canvas>
                </div>
            </section>
        </div>
    </div>
</section>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
    <script>
        (() => {
            const trendCanvas = document.getElementById('payrollTrendChart');
            const statusCanvas = document.getElementById('payrollStatusChart');
            const departmentCanvas = document.getElementById('payrollDepartmentChart');

            if (!trendCanvas || !statusCanvas || !departmentCanvas || trendCanvas.dataset.ready === 'true') {
                return;
            }

            trendCanvas.dataset.ready = 'true';

            const trendData = @json($payrollSummary['monthly_salary_trend']);
            const statusData = @json($payrollSummary['paid_vs_unpaid']);
            const departmentData = @json($payrollSummary['department_salary_distribution']);

            const chartLabelColor = '#667085';
            const chartGridColor = 'rgba(148, 163, 184, 0.2)';
            const chartBorderColor = 'rgba(226, 232, 240, 0.8)';

            new Chart(trendCanvas, {
                type: 'line',
                data: {
                    labels: trendData.map(item => item.label),
                    datasets: [{
                        label: @json(__('dashboard.payroll.salary_paid_dataset')),
                        data: trendData.map(item => item.amount),
                        borderColor: '#2563eb',
                        backgroundColor: 'rgba(37, 99, 235, 0.14)',
                        pointBackgroundColor: '#2563eb',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 3,
                        pointHoverRadius: 4,
                        fill: true,
                        tension: 0.35
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    plugins: {
                        legend: {
                            display: true,
                            labels: {
                                usePointStyle: true,
                                pointStyle: 'circle',
                                color: chartLabelColor,
                                boxWidth: 10,
                                boxHeight: 10,
                                padding: 16
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false,
                                drawBorder: false
                            },
                            border: {
                                color: chartBorderColor
                            },
                            ticks: {
                                color: chartLabelColor
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: chartGridColor,
                                drawBorder: false
                            },
                            border: {
                                color: chartBorderColor
                            },
                            ticks: {
                                color: chartLabelColor
                            }
                        }
                    }
                }
            });

            new Chart(statusCanvas, {
                type: 'doughnut',
                data: {
                    labels: statusData.map(item => item.label),
                    datasets: [{
                        data: statusData.map(item => item.count),
                        backgroundColor: ['#16a34a', '#f59e0b', '#dc2626'],
                        borderColor: '#ffffff',
                        borderWidth: 4,
                        hoverOffset: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '68%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                pointStyle: 'circle',
                                color: chartLabelColor,
                                boxWidth: 10,
                                boxHeight: 10,
                                padding: 18
                            }
                        }
                    }
                }
            });

            new Chart(departmentCanvas, {
                type: 'bar',
                data: {
                    labels: departmentData.map(item => item.label),
                    datasets: [{
                        label: @json(__('dashboard.payroll.salary_paid_dataset')),
                        data: departmentData.map(item => item.amount),
                        backgroundColor: 'rgba(14, 165, 233, 0.82)',
                        borderRadius: 10,
                        borderSkipped: false,
                        maxBarThickness: 42
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            labels: {
                                usePointStyle: true,
                                pointStyle: 'circle',
                                color: chartLabelColor,
                                boxWidth: 10,
                                boxHeight: 10,
                                padding: 16
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false,
                                drawBorder: false
                            },
                            border: {
                                color: chartBorderColor
                            },
                            ticks: {
                                color: chartLabelColor
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: chartGridColor,
                                drawBorder: false
                            },
                            border: {
                                color: chartBorderColor
                            },
                            ticks: {
                                color: chartLabelColor
                            }
                        }
                    }
                }
            });
        })();
    </script>
@endpush
