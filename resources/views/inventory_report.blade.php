@extends('themes.main')

@section('title', 'Financial Reports')

@section('content')
    <div class="container-fluid py-4" style="background-color: #f8f9fc;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold text-dark mb-1">Financial Reports</h3>
                <p class="text-muted small">Comprehensive financial analysis and inventory profitability reports</p>
            </div>
            <button class="btn btn-primary px-4 py-2 rounded-3 shadow-sm">
                <i class="bi bi-download me-2"></i> Export PDF
            </button>
        </div>

        <form id="reportFilters" method="GET" action="{{ route('inventory_report') }}">
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <select name="period" class="form-select border-0 shadow-sm py-2">
                        <option value="this_month" {{ ($period ?? 'this_month') === 'this_month' ? 'selected' : '' }}>This Month</option>
                        <option value="last_quarter" {{ ($period ?? '') === 'last_quarter' ? 'selected' : '' }}>Last Quarter</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <select name="category_id" class="form-select border-0 shadow-sm py-2">
                        <option value="all" {{ ($categoryId ?? 'all') === 'all' ? 'selected' : '' }}>All Categories</option>
                        @foreach(($categories ?? []) as $cat)
                            <option value="{{ $cat->category_ID }}" {{ (string)($categoryId ?? 'all') === (string)$cat->category_ID ? 'selected' : '' }}>
                                {{ $cat->category_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </form>

        <div class="row g-3 mb-4">
            @foreach (($stats ?? []) as $stat)
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between mb-3">
                                <div class="p-2 rounded-3 bg-{{ $stat['color'] }}-subtle text-{{ $stat['color'] }}">
                                    <i class="bi bi-{{ $stat['icon'] }} fs-4"></i>
                                </div>
                                <span
                                    class="badge bg-{{ $stat['badgeStyle'] ?? 'secondary' }}-subtle text-{{ $stat['badgeStyle'] ?? 'secondary' }} rounded-pill align-self-center">
                                    {{ $stat['badge'] ?? '—' }}
                                </span>
                            </div>
                            <h3 class="fw-bold mb-1">{{ $stat['value'] }}</h3>
                            <p class="text-muted small mb-0">{{ $stat['label'] }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-8">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-1">Revenue & Profit Analysis</h6>
                        <p class="text-muted small mb-4">6-month financial performance trend</p>
                        <div style="height: 300px;">
                            <canvas id="financialTrendChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-4">Revenue Sources</h6>
                        <div style="height: 250px;">
                            <canvas id="revenueSourcesChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-md-7">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-4">Top Products by Profit</h6>
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead class="text-muted small">
                                    <tr>
                                        <th>PRODUCT</th>
                                        <th>REVENUE</th>
                                        <th>PROFIT</th>
                                        <th>MARGIN</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse(($topProductsByProfit ?? []) as $row)
                                        @php
                                            $margin = (float) ($row['margin'] ?? 0);
                                            $marginWidth = max(0, min(100, $margin));
                                            $profit = (float) ($row['profit'] ?? 0);
                                        @endphp
                                        <tr>
                                            <td class="fw-bold">{{ $row['name'] ?? '—' }}</td>
                                            <td>₱{{ number_format((float)($row['revenue'] ?? 0), 2) }}</td>
                                            <td class="{{ $profit >= 0 ? 'text-success' : 'text-danger' }} fw-bold">
                                                ₱{{ number_format($profit, 2) }}
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <span class="me-2 small">{{ number_format($margin, 1) }}%</span>
                                                    <div class="progress w-100" style="height: 6px;">
                                                        <div class="progress-bar {{ $profit >= 0 ? 'bg-success' : 'bg-danger' }}"
                                                            style="width: {{ $marginWidth }}%"></div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-muted small">No data for selected filters.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-4">Expense Breakdown</h6>
                        <ul class="list-unstyled">
                            @forelse(($expenseBreakdown ?? []) as $row)
                                <li class="mb-4">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="small fw-semibold">{{ $row['label'] ?? '—' }}</span>
                                        <span class="small text-muted">{{ (int)($row['percent'] ?? 0) }}%
                                            <strong>₱{{ number_format((float)($row['total'] ?? 0), 2) }}</strong>
                                        </span>
                                    </div>
                                    <div class="progress rounded-pill" style="height: 8px;">
                                        <div class="progress-bar bg-primary" style="width: {{ (int)($row['percent'] ?? 0) }}%"></div>
                                    </div>
                                </li>
                            @empty
                                <li class="mb-4">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="small fw-semibold">No data</span>
                                        <span class="small text-muted">0% <strong>₱0.00</strong></span>
                                    </div>
                                    <div class="progress rounded-pill" style="height: 8px;">
                                        <div class="progress-bar bg-primary" style="width: 0%"></div>
                                    </div>
                                </li>
                            @endforelse
                        </ul>
                        <hr>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold">Total Expenses</span>
                            <h4 class="fw-bold mb-0">₱{{ number_format((float)($expenseTotal ?? 0), 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('reportFilters');
            if (!form) return;
            form.querySelectorAll('select').forEach((el) => {
                el.addEventListener('change', () => form.submit());
            });
        });
    </script>

    <style>
        .bg-primary-subtle {
            background-color: #e3f2fd !important;
        }

        .text-primary {
            color: #0d6efd !important;
        }

        .bg-success-subtle {
            background-color: #e8f5e9 !important;
        }

        .text-success {
            color: #2e7d32 !important;
        }

        .bg-purple-subtle {
            background-color: #f3e5f5 !important;
        }

        .text-purple {
            color: #8e24aa !important;
        }

        .bg-orange-subtle {
            background-color: #fff3e0 !important;
        }

        .text-orange {
            color: #ef6c00 !important;
        }
    </style>
@endsection


@section('scripts src')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const trendLabels = @json(($trendChart['labels'] ?? []));
            const trendRevenue = @json(($trendChart['revenue'] ?? []));
            const trendProfit = @json(($trendChart['profit'] ?? []));

            const sourceLabels = @json(($sourcesChart['labels'] ?? []));
            const sourceValues = @json(($sourcesChart['values'] ?? []));

            const formatPesoCompact = (value) => {
                const v = Number(value) || 0;
                if (Math.abs(v) >= 1_000_000) return '₱' + (v / 1_000_000).toFixed(1) + 'M';
                if (Math.abs(v) >= 1_000) return '₱' + (v / 1_000).toFixed(1) + 'k';
                return '₱' + v.toFixed(0);
            };

            // 1. Revenue & Profit Trend Chart
            const trendCtx = document.getElementById('financialTrendChart');
            if (trendCtx) {
                new Chart(trendCtx, {
                    type: 'line',
                    data: {
                        labels: trendLabels,
                        datasets: [{
                                label: 'Revenue',
                                data: trendRevenue,
                                borderColor: '#4489fe',
                                backgroundColor: 'rgba(68, 137, 254, 0.1)',
                                fill: true,
                                tension: 0.4
                            },
                            {
                                label: 'Profit',
                                data: trendProfit,
                                borderColor: '#10b981',
                                backgroundColor: 'rgba(16, 185, 129, 0.08)',
                                fill: true,
                                tension: 0.4
                            }
                        ]
                    },
                    options: {
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true
                            }
                        },
                        scales: {
                            y: {
                                ticks: {
                                    callback: v => formatPesoCompact(v)
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            }

            // 2. Revenue Sources (Doughnut)
            const sourceCtx = document.getElementById('revenueSourcesChart');
            if (sourceCtx) {
                const sourceColors = ['#4489fe', '#8e24aa', '#10b981'];
                new Chart(sourceCtx, {
                    type: 'doughnut',
                    data: {
                        labels: sourceLabels,
                        datasets: [{
                            data: sourceValues,
                            backgroundColor: sourceColors.slice(0, sourceValues.length),
                            borderWidth: 0
                        }]
                    },
                    options: {
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        },
                        cutout: '75%'
                    }
                });
            }
        });
    </script>
@endsection
