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

        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <select class="form-select border-0 shadow-sm py-2">
                    <option>This Month</option>
                    <option>Last Quarter</option>
                </select>
            </div>
            <div class="col-md-4">
                <select class="form-select border-0 shadow-sm py-2">
                    <option>All Categories</option>
                </select>
            </div>
        </div>

        <div class="row g-3 mb-4">
            @php
                $stats = [
                    [
                        'label' => 'Total Revenue',
                        'value' => '$292.8k',
                        'change' => '5.1%',
                        'icon' => 'currency-dollar',
                        'color' => 'primary',
                    ],
                    [
                        'label' => 'Net Profit',
                        'value' => '$70.8k',
                        'change' => '4.6%',
                        'icon' => 'wallet2',
                        'color' => 'success',
                    ],
                    [
                        'label' => 'Gross Margin',
                        'value' => '40.0%',
                        'status' => 'Healthy',
                        'icon' => 'percent',
                        'color' => 'purple',
                    ],
                    [
                        'label' => 'Net Margin',
                        'value' => '24.2%',
                        'status' => 'Strong',
                        'icon' => 'graph-up',
                        'color' => 'orange',
                    ],
                ];
            @endphp

            @foreach ($stats as $stat)
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between mb-3">
                                <div class="p-2 rounded-3 bg-{{ $stat['color'] }}-subtle text-{{ $stat['color'] }}">
                                    <i class="bi bi-{{ $stat['icon'] }} fs-4"></i>
                                </div>
                                <span
                                    class="badge bg-{{ isset($stat['change']) ? 'success' : 'warning' }}-subtle text-{{ isset($stat['change']) ? 'success' : 'orange' }} rounded-pill align-self-center">
                                    {{ $stat['change'] ?? $stat['status'] }}
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
                                    <tr>
                                        <td class="fw-bold">Organic Milk (1 Gallon)</td>
                                        <td>$42,150</td>
                                        <td class="text-success fw-bold">$12,645</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span class="me-2 small">30%</span>
                                                <div class="progress w-100" style="height: 6px;">
                                                    <div class="progress-bar bg-success" style="width: 30%"></div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
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
                            <li class="mb-4">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="small fw-semibold">Store Operations</span>
                                    <span class="small text-muted">40% <strong>$18,536</strong></span>
                                </div>
                                <div class="progress rounded-pill" style="height: 8px;">
                                    <div class="progress-bar bg-primary" style="width: 40%"></div>
                                </div>
                            </li>
                        </ul>
                        <hr>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold">Total Expenses</span>
                            <h4 class="fw-bold mb-0">$46,340</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

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

            // 1. Revenue & Profit Trend Chart
            const trendCtx = document.getElementById('financialTrendChart');
            if (trendCtx) {
                new Chart(trendCtx, {
                    type: 'line',
                    data: {
                        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                        datasets: [{
                            label: 'Revenue',
                            data: [180, 160, 210, 240, 260, 292],
                            borderColor: '#4489fe',
                            backgroundColor: 'rgba(68, 137, 254, 0.1)',
                            fill: true,
                            tension: 0.4
                        }]
                    },
                    options: {
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                ticks: {
                                    callback: v => '$' + v + 'k'
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
                new Chart(sourceCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Deli Counter', 'Prepared Foods', 'Other'],
                        datasets: [{
                            data: [75, 16, 9],
                            backgroundColor: ['#4489fe', '#8e24aa', '#10b981'],
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
