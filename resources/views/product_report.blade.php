@extends('themes.main')

{{-- 1. DEFINE PAGE TITLE --}}
@section('title', 'POS History')



@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h4 class="fw-bold mb-0 text-dark">Product Performance Report</h4>
                <p class="text-muted small">Analyze sales trends and stock movement</p>
            </div>
            <div class="d-flex gap-2">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-calendar3"></i></span>
                    <input type="date" class="form-control border-start-0 shadow-none" id="reportStartDate">
                </div>
                <button class="btn btn-dark shadow-sm px-4">
                    <i class="bi bi-printer me-2"></i>Export PDF
                </button>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm p-3">
                    <div class="d-flex align-items-center">
                        <div class="p-3 bg-primary bg-opacity-10 rounded-3 me-3">
                            <i class="bi bi-graph-up-arrow text-primary fs-4"></i>
                        </div>
                        <div>
                            <p class="text-muted small fw-bold mb-0">TOTAL REVENUE</p>
                            <h4 class="fw-bold mb-0">₱124,500.00</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm p-3">
                    <div class="d-flex align-items-center">
                        <div class="p-3 bg-success bg-opacity-10 rounded-3 me-3">
                            <i class="bi bi-cart-check text-success fs-4"></i>
                        </div>
                        <div>
                            <p class="text-muted small fw-bold mb-0">ITEMS SOLD</p>
                            <h4 class="fw-bold mb-0">856 Units</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm p-3">
                    <div class="d-flex align-items-center">
                        <div class="p-3 bg-warning bg-opacity-10 rounded-3 me-3">
                            <i class="bi bi-exclamation-triangle text-warning fs-4"></i>
                        </div>
                        <div>
                            <p class="text-muted small fw-bold mb-0">LOW STOCK ITEMS</p>
                            <h4 class="fw-bold mb-0">12 Products</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-md-8">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white py-3 border-0">
                        <h6 class="fw-bold mb-0">Sales Analytics</h6>
                    </div>
                    <div class="card-body">
                        <canvas id="reportChart" style="height: 300px;"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white py-3 border-0">
                        <h6 class="fw-bold mb-0">Top Performing Items</h6>
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            <li
                                class="list-group-item d-flex justify-content-between align-items-center border-0 px-4 py-3">
                                <div>
                                    <span class="fw-bold d-block">Ariel Power Gel</span>
                                    <small class="text-muted">Laundry Care</small>
                                </div>
                                <span class="badge bg-primary rounded-pill">120 Sold</span>
                            </li>
                            <li
                                class="list-group-item d-flex justify-content-between align-items-center border-0 px-4 py-3">
                                <div>
                                    <span class="fw-bold d-block">Coca-Cola 1.5L</span>
                                    <small class="text-muted">Beverages</small>
                                </div>
                                <span class="badge bg-primary rounded-pill">98 Sold</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
<script>
    var reportCtx = document.getElementById('reportChart').getContext('2d');
    new Chart(reportCtx, {
        type: 'line',
        data: {
            labels: ['Feb 13', 'Feb 14', 'Feb 15', 'Feb 16', 'Feb 17', 'Feb 18', 'Feb 19'],
            datasets: [{
                label: 'Sales Revenue',
                data: [12000, 19000, 3000, 5000, 20000, 23000, 15000],
                borderColor: '#0d6efd',
                fill: true,
                backgroundColor: 'rgba(13, 110, 253, 0.1)',
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
                    beginAtZero: true
                }
            }
        }
    });
</script>
