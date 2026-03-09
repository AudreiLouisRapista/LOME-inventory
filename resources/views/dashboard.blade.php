@extends('themes.main')

@section('title', 'Dashboard')

@section('content')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

        .lome-dashboard {
            font-family: 'Plus Jakarta Sans', sans-serif;
            padding: 30px;
            /* Enhanced outer spacing */
            color: #1e293b;
        }

        /* --- Header Spacing --- */
        .dash-header {
            margin-bottom: 35px;
            padding-left: 10px;
        }

        .dash-header h1 {
            font-size: 28px;
            font-weight: 800;
            color: #0f172a;
            margin: 0;
        }

        .dash-header p {
            font-size: 14px;
            color: #64748b;
            margin-top: 4px;
        }

        /* --- Spaced Grid --- */
        .dashboard-row {
            margin-bottom: 30px;
            /* Vertical gap between sections */
        }

        /* --- Stat Cards (Soft Rounded) --- */
        .stat-card-modern {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            padding: 24px;
            display: flex;
            align-items: center;
            gap: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
            height: 110px;
        }

        .pill-icon {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
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
        <div class="dash-header">
            <h1>Dashboard</h1>
            <p>Over View</p>
        </div>

        <div class="row dashboard-row">
            <div class="col-md-3">
                <div class="stat-card-modern">
                    <div class="pill-icon pill-green"><i class="fas fa-box"></i></div>
                    <div class="stat-info">
                        <div class="stat-val">5,483</div>
                        <div class="stat-label">Total Products</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card-modern">
                    <div class="pill-icon pill-blue"><i class="fas fa-shopping-cart"></i></div>
                    <div class="stat-info">
                        <div class="stat-val">2,859</div>
                        <div class="stat-label">Orders</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card-modern">
                    <div class="pill-icon pill-purple"><i class="fas fa-chart-line"></i></div>
                    <div class="stat-info">
                        <div class="stat-val">5,483</div>
                        <div class="stat-label">Total Stock</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card-modern">
                    <div class="pill-icon pill-orange"><i class="fas fa-exclamation-triangle"></i></div>
                    <div class="stat-info">
                        <div class="stat-val">38</div>
                        <div class="stat-label">Out of Stock</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row dashboard-row">
            <div class="col-lg-7">
                <div class="content-card">
                    <div class="card-head">
                        <h3>Expense vs Profit</h3>
                    </div>
                    <div style="height: 300px;"><canvas id="expenseChart"></canvas></div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="content-card">
                    <div class="card-head">
                        <h3>Top Sales Product</h3>
                    </div>
                    @php
                        $prods = [
                            ['Wireless Mouse', 92],
                            ['USB-C Cable', 85],
                            ['Laptop Stand', 78],
                            ['Mechanical Keyboard', 71],
                            ['Desk Lamp', 65],
                        ];
                    @endphp
                    @foreach ($prods as $p)
                        <div class="sales-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="small font-weight-bold">{{ $p[0] }}</span>
                                <span class="small text-muted">{{ $p[1] }}%</span>
                            </div>
                            <div class="progress-track">
                                <div class="progress-fill" style="width: {{ $p[1] }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="row dashboard-row">
            <div class="col-lg-8">
                <div class="content-card">
                    <div class="card-head">
                        <h3>Monthly Inventory vs Sales</h3>
                    </div>
                    <div style="height: 280px;"><canvas id="comboChart"></canvas></div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="content-card">
                    <div class="card-head">
                        <h3>Expiration Center</h3>
                    </div>
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <div class="pill-icon pill-orange" style="width: 60px; height: 60px;"><i
                                class="fas fa-hourglass-half"></i></div>
                        <div>
                            <h2 class="m-0" style="font-size: 32px; font-weight: 800;">127</h2>
                            <span class="small text-muted">Items Near Expiry</span>
                        </div>
                    </div>

                    <button class="exp-btn" data-toggle="modal" data-target="#crit7Modal">
                        <span class="small font-weight-bold">Critical (7 Days)</span>
                        <span class="text-crit">23 items <i class="fas fa-chevron-right ml-2"></i></span>
                    </button>

                    <button class="exp-btn" data-toggle="modal" data-target="#warn30Modal">
                        <span class="small font-weight-bold">Warning (30 Days)</span>
                        <span class="text-warn">104 items <i class="fas fa-chevron-right ml-2"></i></span>
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
                    <div class="table-responsive">
                        <table class="table">
                            <thead class="text-muted small">
                                <tr>
                                    <th>PRODUCT</th>
                                    <th>SKU</th>
                                    <th>EXPIRY</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Sample Item A</td>
                                    <td>#12345</td>
                                    <td class="text-danger font-weight-bold">Mar 14, 2026</td>
                                </tr>
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
                    <p class="text-muted">Filtering products expiring within 30 days...</p>
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
