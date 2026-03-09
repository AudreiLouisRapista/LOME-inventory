@extends('themes.main')

{{-- 1. DEFINE PAGE TITLE --}}
@section('title', 'Stock Movement Ledger')


@section('content')
    <link rel="stylesheet" href="{{ asset('css/pages/stockMovement.css') }}">
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-md-6">
                <h3 class="fw-bold text-dark"><i class="bi bi-arrow-left-right me-2 text-primary"></i>Stock Movements</h3>
                <p class="text-muted">Inventory Ledger for Lome Shop Mart</p>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm custom-stat-card">
                    <div class="card-body d-flex align-items-center">
                        <div class="stat-icon-box bg-soft-success">
                            <i class="bi bi-arrow-down-left"></i>
                        </div>
                        <div class="ms-3">
                            <p class="stat-label">Total Inbound</p>
                            <h4 class="stat-value">+{{ number_format($recentIn ?? 0) }} Units</h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-0 shadow-sm custom-stat-card">
                    <div class="card-body d-flex align-items-center">
                        <div class="stat-icon-box bg-soft-danger">
                            <i class="bi bi-arrow-up-right"></i>
                        </div>
                        <div class="ms-3">
                            <p class="stat-label">Total Outbound</p>
                            <h4 class="stat-value">-{{ number_format($recentOut ?? 0) }} Units</h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-0 shadow-sm custom-stat-card">
                    <div class="card-body d-flex align-items-center">
                        <div class="stat-icon-box bg-soft-primary">
                            <i class="bi bi-arrow-repeat"></i>
                        </div>
                        <div class="ms-3">
                            <p class="stat-label">Transfers</p>
                            <h4 class="stat-value">90</h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-0 shadow-sm custom-stat-card">
                    <div class="card-body d-flex align-items-center">
                        <div class="stat-icon-box bg-soft-warning">
                            <i class="bi bi-box-seam"></i>
                        </div>
                        <div class="ms-3">
                            <p class="stat-label">Adjustments</p>
                            <h4 class="stat-value">5</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <div class="row align-items-center">
                    <div class="col">
                        <h6 class="mb-0 fw-bold">Movement Logs</h6>
                    </div>
                    <div class="col-md-7 d-flex justify-content-md-end gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-funnel text-muted"></i>
                            <select class="form-select border-light shadow-sm" id="typeFilter" style="min-width: 150px;">
                                <option value="">All Types</option>
                                <option value="IN">Inbound</option>
                                <option value="OUT">Outbound</option>
                            </select>
                        </div>
                        <button class="btn btn-success px-4 shadow-sm">
                            <i class="bi bi-download me-2"></i> Export
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="stockMovementTable" class="table table-hover align-middle" style="width:100%">
                            <thead class="table-light">
                                <tr>
                                    <th>Date/Time</th>
                                    <th>Reference</th>
                                    <th>Product Name</th>
                                    <th>Type</th>
                                    <th>Batch Quantity</th>
                                    <th class="text-end">Qty</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($movements as $move)
                                    <tr>
                                        <td>
                                            <div class="fw-semibold text-dark" style="font-size: 0.9rem;">
                                                {{ \Carbon\Carbon::parse($move->created_at)->format('Y-m-d') }}
                                            </div>
                                            <div class="text-muted" style="font-size: 0.8rem;">
                                                {{ \Carbon\Carbon::parse($move->created_at)->format('h:i A') }}
                                            </div>
                                        </td>
                                        <td><span
                                                class="badge bg-secondary-subtle text-dark border">{{ $move->invoice_number }}</span>
                                        </td>
                                        <td class="fw-bold">{{ $move->product_name }}</td>
                                        <td>
                                            @if ($move->MovementType == 'IN')
                                                <span class="badge-columns bg-soft-success-column">
                                                    <i class="bi bi-arrow-down-left"></i> Inbound
                                                </span>
                                            @else
                                                <span class="badge-columns bg-soft-danger-column">
                                                    <i class="bi bi-arrow-up-right"></i> Outbound
                                                </span>
                                            @endif
                                        </td>
                                        <td>{{ $move->batch_quantity }}</td>
                                        <td
                                            class="text-end fw-bold {{ $move->MovementType == 'IN' ? 'text-success' : 'text-danger' }}">
                                            {{ $move->MovementType == 'IN' ? '+' : '-' }}{{ $move->batch_quantity }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>


    @endsection

    @section('scripts src')
        <script>
            $(document).ready(function() {
                var table = $('#stockMovementTable').DataTable({
                    responsive: true,
                    order: [
                        [0, 'desc']
                    ], // Default sort by Date (Newest first)
                    lengthChange: false,
                    dom: '<"d-flex justify-content-between align-items-center mb-3"lf>rt<"d-flex justify-content-between align-items-center mt-3"ip>',
                    language: {
                        search: "_INPUT_",
                        searchPlaceholder: "Search products or reference..."
                    }
                });

                // Custom "Type" Filter Logic
                $('#typeFilter').on('change', function() {
                    var val = $(this).val();
                    // Assume Type is in the 5th column (index 4)
                    table.column(3).search(val).draw();
                });
            });
        </script>

    @endsection
