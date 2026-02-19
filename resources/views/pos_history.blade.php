@extends('themes.main')

{{-- 1. DEFINE PAGE TITLE --}}
@section('title', 'POS History')



@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h4 class="fw-bold mb-0">POS Import History</h4>
                <p class="text-muted small">Track and locate previously imported POS data files</p>
            </div>
            <button class="btn btn-primary shadow-sm">
                <i class="bi bi-file-earmark-arrow-up me-2"></i>New Import
            </button>
        </div>

        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm border-start border-primary border-4">
                    <div class="card-body">
                        <p class="text-muted small fw-bold mb-1">TOTAL IMPORTS</p>
                        <h5 class="mb-0">1,248</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm border-start border-success border-4">
                    <div class="card-body">
                        <p class="text-muted small fw-bold mb-1">SUCCESSFUL</p>
                        <h5 class="mb-0">1,245</h5>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <div class="d-flex align-items-center justify-content-between">
                    <h6 class="mb-0 fw-bold text-secondary">Import Logs</h6>
                    <div class="input-group" style="width: 300px;">
                        <span class="input-group-text bg-light border-0"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control bg-light border-0" placeholder="Search file name...">
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-secondary small fw-bold">
                        <tr>
                            <th class="ps-4">FILE NAME</th>
                            <th>IMPORT DATE</th>
                            <th>RECORDS</th>
                            <th>USER</th>
                            <th>STATUS</th>
                            <th class="text-end pe-4">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-filetype-csv fs-4 text-success me-3"></i>
                                    <div>
                                        <span class="fw-bold d-block">POS_Sales_Feb_2026.csv</span>
                                        <span class="text-muted extra-small">2.4 MB</span>
                                    </div>
                                </div>
                            </td>
                            <td>Feb 19, 2026 <br><small class="text-muted">10:45 AM</small></td>
                            <td>450 Rows</td>
                            <td>Admin_John</td>
                            <td><span class="badge bg-success-subtle text-success border border-success">Completed</span>
                            </td>
                            <td class="text-end pe-4">
                                <button class="btn btn-sm btn-outline-primary" title="Locate Data">
                                    <i class="bi bi-geo-alt-fill"></i> Locate
                                </button>
                                <button class="btn btn-sm btn-light border ml-1">
                                    <i class="bi bi-download"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-filetype-xlsx fs-4 text-primary me-3"></i>
                                    <div>
                                        <span class="fw-bold d-block">Inventory_Sync_Batch_A.xlsx</span>
                                        <span class="text-muted extra-small">1.8 MB</span>
                                    </div>
                                </div>
                            </td>
                            <td>Feb 18, 2026 <br><small class="text-muted">03:20 PM</small></td>
                            <td>1,200 Rows</td>
                            <td>Admin_Jane</td>
                            <td><span class="badge bg-success-subtle text-success border border-success">Completed</span>
                            </td>
                            <td class="text-end pe-4">
                                <button class="btn btn-sm btn-outline-primary"><i class="bi bi-geo-alt-fill"></i>
                                    Locate</button>
                                <button class="btn btn-sm btn-light border ml-1"><i class="bi bi-download"></i></button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white py-3 border-0">
                <nav>
                    <ul class="pagination pagination-sm justify-content-end mb-0">
                        <li class="page-item disabled"><a class="page-link" href="#">Previous</a></li>
                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                        <li class="page-item"><a class="page-link" href="#">Next</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
@endsection

<style>
    .extra-small {
        font-size: 0.75rem;
    }

    .bg-success-subtle {
        background-color: #e8f5e9;
    }

    .table thead th {
        letter-spacing: 0.5px;
        border-top: none;
    }

    .card {
        border-radius: 12px;
    }

    .input-group-text {
        border-radius: 8px 0 0 8px !important;
    }

    .form-control {
        border-radius: 0 8px 8px 0 !important;
    }
</style>
