@extends('themes.main')

@section('title', 'Dashboard')

@php
    // Dashboard data processing logic
    $totalProducts = 5483;
    $totalOrders = 2859;
    $totalStock = 5483;
    $outOfStock = 38;
    $itemsNearExpiry = 127;
    $criticalItems = 23;
    $warningItems = 104;

    // Chart data arrays
    $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
    $profitData = [35, 42, 38, 55, 48, 62];
    $salesData = [440, 510, 470, 590, 630, 680];
    $stockData = [700, 850, 720, 950, 880, 1020];

    // Top products data
    $topProducts = [
        ['Wireless Mouse', 92],
        ['USB-C Cable', 85],
        ['Laptop Stand', 78],
        ['Mechanical Keyboard', 71],
        ['Desk Lamp', 65],
    ];

    // Sample expiry data
    $criticalExpiryItems = [
        ['Sample Item A', '#12345', 'Mar 14, 2026'],
        ['Sample Item B', '#12346', 'Mar 15, 2026'],
        ['Sample Item C', '#12347', 'Mar 16, 2026'],
    ];

    // Calculate percentages and statistics
    $stockUtilization = round((($totalProducts - $outOfStock) / $totalProducts) * 100, 1);
    $orderFulfillmentRate = round(($totalOrders / ($totalOrders + $outOfStock)) * 100, 1);
    $expiryRiskLevel = $criticalItems > 50 ? 'high' : ($criticalItems > 20 ? 'medium' : 'low');

    // Generate CSS color classes dynamically
    $statCards = [
        [
            'icon' => 'fas fa-box',
            'value' => number_format($totalProducts),
            'label' => 'Total Products',
            'color' => 'green',
        ],
        [
            'icon' => 'fas fa-shopping-cart',
            'value' => number_format($totalOrders),
            'label' => 'Orders',
            'color' => 'blue',
        ],
        [
            'icon' => 'fas fa-chart-line',
            'value' => number_format($totalStock),
            'label' => 'Total Stock',
            'color' => 'purple',
        ],
        [
            'icon' => 'fas fa-exclamation-triangle',
            'value' => $outOfStock,
            'label' => 'Out of Stock',
            'color' => 'orange',
        ],
    ];
@endphp

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endsection

@section('content')
    <div class="lome-dashboard">
        flex-shrink: 0;
        }

        /* Modern Palette */
        .pill-green {
        background: #f0fdf4;
        color: #16a34a;
        }

        .pill-blue {
        background: #eff6ff;
        color: #2563eb;
        }

        .pill-purple {
        background: #faf5ff;
        color: #7c3aed;
        }

        .pill-orange {
        background: #fff7ed;
        color: #ea580c;
        }

        .stat-val {
        font-size: 26px;
        font-weight: 800;
        color: #0f172a;
        line-height: 1;
        }

        .stat-label {
        font-size: 13px;
        color: #64748b;
        font-weight: 600;
        margin-top: 4px;
        }

        /* --- Main Content Cards --- */
        .content-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 24px;
        padding: 28px;
        height: 100%;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.04);
        }

        .card-head {
        margin-bottom: 25px;
        }

        .card-head h3 {
        font-size: 18px;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
        }

        /* --- Sleek Progress Bars (Medium Size) --- */
        .sales-item {
        margin-bottom: 18px;
        }

        .progress-track {
        height: 10px;
        /* Sleek medium height */
        background: #f1f5f9;
        border-radius: 10px;
        overflow: hidden;
        margin-top: 8px;
        }

        .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #6366f1, #a855f7);
        border-radius: 10px;
        }

        /* --- Expiration Center Actions --- */
        .exp-btn {
        width: 100%;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        padding: 16px;
        border-radius: 14px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 12px;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
        }

        .exp-btn:hover {
        border-color: #6366f1;
        background: #f8fafc;
        transform: translateX(5px);
        }

        .text-crit {
        color: #dc2626;
        font-weight: 700;
        }

        .text-warn {
        color: #d97706;
        font-weight: 700;
        }

        /* Modal Styling */
        .modal-content {
        border-radius: 24px;
        border: none;
        padding: 15px;
        }

        .modal-header {
        border-bottom: none;
        }

        .modal-title {
        font-weight: 800;
        color: #0f172a;
        }
        </style>

        <div class="lome-dashboard">
            <!-- Enhanced Dashboard Header with PHP Logic -->
            <div class="dash-header">
                <h1>Dashboard</h1>
                <p>Overview - Last updated: {{ date('M j, Y \a\t g:i A') }}</p>
                @php
                    $currentHour = date('H');
                    $greeting =
                        $currentHour < 12 ? 'Good Morning' : ($currentHour < 18 ? 'Good Afternoon' : 'Good Evening');
                @endphp
                <p class="text-muted">{{ $greeting }}, {{ auth()->user()->name ?? 'User' }}!</p>
            </div>

            <!-- Dashboard Metrics Summary -->
            <div class="dashboard-metrics">
                <div class="row">
                    <div class="col-md-3 metric-item">
                        <div class="metric-value">{{ $stockUtilization }}%</div>
                        <div class="metric-label">Stock Utilization</div>
                    </div>
                    <div class="col-md-3 metric-item">
                        <div class="metric-value">{{ $orderFulfillmentRate }}%</div>
                        <div class="metric-label">Order Fulfillment</div>
                    </div>
                    <div class="col-md-3 metric-item">
                        <div class="metric-value">
                            <span class="status-indicator status-{{ $expiryRiskLevel }}"></span>
                            {{ $expiryRiskLevel }}
                        </div>
                        <div class="metric-label">Expiry Risk Level</div>
                    </div>
                    <div class="col-md-3 metric-item">
                        <div class="metric-value">{{ number_format($totalProducts - $totalStock) }}</div>
                        <div class="metric-label">Available Stock</div>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards with PHP Loop -->
            <div class="row dashboard-row">
                @foreach ($statCards as $card)
                    <div class="col-md-3">
                        <x-stat-card :icon="$card['icon']" :value="$card['value']" :label="$card['label']" :color="$card['color']" />
                    </div>
                @endforeach
            </div>

            <div class="row dashboard-row">
                <div class="col-lg-7">
                    <x-chart-container title="Expense vs Profit" subtitle="Monthly financial overview" id="expenseChart" />
                </div>

                <div class="col-lg-5">
                    <div class="content-card">
                        <div class="card-head">
                            <h3>Top Sales Product</h3>
                            @php
                                $totalTopSales = array_sum(array_column($topProducts, 1));
                                $averagePerformance = round($totalTopSales / count($topProducts), 1);
                            @endphp
                            <p class="text-muted small">Average Performance: {{ $averagePerformance }}%</p>
                        </div>
                        @foreach ($topProducts as $index => $product)
                            @php
                                $rank = $index + 1;
                                $performanceClass =
                                    $product[1] >= 85
                                        ? 'text-success'
                                        : ($product[1] >= 70
                                            ? 'text-warning'
                                            : 'text-danger');
                            @endphp
                            <div class="sales-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="small font-weight-bold">
                                        <span class="badge badge-secondary mr-2">{{ $rank }}</span>
                                        {{ $product[0] }}
                                    </span>
                                    <span
                                        class="small {{ $performanceClass }} font-weight-bold">{{ $product[1] }}%</span>
                                </div>
                                <div class="progress-track">
                                    <div class="progress-fill" style="width: {{ $product[1] }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="row dashboard-row">
                <div class="col-lg-8">
                    <x-chart-container title="Monthly Inventory vs Sales" subtitle="Stock levels and sales performance"
                        id="comboChart" />
                </div>

                <div class="col-lg-4">
                    <div class="content-card">
                        <div class="card-head">
                            <h3>Expiration Center</h3>
                            @php
                                $daysUntilNextExpiry = 7; // This could come from database
                                $urgentItems = $criticalItems + $warningItems;
                            @endphp
                            <p class="text-muted small">Next expiry in {{ $daysUntilNextExpiry }} days</p>
                        </div>
                        <div class="d-flex align-items-center gap-3 mb-4">
                            <div class="pill-icon pill-orange" style="width: 60px; height: 60px;">
                                <i class="fas fa-hourglass-half"></i>
                            </div>
                            <div>
                                <h2 class="m-0" style="font-size: 32px; font-weight: 800;">
                                    {{ number_format($itemsNearExpiry) }}</h2>
                                <span class="small text-muted">Items Near Expiry</span>
                            </div>
                        </div>

                        @php
                            $criticalPercentage = round(($criticalItems / $itemsNearExpiry) * 100);
                            $warningPercentage = round(($warningItems / $itemsNearExpiry) * 100);
                        @endphp

                        <button class="exp-btn" data-toggle="modal" data-target="#crit7Modal">
                            <div>
                                <span class="small font-weight-bold">Critical (7 Days)</span>
                                <div class="text-muted small">{{ $criticalPercentage }}% of expiring items</div>
                            </div>
                            <span class="text-crit">{{ $criticalItems }} items <i
                                    class="fas fa-chevron-right ml-2"></i></span>
                        </button>

                        <button class="exp-btn" data-toggle="modal" data-target="#warn30Modal">
                            <div>
                                <span class="small font-weight-bold">Warning (30 Days)</span>
                                <div class="text-muted small">{{ $warningPercentage }}% of expiring items</div>
                            </div>
                            <span class="text-warn">{{ $warningItems }} items <i
                                    class="fas fa-chevron-right ml-2"></i></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="crit7Modal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Critical Expiration (Next 7 Days)</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                        @php
                            $totalValue = 0;
                            foreach ($criticalExpiryItems as $item) {
                                $totalValue += rand(50, 500); // Simulated value calculation
                            }
                        @endphp
                        <div class="alert alert-danger">
                            <strong>Urgent Action Required:</strong> {{ count($criticalExpiryItems) }} items expiring
                            within 7 days.
                            Total estimated value: ${{ number_format($totalValue) }}
                        </div>
                        <div class="table-responsive">
                            <table class="table data-table">
                                <thead>
                                    <tr>
                                        <th>PRODUCT</th>
                                        <th>SKU</th>
                                        <th>EXPIRY</th>
                                        <th>DAYS LEFT</th>
                                        <th>ACTION</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($criticalExpiryItems as $item)
                                        @php
                                            $expiryDate = strtotime($item[2]);
                                            $today = strtotime(date('M j, Y'));
                                            $daysLeft = ceil(($expiryDate - $today) / (60 * 60 * 24));
                                            $urgencyClass =
                                                $daysLeft <= 3 ? 'text-danger font-weight-bold' : 'text-warning';
                                        @endphp
                                        <tr>
                                            <td>{{ $item[0] }}</td>
                                            <td>{{ $item[1] }}</td>
                                            <td class="{{ $urgencyClass }}">{{ $item[2] }}</td>
                                            <td><span
                                                    class="badge badge-{{ $daysLeft <= 3 ? 'danger' : 'warning' }}">{{ $daysLeft }}
                                                    days</span></td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary">Mark as Used</button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="warn30Modal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Warning Expiration (Next 30 Days)</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                        @php
                            $categories = ['Electronics', 'Food', 'Medicine', 'Chemicals'];
                            $categoryCounts = [];
                            foreach ($categories as $cat) {
                                $categoryCounts[$cat] = rand(10, 40);
                            }
                            $totalWarningItems = array_sum($categoryCounts);
                        @endphp
                        <div class="summary-card">
                            <div class="summary-title">Items by Category</div>
                            <div class="row">
                                @foreach ($categoryCounts as $category => $count)
                                    <div class="col-md-3">
                                        <div class="summary-value">{{ $count }}</div>
                                        <div class="metric-label">{{ $category }}</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <p class="text-muted">Total items expiring within 30 days: {{ $totalWarningItems }}</p>
                        <div class="table-responsive">
                            <table class="table data-table">
                                <thead>
                                    <tr>
                                        <th>CATEGORY</th>
                                        <th>COUNT</th>
                                        <th>PERCENTAGE</th>
                                        <th>STATUS</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($categoryCounts as $category => $count)
                                        @php
                                            $percentage = round(($count / $totalWarningItems) * 100, 1);
                                            $status =
                                                $percentage > 30
                                                    ? 'High Risk'
                                                    : ($percentage > 15
                                                        ? 'Medium Risk'
                                                        : 'Low Risk');
                                            $statusClass =
                                                $percentage > 30
                                                    ? 'danger'
                                                    : ($percentage > 15
                                                        ? 'warning'
                                                        : 'success');
                                        @endphp
                                        <tr>
                                            <td>{{ $category }}</td>
                                            <td>{{ $count }}</td>
                                            <td>{{ $percentage }}%</td>
                                            <td><span class="badge badge-{{ $statusClass }}">{{ $status }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection

    @section('scripts src')

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Profit Line Chart
                new Chart(document.getElementById('expenseChart'), {
                    type: 'line',
                    data: {
                        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                        datasets: [{
                            label: 'Profit',
                            data: [35, 42, 38, 55, 48, 62],
                            borderColor: '#6366f1',
                            backgroundColor: 'rgba(99, 102, 241, 0.05)',
                            fill: true,
                            tension: 0.4,
                            pointRadius: 5,
                            pointBackgroundColor: '#fff',
                            pointBorderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                grid: {
                                    // To hide the grid lines entirely
                                    display: true,

                                    // To make the lines dashed (like in your 'Expense vs Profit' image)
                                    borderDash: [5, 5],

                                    // Change the color to match a soft professional look
                                    color: '#e2e8f0',

                                    // Remove the line on the very edge of the axis
                                    drawBorder: false,
                                },
                                ticks: {
                                    // Customizing the labels on the side
                                    color: '#64748b',
                                    font: {
                                        family: 'Plus Jakarta Sans'
                                    }
                                }
                            },
                            x: {
                                grid: {
                                    // Usually, the x-axis grid is hidden for a cleaner look
                                    display: false,
                                    drawBorder: false
                                }
                            }
                        },
                        option: {
                            plugins: {
                                legend: {
                                    display: false
                                }
                            }

                        }
                    }

                });

                // Modern Combo Chart (Inventory vs Sales)
                new Chart(document.getElementById('comboChart'), {
                    data: {
                        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                        datasets: [{
                            type: 'bar',
                            label: 'Items Sold',
                            data: [440, 510, 470, 590, 630, 680],
                            backgroundColor: '#6366f1',
                            borderRadius: 8,
                            barThickness: 22
                        }, {
                            type: 'line',
                            label: 'Stock Level',
                            data: [700, 850, 720, 950, 880, 1020],
                            borderColor: '#10b981',
                            borderWidth: 3,
                            tension: 0,
                            fill: false,
                            pointStyle: 'circle'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            x: {
                                grid: {
                                    display: false
                                }
                            },
                            y: {
                                grid: {
                                    borderDash: [5, 5]
                                }
                            }
                        }
                    }
                });
            });
        </script>
    @endsection
