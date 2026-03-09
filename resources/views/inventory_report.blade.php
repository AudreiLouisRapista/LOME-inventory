@extends('themes.main')

{{-- 1. DEFINE PAGE TITLE --}}
@section('title', 'POS History')



@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h4 class="fw-bold mb-0 text-dark">Inventory Asset Report</h4>
                <p class="text-muted small">Current stock valuation and replenishment tracking</p>
            </div>
            <div class="btn-group shadow-sm">
                <button class="btn btn-white border"><i class="bi bi-file-earmark-excel me-2"></i>Excel</button>
                <button class="btn btn-danger"><i class="bi bi-file-earmark-pdf me-2"></i>Export PDF</button>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm border-bottom border-primary border-3">
                    <div class="card-body">
                        <p class="text-muted small fw-bold mb-1">TOTAL STOCK VALUE</p>
                        <h4 class="fw-bold">₱450,230.00</h4>
                        <small class="text-success"><i class="bi bi-arrow-up"></i> 5% from last month</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm border-bottom border-warning border-3">
                    <div class="card-body">
                        <p class="text-muted small fw-bold mb-1">OUT OF STOCK</p>
                        <h4 class="fw-bold text-danger">8 Items</h4>
                        <small class="text-muted">Requires immediate order</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm border-bottom border-info border-3">
                    <div class="card-body">
                        <p class="text-muted small fw-bold mb-1">TOTAL CATEGORIES</p>
                        <h4 class="fw-bold">24</h4>
                        <small class="text-muted">Active in inventory</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm border-bottom border-success border-3">
                    <div class="card-body">
                        <p class="text-muted small fw-bold mb-1">STOCK TURNOVER</p>
                        <h4 class="fw-bold">4.2x</h4>
                        <small class="text-muted">Annualized rate</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-md-5">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3 border-0">
                        <h6 class="fw-bold mb-0">Stock by Category</h6>
                    </div>
                    <div class="card-body" style="height: 350px;">
                        <canvas id="inventoryPieChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-md-7">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                        <h6 class="fw-bold mb-0">Stock Replenishment Status</h6>
                        <span class="badge bg-light text-dark border">Auto-refreshed</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="bg-light small">
                                <tr>
                                    <th class="ps-3">Product</th>
                                    <th>On Hand</th>
                                    <th>Safety Stock</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="ps-3 fw-bold">Surf Powder (1kg)</td>
                                    <td>15</td>
                                    <td>20</td>
                                    <td><span class="badge bg-warning-subtle text-warning px-2">Low Stock</span></td>
                                </tr>
                                <tr>
                                    <td class="ps-3 fw-bold">Lucky Me Noodles</td>
                                    <td>5</td>
                                    <td>50</td>
                                    <td><span class="badge bg-danger-subtle text-danger px-2">Critical</span></td>
                                </tr>
                                <tr>
                                    <td class="ps-3 fw-bold">Milo Sachet</td>
                                    <td>450</td>
                                    <td>100</td>
                                    <td><span class="badge bg-success-subtle text-success px-2">Healthy</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

<script>
    var invCtx = document.getElementById('inventoryPieChart').getContext('2d');
    new Chart(invCtx, {
        type: 'doughnut',
        data: {
            labels: ['Beverages', 'Laundry', 'Snacks', 'Canned Goods'],
            datasets: [{
                data: [40, 25, 20, 15],
                backgroundColor: ['#0d6efd', '#6610f2', '#ffc107', '#198754'],
                hoverOffset: 4
            }]
        },
        options: {
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
</script>
