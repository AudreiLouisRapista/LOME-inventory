@extends('themes.main')

@section('title', 'Dashboard')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/pages/dashboard_style.css') }}">

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
                        <div class="stat-val">{{ number_format($totalProducts ?? 0) }}</div>
                        <div class="stat-label">Total Products</div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="stat-card-modern">
                    <div class="pill-icon pill-purple"><i class="fas fa-chart-line"></i></div>
                    <div class="stat-info">
                        <div class="stat-val">{{ number_format($totalQuantity ?? 0) }}</div>
                        <div class="stat-label">Total Stock</div>
                    </div>
                    
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card-modern">
                    <div class="pill-icon pill-orange"><i class="fas fa-exclamation-triangle"></i></div>
                    <div class="stat-info">
                        <div class="stat-val">{{ number_format($outOfStock ?? 0) }}</div>
                        <div class="stat-label">Out of Stock</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card-modern">
                    <div class="pill-icon pill-blue"><i class="fas fa-file-invoice-dollar" style="color: red"></i></div>
                    <div class="stat-info">
                        <div class="stat-val" style="color: red">₱{{ number_format($unpaidInvoiceTotal ?? 0, 2) }}</div>
                        <div class="stat-label">Invoice Balance</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row dashboard-row">
            <div class="col-lg-7">
                <div class="content-card">
                    <div class="card-head">
                        <h3>Expense vs Revenue</h3>
                    </div>
                    <div style="height: 300px;"><canvas id="expenseChart"></canvas></div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="content-card">
                    <div class="card-head">
                        <h3>Top Sales Product</h3>
                    </div>
                    <div id="topSalesList">
                        @forelse (($topSalesProducts ?? []) as $p)
                            <div class="sales-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="small font-weight-bold">{{ $p['name'] }}</span>
                                    <span class="small text-muted">{{ $p['percent'] }}%</span>
                                </div>
                                <div class="progress-track">
                                    <div class="progress-fill" style="width: {{ $p['percent'] }}%"></div>
                                </div>
                            </div>
                        @empty
                            <div class="text-muted small">No sales data.</div>
                        @endforelse
                    </div>
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
                            <h2 class="m-0" style="font-size: 32px; font-weight: 800;">{{ $itemsNearExpiry ?? 0 }}</h2>
                            <span class="small text-muted">Items Near Expiry</span>
                        </div>
                    </div>

                    <button class="exp-btn" data-toggle="modal" data-target="#crit7Modal">
                        <span class="small font-weight-bold">Critical (7 Days)</span>
                        <span class="text-crit">{{ $criticalExpiryCount ?? 0 }} items <i class="fas fa-chevron-right ml-2"></i></span>
                    </button>

                    <button class="exp-btn" data-toggle="modal" data-target="#warn30Modal">
                        <span class="small font-weight-bold">Warning (30 Days)</span>
                        <span class="text-warn">{{ $warningExpiryCount ?? 0 }} items <i class="fas fa-chevron-right ml-2"></i></span>
                    </button>
                </div>
            </div>

            <div class="col py-4 bg-dashboard">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-4"><i class="bi bi-exclamation-triangle text-warning me-2"></i>Reorder
                            Required</h6>

                        @forelse(($reorderRequired ?? []) as $item)
                            @php
                                $isUrgent = ($item['level'] ?? '') === 'urgent';
                                $badgeClass = $isUrgent ? 'bg-danger' : 'bg-warning';
                                $itemBgClass = $isUrgent ? 'bg-light-danger' : 'bg-light-warning';
                                $label = $isUrgent ? 'Urgent' : 'Soon';
                                $percent = (int) ($item['percent'] ?? 0);
                            @endphp
                            <div class="reorder-item p-3 rounded-4 mb-3 border {{ $itemBgClass }}">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="fw-bold">{{ $item['name'] ?? 'Unknown' }}</span>
                                    <span class="badge {{ $badgeClass }} rounded-pill">{{ $label }}</span>
                                </div>
                                <p class="small text-muted mb-2">Current: {{ $item['current'] ?? 0 }} | Reorder: {{ $item['reorder_to'] ?? 0 }}</p>
                                <div class="progress rounded-pill" style="height: 6px;">
                                    <div class="progress-bar {{ $badgeClass }}" style="width: {{ $percent }}%"></div>
                                </div>
                            </div>
                        @empty
                            <div class="text-muted small">No items need reordering.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="py-4 bg-dashboard">
                <div class="col-xl-12">
                    <div class="card main-card border-0 shadow-lg rounded-5 overflow-hidden">
                        <div class="card-body p-4">

                            <div class="row align-items-center mb-4">
                                <div class="col-md-7">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="icon-gradient me-3">
                                            <i class="bi bi-bar-chart-line-fill text-white"></i>
                                        </div>
                                        <h2 class="fw-bold text-navy mb-0">Sales Amount by Product</h2>
                                    </div>
                                    <p class="text-muted ms-5 ps-2 mb-0">Performance analysis across top 10 products</p>
                                </div>

                                <div class="col-md-5 d-flex justify-content-end align-items-center gap-3">
                                    <div class="stat-badge shadow-sm">
                                        <span class="label">Total Sales</span>
                                        <span id="totalSumBadge" class="value text-purple">{{ $totalSum }}</span>
                                    </div>
                                    <div class="stat-badge shadow-sm border-success-subtle">
                                        <span class="label text-success">Average</span>
                                        <span id="totalAvgBadge" class="value text-success">{{ $totalAverages }}</span>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <select id="dateFilter" name="date_filter"
                                            class="form-select form-select-sm border-0 shadow-sm bg-light">
                                            <option value="all">All Records</option>
                                            @foreach ($availableDates as $d)
                                                <option value="{{ $d->date }}"
                                                    {{ $filter == $d->date ? 'selected' : '' }}>
                                                    {{ \Carbon\Carbon::parse($d->date)->format('M d, Y') }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="chart-container mb-4" style="position: relative; height:400px; width:100%">
                                <canvas id="posSalesChart"></canvas>
                            </div>

                            <div class="d-flex justify-content-between align-items-center pt-3 border-top border-light">
                                <div class="d-flex align-items-center">
                                    <div class="legend-dot me-2"
                                        style="width: 10px; height: 10px; border-radius: 50%; background-color: #8a3ffc;">
                                    </div>
                                    <span class="text-muted fw-semibold small">Sales Amount</span>
                                </div>

                                <div class="top-seller-pill px-4 py-2" style="background: #f3f0ff; border-radius: 50px;">
                                    <span class="text-purple-dark fw-bold small">
                                        Top Seller:
                                        <span id="topSellerName"
                                            class="text-navy">{{ $bestSellerName ?? 'No Sales' }}</span>
                                        <span id="topSellerValue" class="text-muted ms-1">
                                            ({{ $bestSellerRecord ? '₱' . number_format($bestSellerRecord->TotalSalesPerQty / 1000, 1) : 'No Sales' }})
                                        </span>
                                    </span>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
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
                                @forelse(($criticalExpiryItems ?? []) as $row)
                                    <tr>
                                        <td>{{ $row->product_name ?? 'Unknown' }}</td>
                                        <td>#{{ $row->product_ID ?? '' }}</td>
                                        <td class="text-danger font-weight-bold">
                                            {{ $row->expiration_date ? \Carbon\Carbon::parse($row->expiration_date)->format('M d, Y') : 'N/A' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-muted small">No critical expirations.</td>
                                    </tr>
                                @endforelse
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
                                @forelse(($warningExpiryItems ?? []) as $row)
                                    <tr>
                                        <td>{{ $row->product_name ?? 'Unknown' }}</td>
                                        <td>#{{ $row->product_ID ?? '' }}</td>
                                        <td class="text-warning font-weight-bold">
                                            {{ $row->expiration_date ? \Carbon\Carbon::parse($row->expiration_date)->format('M d, Y') : 'N/A' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-muted small">No items expiring in the next 30 days.</td>
                                    </tr>
                                @endforelse
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
                    labels: {!! json_encode($expenseProfitLabels ?? []) !!},
                    datasets: [{
                            label: 'Revenue',
                            data: {!! json_encode($revenueSeries ?? []) !!},
                            borderColor: '#6366f1',
                            backgroundColor: 'rgba(99, 102, 241, 0.05)',
                            fill: true,
                            tension: 0.4,
                            pointRadius: 5,
                            pointBackgroundColor: '#fff',
                            pointBorderWidth: 2
                        },
                        {
                            label: 'Expense',
                            data: {!! json_encode($expenseSeries ?? []) !!},
                            borderColor: '#ef4444', // Professional Red
                            backgroundColor: 'transparent', // Keep it clean to avoid overlap
                            fill: false,
                            tension: 0.4,
                            pointRadius: 5,
                            pointBackgroundColor: '#fff',
                            pointBorderWidth: 2
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                font: {
                                    family: 'Plus Jakarta Sans',
                                    size: 12
                                }
                            }
                        }
                    },
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
                    labels: {!! json_encode($inventorySalesLabels ?? []) !!},
                    datasets: [{
                        type: 'bar',
                        label: 'Items Sold',
                        data: {!! json_encode($itemsSoldSeries ?? []) !!},
                        backgroundColor: '#6366f1',
                        borderRadius: 8,
                        barThickness: 22
                    }, {
                        type: 'line',
                        label: 'Stock Level',
                        data: {!! json_encode($stockLevelSeries ?? []) !!},
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

            // Sales Amount by Product (The Wave Chart)
            const posSalesChart = new Chart(document.getElementById('posSalesChart'), {
                type: 'line',
                data: {
                    labels: {!! json_encode($labels) !!},
                    datasets: [{
                        label: 'Sales Amount',
                        data: {!! json_encode($values) !!},
                        borderColor: '#8a3ffc',
                        borderWidth: 4,
                        fill: true,
                        backgroundColor: function(context) {
                            const chart = context.chart;
                            const {
                                ctx,
                                chartArea
                            } = chart;
                            if (!chartArea) return null;
                            const gradient = ctx.createLinearGradient(0, chartArea.top, 0,
                                chartArea.bottom);
                            gradient.addColorStop(0, 'rgba(138, 63, 252, 0.2)');
                            gradient.addColorStop(1, 'rgba(138, 63, 252, 0)');
                            return gradient;
                        },
                        tension: 0.45,
                        pointRadius: 6,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#8a3ffc',
                        pointBorderWidth: 3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                borderDash: [5, 5],
                                color: '#e2e8f0',
                                drawBorder: false
                            },
                            ticks: {
                                callback: (value) => (value >= 1000 ? (value / 1000) : value),
                                font: {
                                    family: 'Plus Jakarta Sans',
                                    size: 12
                                }
                            }

                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    family: "'Plus Jakarta Sans', sans-serif",
                                    size: 10
                                }
                            }
                        }

                    }
                }
            });

            // 2. The AJAX Filter Logic
            const dateFilter = document.getElementById('dateFilter');

            if (dateFilter) {
                dateFilter.addEventListener('change', function() {
                    let filterValue = this.value;

                    // Trigger the AJAX request to your DashboardController
                    fetch(`{{ route('admin.dashboard') }}?created_at=${filterValue}`, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest' // Tells Laravel it's an AJAX call
                            }
                        })
                        .then(response => {
                            if (!response.ok) throw new Error('Network response was not ok');
                            return response.json();
                        })
                        .then(data => {
                            // 1. Update the Chart
                            posSalesChart.data.labels = data.labels;
                            posSalesChart.data.datasets[0].data = data.values;
                            posSalesChart.update();

                            // 2. Fix the IDs to match your HTML exactly
                            if (document.getElementById('totalSumBadge')) {
                                document.getElementById('totalSumBadge').innerText = data.totalSum;
                            }

                            if (document.getElementById('totalAvgBadge')) {
                                document.getElementById('totalAvgBadge').innerText = data.totalAverages;
                            }

                            if (document.getElementById('topSellerName')) {
                                document.getElementById('topSellerName').innerText = data
                                    .bestSellerName;
                            }

                            // 3. Update Top Sales Product list
                            const topSalesList = document.getElementById('topSalesList');
                            if (topSalesList && Array.isArray(data.topSalesProducts)) {
                                topSalesList.innerHTML = '';

                                if (data.topSalesProducts.length === 0) {
                                    const empty = document.createElement('div');
                                    empty.className = 'text-muted small';
                                    empty.textContent = 'No sales data.';
                                    topSalesList.appendChild(empty);
                                } else {
                                    data.topSalesProducts.forEach((p) => {
                                        const item = document.createElement('div');
                                        item.className = 'sales-item';

                                        const header = document.createElement('div');
                                        header.className = 'd-flex justify-content-between align-items-center';

                                        const name = document.createElement('span');
                                        name.className = 'small font-weight-bold';
                                        name.textContent = p.name ?? '';

                                        const pct = document.createElement('span');
                                        pct.className = 'small text-muted';
                                        pct.textContent = `${p.percent ?? 0}%`;

                                        header.appendChild(name);
                                        header.appendChild(pct);

                                        const track = document.createElement('div');
                                        track.className = 'progress-track';

                                        const fill = document.createElement('div');
                                        fill.className = 'progress-fill';
                                        fill.style.width = `${p.percent ?? 0}%`;

                                        track.appendChild(fill);

                                        item.appendChild(header);
                                        item.appendChild(track);

                                        topSalesList.appendChild(item);
                                    });
                                }
                            }
                        })
                        .catch(error => console.error('Error fetching filtered data:', error));
                });
            }
        });
    </script>
@endsection
