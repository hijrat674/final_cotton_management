<section class="summary-showcase advance-summary-section content-card mt-4">
    <div class="users-summary-header">
        <div>
            <h3 class="users-summary-title">{{ __('dashboard.advance.title') }}</h3>
            <p class="users-summary-text mb-0">{{ __('dashboard.advance.text') }}</p>
        </div>
        <a href="{{ route('employee-advances.index') }}" class="btn btn-outline-primary users-summary-action">
            {{ __('dashboard.advance.open') }}
        </a>
    </div>

    <div class="row g-4 users-summary-grid">
        <div class="col-sm-6 col-xl-4">
            <article class="users-summary-card advance-summary-card-total">
                <span class="users-summary-icon" aria-hidden="true">
                    <i class="bi bi-wallet2"></i>
                </span>
                <div class="users-summary-copy">
                    <span class="users-summary-label">{{ __('dashboard.advance.total_advances') }}</span>
                    <div class="users-summary-value-wrap">
                        <div class="advance-summary-value-block">
                            <strong class="advance-summary-amount">{{ number_format($advanceSummary['total_advances'], 2) }}</strong>
                            <span class="advance-summary-currency">AFG</span>
                        </div>
                    </div>
                </div>
            </article>
        </div>

        <div class="col-sm-6 col-xl-4">
            <article class="users-summary-card advance-summary-card-pending">
                <span class="users-summary-icon" aria-hidden="true">
                    <i class="bi bi-clock-history"></i>
                </span>
                <div class="users-summary-copy">
                    <span class="users-summary-label">{{ __('dashboard.advance.pending_advances') }}</span>
                    <div class="users-summary-value-wrap">
                        <div class="advance-summary-value-block">
                            <strong class="advance-summary-amount">{{ number_format($advanceSummary['pending_advances'], 2) }}</strong>
                            <span class="advance-summary-currency">AFG</span>
                        </div>
                    </div>
                </div>
            </article>
        </div>

        <div class="col-sm-6 col-xl-4">
            <article class="users-summary-card advance-summary-card-deducted">
                <span class="users-summary-icon" aria-hidden="true">
                    <i class="bi bi-check-circle-fill"></i>
                </span>
                <div class="users-summary-copy">
                    <span class="users-summary-label">{{ __('dashboard.advance.deducted_advances') }}</span>
                    <div class="users-summary-value-wrap">
                        <div class="advance-summary-value-block">
                            <strong class="advance-summary-amount">{{ number_format($advanceSummary['deducted_advances'], 2) }}</strong>
                            <span class="advance-summary-currency">AFG</span>
                        </div>
                    </div>
                </div>
            </article>
        </div>
    </div>

    <div class="row g-4 advance-chart-grid">
        <div class="col-xl-5">
            <section class="advance-chart-panel advance-chart-panel-trend">
                <div class="advance-chart-head">
                    <div>
                        <h4 class="advance-chart-title">{{ __('dashboard.advance.trend_title') }}</h4>
                        <p class="advance-chart-text mb-0">{{ __('dashboard.advance.trend_text') }}</p>
                    </div>
                    <span class="advance-chart-badge">{{ __('dashboard.advance.trend_badge') }}</span>
                </div>
                <div class="advance-chart-canvas-wrap advance-chart-canvas-wrap-md">
                    <canvas id="advanceTrendChart"></canvas>
                </div>
            </section>
        </div>

        <div class="col-xl-7">
            <section class="advance-chart-panel advance-chart-panel-comparison">
                <div class="advance-chart-head">
                    <div>
                        <h4 class="advance-chart-title">{{ __('dashboard.advance.comparison_title') }}</h4>
                        <p class="advance-chart-text mb-0">{{ __('dashboard.advance.comparison_text') }}</p>
                    </div>
                    <span class="advance-chart-badge">{{ __('dashboard.advance.comparison_badge') }}</span>
                </div>
                <div class="advance-chart-canvas-wrap advance-chart-canvas-wrap-lg">
                    <canvas id="advanceComparisonChart"></canvas>
                </div>
            </section>
        </div>
    </div>
</section>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
    <script>
        (() => {
            const trendCanvas = document.getElementById('advanceTrendChart');
            const comparisonCanvas = document.getElementById('advanceComparisonChart');

            if (!trendCanvas || !comparisonCanvas || trendCanvas.dataset.ready === 'true') {
                return;
            }

            trendCanvas.dataset.ready = 'true';

            const trendData = @json($advanceSummary['monthly_trend']);
            const comparisonData = @json($advanceSummary['advance_vs_deduction']);
            const chartLabelColor = '#667085';
            const chartGridColor = 'rgba(148, 163, 184, 0.2)';
            const chartBorderColor = 'rgba(226, 232, 240, 0.8)';

            new Chart(trendCanvas, {
                type: 'bar',
                data: {
                    labels: trendData.map(item => item.label),
                    datasets: [{
                        label: @json(__('dashboard.advance.advances_dataset')),
                        data: trendData.map(item => item.amount),
                        backgroundColor: 'rgba(239, 68, 68, 0.82)',
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

            new Chart(comparisonCanvas, {
                type: 'line',
                data: {
                    labels: comparisonData.map(item => item.label),
                    datasets: [
                        {
                            label: @json(__('dashboard.advance.issued_dataset')),
                            data: comparisonData.map(item => item.advanced),
                            borderColor: '#ef4444',
                            backgroundColor: 'rgba(239, 68, 68, 0.12)',
                            pointBackgroundColor: '#ef4444',
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 2,
                            pointRadius: 3,
                            pointHoverRadius: 4,
                            fill: false,
                            tension: 0.3
                        },
                        {
                            label: @json(__('dashboard.advance.deducted_dataset')),
                            data: comparisonData.map(item => item.deducted),
                            borderColor: '#16a34a',
                            backgroundColor: 'rgba(22, 163, 74, 0.12)',
                            pointBackgroundColor: '#16a34a',
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 2,
                            pointRadius: 3,
                            pointHoverRadius: 4,
                            fill: false,
                            tension: 0.3
                        }
                    ]
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
        })();
    </script>
@endpush
