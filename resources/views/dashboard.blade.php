@extends('themes.main')

{{-- Define the page title that goes into the <title> tag in head.blade.php --}}
@section('title', 'Dashboard')


{{-- This section replaces the content-header section in the master layout --}}
@section('content_header')

    <style>
        /* --- Dashboard Layout --- */
        .dash-header-section {
            background-color: rgba(252, 252, 252, 0.876);
            transition: background-color 0.3s ease;
        }

        /* --- Stat Cards --- */
        .dash-stat-card {
            background: rgb(255, 255, 255);
            padding: 20px;
            border-radius: 20px;
            border: 1px solid #f0f0f0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            height: 155px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: all 0.3s ease;
        }

        .dash-stat-title {
            color: #64748b;
            font-size: 14px;
            font-weight: 500;
        }

        .dash-stat-value {
            font-size: 32px;
            font-weight: 700;
            margin: 0;
            color: #1e293b;
        }

        .dash-stat-subtext {
            color: #94a3b8;
            font-size: 11px;
        }

        /* --- Recent Activity --- */
        .dash-activity-item {
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }

        .dash-activity-text {
            margin: 0;
            font-size: 14px;
            color: #555;
        }

        .dash-activity-time {
            font-size: 12px;
            color: gray;
        }

        /* =========================================================
                                                                                                                                                                                                                                                                                                                                                                                                                                                                         DARK MODE OVERRIDES (Only triggers when .dark-mode is active)
                                                                                                                                                                                                                                                                                                                                                                                                                                                                         ========================================================= */

        .dark-mode .dash-header-section {
            background-color: #121212 !important;
        }

        .dark-mode .dash-stat-card,
        .dark-mode .chart-container,
        .dark-mode .card {
            background-color: #1a1a1a !important;
            border-color: #333 !important;
        }

        .dark-mode .dash-stat-value,
        .dark-mode .dash-activity-text,
        .dark-mode .chart-container canvas,
        label,
        input,
        select,
        text {
            color: #e0e0e0 !important;
        }

        .dark-mode h1,
        .dark-mode h3,
        .dark-mode h4,
        .dark-mode p {
            color: #ffffff !important;
        }

        .dark-mode .dash-stat-title,
        .dark-mode .dash-stat-subtext,
        .dark-mode .dash-activity-time {
            color: #94a3b8 !important;
        }

        .dark-mode .dash-activity-item {
            border-bottom-color: #333 !important;
        }
    </style>



    <div class="col dash-header-section">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="font-weight-bold text-dark mb-0">Dashboard</h1>
                    </div>
                </div>
            </div>
        </div>
    @endsection



    {{-- This section replaces the main content block --}}
    @section('content')


        <div class="row mb-4 g-3" style="font-family: 'Inter', sans-serif;">
            <div class="col-lg-3 col-6">
                <div class="dash-stat-card"
                    style="background: white; padding: 20px; border-radius: 20px; border: 1px solid #f0f0f0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); height: 155px; display: flex; flex-direction: column; justify-content: space-between;">
                    <div>
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="color: #64748b; font-size: 14px; font-weight: 500;">Total Products</span>

                        </div>
                    </div>
                    <div>
                        <div style="display: flex; justify-content: space-between; align-items: flex-end;">
                            <h3 style="font-size: 32px; font-weight: 700; margin: 0; color: #1e293b;">
                                {{ number_format($totalProducts) }}</h3>
                            {{-- Accurate Percentage Design --}}
                            @php
                                $isPositive = $quantityPercent >= 0;
                                $bgColor = $isPositive ? '#f0fdf4' : '#fef2f2';
                                $textColor = $isPositive ? '#16a34a' : '#dc2626';
                                $icon = $isPositive ? '↗' : '↘';
                            @endphp
                            <div
                                style="background: {{ $bgColor }}; color: {{ $textColor }}; padding: 4px 8px; border-radius: 10px; font-size: 12px; font-weight: 600; display: flex; align-items: center; gap: 4px;">
                                {{ $icon }}
                                {{ $quantityPercent }} %
                            </div>
                        </div>
                        <small style="color: #94a3b8; font-size: 11px;">From last week</small>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="dash-stat-card"
                    style="background: white; padding: 20px; border-radius: 20px; border: 1px solid #f0f0f0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); height: 155px; display: flex; flex-direction: column; justify-content: space-between;">
                    <div>
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="color: #64748b; font-size: 14px; font-weight: 500;">Available Stock</span>

                        </div>
                    </div>
                    <div>
                        <div style="display: flex; justify-content: space-between; align-items: flex-end;">
                            <h3 style="font-size: 32px; font-weight: 700; margin: 0; color: #1e293b;">
                                {{ number_format($totalQuantity) }}</h3>
                            <div
                                style="background: #fef2f2; color: #dc2626; padding: 4px 8px; border-radius: 10px; font-size: 12px; font-weight: 600; display: flex; align-items: center; gap: 4px;">
                                ↘ 1.24%
                            </div>
                        </div>
                        <small style="color: #94a3b8; font-size: 11px;">From last week</small>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="dash-stat-card"
                    style="background: white; padding: 20px; border-radius: 20px; border: 1px solid #f0f0f0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); height: 155px; display: flex; flex-direction: column; justify-content: space-between;">
                    <div>
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="color: #64748b; font-size: 14px; font-weight: 500;">Low Stock</span>

                        </div>
                    </div>
                    <div>
                        <div style="display: flex; justify-content: space-between; align-items: flex-end;">
                            <h3 style="font-size: 32px; font-weight: 700; margin: 0; color: #1e293b;">
                                {{ number_format($lowStockProducts) }}</h3>
                            <div
                                style="background: #f0fdf4; color: #16a34a; padding: 4px 8px; border-radius: 10px; font-size: 12px; font-weight: 600; display: flex; align-items: center; gap: 4px;">
                                ↗ 1.52%
                            </div>
                        </div>
                        <small style="color: #94a3b8; font-size: 11px;">From last week</small>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="dash-stat-card"
                    style="background: white; padding: 20px; border-radius: 20px; border: 1px solid #f0f0f0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); height: 155px; display: flex; flex-direction: column; justify-content: space-between;">
                    <div>
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="color: #64748b; font-size: 14px; font-weight: 500;">Out of Stock</span>

                        </div>
                    </div>
                    <div>
                        <div style="display: flex; justify-content: space-between; align-items: flex-end;">
                            <h3 style="font-size: 32px; font-weight: 700; margin: 0; color: #1e293b;">
                                {{ number_format($outOfStock) }}</h3>
                            <div
                                style="background: #fef2f2; color: #dc2626; padding: 4px 8px; border-radius: 10px; font-size: 12px; font-weight: 600; display: flex; align-items: center; gap: 4px;">
                                ↘ 1.55%
                            </div>
                        </div>
                        <small style="color: #94a3b8; font-size: 11px;">From last week</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">

            <section class="col-md-8 ">

                <!-- Line Graph Section -->
                <div class="chart-container bg-white p-3 "
                    style="border-radius: 30px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.222);">
                    <canvas id="lineChart"></canvas>
                </div>
                <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                <script>
                    const labels = ['jan', 'feb', 'mar', 'apr', 'may'];
                    const TotalStockData = [500, 700, 400, 600, 800];
                    const SoldData = [300, 400, 200, 350, 500];

                    const ctx = document.getElementById('lineChart').getContext('2d');

                    new Chart(ctx, {

                        data: {
                            labels: labels,
                            datasets: [{
                                    type: 'line',
                                    label: 'Sold',
                                    data: SoldData,
                                    borderColor: '#8b5cf6',
                                    backgroundColor: 'rgba(139, 92, 246, 0.2)',
                                    fill: false,
                                    tension: 0.4,
                                    pointRadius: 5,
                                    pointBackgroundColor: '#8b5cf6'
                                },
                                {
                                    type: 'bar',
                                    label: 'Total Stock',
                                    data: TotalStockData,
                                    borderColor: '#87CEEB',
                                    backgroundColor: 'rgba(135, 206, 235, 0.6)',
                                    fill: true,
                                    tension: 0.4,
                                    pointRadius: 5,

                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainingAspectRatio: false,
                            plugins: {
                                legend: {
                                    labels: {
                                        color: 'black'
                                    }
                                },
                                title: {
                                    display: true,
                                    text: 'Stock & Sold Over Time',
                                    color: 'black',
                                    font: {
                                        size: 18

                                    }
                                }
                            },
                            scales: {
                                x: {
                                    ticks: {
                                        color: '#64748b', // Professional slate grey
                                    },
                                    grid: {
                                        display: false,
                                        borderDash: [5, 5],
                                        drawTicks: false // Hides the vertical lines like in your image
                                    },
                                    border: {
                                        display: true // Removes the bottom solid line
                                    }
                                },
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        color: '#64748b',
                                        stepSize: 200 // Matches the 0, 200, 400, 600, 800 spacing in your image
                                    },
                                    grid: {
                                        color: '#f1f5f9', // Very light grey for the horizontal lines
                                        borderDash: [5, 5], // This creates the "cut lines"
                                        drawTicks: true,
                                    },
                                    border: {
                                        display: true // Removes the left solid line
                                    }
                                }
                            }
                        }
                    });
                </script>

            </section>


            <!-- PIE CHART -->

            <section class="col-md-4">
                <div class="card shadow-sm" style="border-radius: 20px; border: none; overflow: hidden;">

                    <div class="card-header border-0 bg-white pt-4 px-4">
                        <h3 class="card-title text-bold" style="font-size: 1.1rem; color: #1e293b;">Category Breakdown</h3>
                    </div>

                    <div class="card-body px-4 pb-4 pt-0">
                        <div class="chart-container mb-4" style="height: 220px; position: relative;">
                            <canvas id="categoryChart"></canvas>
                        </div>

                        <ul class="nav flex-column">
                            <li class="nav-item mb-2 border-bottom pb-2" style="border-color: #f1f5f9 !important;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted" style="font-size: 0.9rem;">
                                        <i class="fas fa-circle mr-2" style="color: #3b82f6;"></i> Electronics
                                    </span>
                                    <span class="font-weight-bold text-dark">1,250</span>
                                </div>
                            </li>
                            <li class="nav-item mb-2 border-bottom pb-2" style="border-color: #f1f5f9 !important;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted" style="font-size: 0.9rem;">
                                        <i class="fas fa-circle mr-2" style="color: #a78bfa;"></i> Accessories
                                    </span>
                                    <span class="font-weight-bold text-dark">980</span>
                                </div>
                            </li>
                            <li class="nav-item mb-2 border-bottom pb-2" style="border-color: #f1f5f9 !important;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted" style="font-size: 0.9rem;">
                                        <i class="fas fa-circle mr-2" style="color: #ec4899;"></i> Furniture
                                    </span>
                                    <span class="font-weight-bold text-dark">750</span>
                                </div>
                            </li>

                            <li class="nav-item mt-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="font-weight-bold text-dark" style="font-size: 1rem;">Total Items</span>
                                    <span class="font-weight-bold text-dark" style="font-size: 1rem;">4,092</span>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>


                <script>
                    $(function() {
                        // Select the canvas by ID
                        var doughnutCanvas = document.getElementById('categoryChart');

                        if (doughnutCanvas) {
                            new Chart(doughnutCanvas.getContext('2d'), {
                                type: 'doughnut',
                                data: {
                                    labels: ['Electronics', 'Accessories', 'Furniture', 'Stationery', 'Others'],
                                    datasets: [{
                                        data: [1250, 980, 750, 620, 492],
                                        backgroundColor: [
                                            '#3b82f6', // Blue
                                            '#a78bfa', // Purple
                                            '#ec4899', // Pink
                                            '#f59e0b', // Orange
                                            '#64748b' // Grey
                                        ],
                                        borderWidth: 5, // White space between segments
                                        hoverOffset: 10
                                    }]
                                },
                                options: {
                                    cutout: '70%', // Creates the "ring" look
                                    maintainAspectRatio: false,
                                    plugins: {
                                        legend: {
                                            display: false // We use the HTML list above instead
                                        },
                                        tooltip: {
                                            enabled: true
                                        }
                                    }
                                }
                            });
                        }
                    });
                </script>

                <script>
                    $(function() {
                        var ctx2 = document.getElementById('categoryChart').getContext('2d');
                        new Chart(ctx2, {
                            type: 'doughnut',
                            data: {
                                labels: ['Electronics', 'Accessories', 'Furniture', 'Stationery', 'Others'],
                                datasets: [{
                                    data: [1250, 980, 750, 620, 492],
                                    backgroundColor: ['#007bff', '#a78bfa', '#ec4899', '#ffc107', '#6c757d'],
                                    borderWidth: 4, // Adds white spacing between slices
                                    hoverOffset: 4
                                }]
                            },
                            options: {
                                cutout: '70%', // This creates the thin ring effect
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        display: false // We use our custom HTML legend instead
                                    }
                                }
                            }
                        });
                    });
                </script>
            </section>
        </div>

        <section class="col " style="margin-top: 20px;">
            <div class="container-fluid">
                <div class="col">
                    <div class="col-12">
                        <div class="card" style="border-radius: 10px; padding:15px;">

                            <!-- Role above Name -->
                            <h3 style="margin: 0; font-size: 20px; font-weight: 600; text-align:center;">
                                Welcome back!!
                            </h3>
                            <p style="text-align:center;"> {{ session('name') }}</p>

                            <!-- Activity Log Section -->
                            <div class="activity-log mt-4">
                                <h4 style="font-size: 18px; font-weight: 600; margin-bottom: 10px;">
                                    Recent Activity
                                </h4>
                                <ul style="list-style-type: none; padding: 0; max-height: 300px; overflow-y: auto;">
                                    @foreach ($logs as $log)
                                        @php
                                            // Set styles based on action
                                            switch ($log->action) {
                                                case 'added':
                                                    $bgColor = 'rgba(0, 128, 0, 0.2)'; // green
                                                    $textColor = '#28a745';
                                                    break;
                                                case 'updated':
                                                    $bgColor = 'rgba(255, 165, 0, 0.2)'; // orange
                                                    $textColor = 'orange';
                                                    break;
                                                case 'deleted':
                                                    $bgColor = 'rgba(255, 0, 0, 0.2)'; // red
                                                    $textColor = '#E3242B';
                                                    break;
                                                default:
                                                    $bgColor = 'transparent';
                                                    $textColor = '#000';
                                            }
                                        @endphp
                                        <li
                                            style="margin-bottom: 10px; padding-bottom: 10px; border-bottom: 1px solid #eee;">
                                            <p style="margin: 0; font-size: 14px; color: #555;">
                                                <strong
                                                    style="background: {{ $bgColor }}; color: {{ $textColor }}; padding: 3px 8px; border-radius: 12px;">
                                                    {{ ucfirst($log->action) }}
                                                </strong> - {{ $log->description }}
                                            </p>
                                            <span style="font-size: 12px; color: gray;">
                                                {{ $log->created_at->diffForHumans() }}
                                            </span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
        </section>
















    @endsection

    {{-- Inject the dashboard-specific scripts --}}
    @section('scripts')
        <script src="{{ asset('java/calendar.js') }}"></script>
        <script src="{{ asset('dist/js/demo.js') }}"></script>
        <script src="{{ asset('dist/js/pages/dashboard.js') }}"></script>

    @endsection
